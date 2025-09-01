<?php
/**
 * Session Management
 * Handle user sessions and authentication checks
 */

class SessionManager {
    
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($user_data) {
        self::startSession();
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['email'] = $user_data['email'];
        $_SESSION['role'] = $user_data['role'];
        $_SESSION['logged_in'] = true;
    }

    public static function logout() {
        self::startSession();
        session_unset();
        session_destroy();
    }

    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: login.html');
            exit();
        }
    }

    public static function getUserId() {
        self::startSession();
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public static function getUsername() {
        self::startSession();
        return isset($_SESSION['username']) ? $_SESSION['username'] : null;
    }

    public static function getUserRole() {
        self::startSession();
        return isset($_SESSION['role']) ? $_SESSION['role'] : null;
    }

    public static function isAdmin() {
        return self::getUserRole() === 'admin';
    }

    public static function isStudent() {
        return self::getUserRole() === 'student';
    }

    public static function isStaff() {
        return self::getUserRole() === 'staff';
    }
}
?>
