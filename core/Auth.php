<?php
class Auth {
    public static function login($email, $password) {
        $user = User::findBy('email', $email);
        if (!$user) return false;
        if ($user['status'] === 'locked') {
            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                return false;
            }
            User::update($user['id'], ['status' => 'active', 'login_attempts' => 0, 'locked_until' => null]);
            $user['status'] = 'active';
        }
        if ($user['status'] !== 'active') return false;
        if (!password_verify($password, $user['password'])) {
            self::incrementAttempts($user);
            return false;
        }
        User::update($user['id'], ['login_attempts' => 0, 'locked_until' => null, 'last_login' => date('Y-m-d H:i:s')]);
        unset($user['password']);
        $_SESSION['user'] = $user;
        session_regenerate_id(true);
        return true;
    }

    private static function incrementAttempts($user) {
        $attempts = $user['login_attempts'] + 1;
        $data = ['login_attempts' => $attempts];
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $data['status'] = 'locked';
            $data['locked_until'] = date('Y-m-d H:i:s', time() + LOGIN_LOCKOUT_TIME);
        }
        User::update($user['id'], $data);
    }

    public static function logout() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check() {
        return isset($_SESSION['user']);
    }

    public static function user() {
        return $_SESSION['user'] ?? null;
    }

    public static function id() {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function isAdmin() {
        $user = self::user();
        return $user && in_array($user['role'], ['admin', 'manager']);
    }

    public static function isCustomer() {
        $user = self::user();
        return $user && $user['role'] === 'customer';
    }

    public static function hasRole($role) {
        $user = self::user();
        return $user && $user['role'] === $role;
    }
}
