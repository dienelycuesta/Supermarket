<?php
class Router {
    private $routes = [];
    private $prefix = '';

    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    public function any($path, $handler) {
        $this->addRoute('GET', $path, $handler);
        $this->addRoute('POST', $path, $handler);
    }

    public function group($prefix, $callback) {
        $previousPrefix = $this->prefix;
        $this->prefix .= $prefix;
        $callback($this);
        $this->prefix = $previousPrefix;
    }

    private function addRoute($method, $path, $handler) {
        $fullPath = $this->prefix . $path;
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $fullPath);
        $pattern = '#^' . $pattern . '$#';
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch() {
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $this->callHandler($route['handler'], $params);
            }
        }

        http_response_code(404);
        $controller = new Controller();
        $ref = new ReflectionMethod($controller, 'render');
        $ref->setAccessible(true);
        $ref->invoke($controller, 'errors/404', ['pageTitle' => 'Page Not Found']);
    }

    private function getUri() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = '/supermarket';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . trim($uri, '/');
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    private function callHandler($handler, $params) {
        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$controllerName, $method] = explode('@', $handler);
            if (!class_exists($controllerName)) {
                $paths = [
                    APP_ROOT . '/controllers/admin/' . $controllerName . '.php',
                    APP_ROOT . '/controllers/client/' . $controllerName . '.php',
                    APP_ROOT . '/controllers/auth/' . $controllerName . '.php',
                ];
                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        require_once $path;
                        break;
                    }
                }
            }
            $controller = new $controllerName();
            return call_user_func_array([$controller, $method], array_values($params));
        }
        if (is_callable($handler)) {
            return call_user_func_array($handler, array_values($params));
        }
    }
}
