<?php
class CSRF {
    public static function generate() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field() {
        return '<input type="hidden" name="csrf_token" value="' . self::generate() . '">';
    }

    public static function validate($token = null) {
        if ($token === null) {
            $token = $_POST['csrf_token'] ?? '';
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function validateHeader() {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return self::validate($token);
    }
}
