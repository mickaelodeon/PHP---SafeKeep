<?php
/**
 * Database Configuration
 * SafeKeep - Lost & Found System
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'safekeep_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $pdo;

    /**
     * Get database connection
     */
    public function getConnection() {
        $this->pdo = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->pdo;
    }

    /**
     * Test database connection
     */
    public function testConnection() {
        try {
            $connection = $this->getConnection();
            if ($connection) {
                return true;
            }
        } catch(Exception $e) {
            return false;
        }
        return false;
    }
}
?>
