<?php
/**
 * Utility Functions
 * Common helper functions for the application
 */

class Utils {
    
    /**
     * Sanitize input data
     */
    public static function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    /**
     * Validate email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Generate secure random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Send JSON response
     */
    public static function sendJsonResponse($data, $status_code = 200) {
        http_response_code($status_code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Handle file upload
     */
    public static function uploadFile($file, $upload_dir = '../uploads/') {
        // Create upload directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error');
        }

        if ($file['size'] > $max_size) {
            throw new Exception('File size too large (max 5MB)');
        }

        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed');
        }

        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            return $new_filename;
        } else {
            throw new Exception('Failed to move uploaded file');
        }
    }

    /**
     * Format date for display
     */
    public static function formatDate($date, $format = 'M j, Y') {
        return date($format, strtotime($date));
    }

    /**
     * Time ago function
     */
    public static function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        if ($time < 31536000) return floor($time/2592000) . ' months ago';
        
        return floor($time/31536000) . ' years ago';
    }

    /**
     * Generate slug from text
     */
    public static function generateSlug($text) {
        $text = preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
        return strtolower(trim($text, '-'));
    }

    /**
     * Validate CSRF token
     */
    public static function validateCSRF($token) {
        session_start();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate CSRF token
     */
    public static function generateCSRF() {
        session_start();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
?>
