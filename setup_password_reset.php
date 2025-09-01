<?php
/**
 * Setup Password Reset System
 * Run this once to create the password_resets table
 */

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Setting up Password Reset System</h2>";
    
    // Create password_resets table
    $sql = "
        CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(64) NOT NULL UNIQUE,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_token (token),
            INDEX idx_email (email),
            INDEX idx_expires (expires_at),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ";
    
    $stmt = $db->prepare($sql);
    $success = $stmt->execute();
    
    if ($success) {
        echo "<p style='color: green;'>✓ Password reset table created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create password reset table</p>";
        print_r($stmt->errorInfo());
    }
    
    // Check if table exists and show structure
    $stmt = $db->prepare("SHOW TABLES LIKE 'password_resets'");
    $stmt->execute();
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "<h3>Password Reset Table Structure:</h3>";
        $stmt = $db->prepare("DESCRIBE password_resets");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "<td>{$column['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h3>Password Reset System Ready!</h3>";
    echo "<p>You can now use the forgot password functionality:</p>";
    echo "<ul>";
    echo "<li><a href='forgotpassword.html'>Test Forgot Password</a></li>";
    echo "<li><a href='login.html'>Go to Login</a></li>";
    echo "<li><a href='register.html'>Test Registration</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
