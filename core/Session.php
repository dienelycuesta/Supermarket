<?php
class Session {
    public static function flash($key, $value) {
        $_SESSION['flash'][$key] = $value;
    }

    public static function getFlash($key) {
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    public static function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        unset($_SESSION[$key]);
    }
}
