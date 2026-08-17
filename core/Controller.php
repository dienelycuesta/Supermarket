<?php
class Controller {
    protected function render($view, $data = []) {
        extract($data);
        ob_start();
        $viewFile = APP_ROOT . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Vista no encontrada: {$view}";
            return;
        }
        include $viewFile;
        $content = ob_get_clean();
        if (isset($layout)) {
            $layoutFile = APP_ROOT . '/views/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    protected function json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    protected function requireAuth() {
        if (!Auth::check()) {
            Session::flash('error', 'Inicie sesión para continuar.');
            $this->redirect(url('login'));
        }
    }

    protected function requireAdmin() {
        $this->requireAuth();
        if (!Auth::isAdmin()) {
            http_response_code(403);
            $this->render('errors/403', ['pageTitle' => 'Acceso Denegado']);
            exit;
        }
    }

    protected function requireRole($roles) {
        $this->requireAuth();
        $user = Auth::user();
        if (!in_array($user['role'], (array)$roles)) {
            http_response_code(403);
            $this->render('errors/403', ['pageTitle' => 'Acceso Denegado']);
            exit;
        }
    }

    protected function validateCSRF() {
        $token = $_POST['csrf_token'] ?? '';
        if (!CSRF::validate($token)) {
            Session::flash('error', 'Token de seguridad inválido. Por favor intente de nuevo.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? url(''));
        }
    }

    protected function getPage() {
        return max(1, (int)($_GET['page'] ?? 1));
    }
}
