<?php
/**
 * Fix Admin Password
 * Reset the admin password to a properly hashed version
 */

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        // Hash the password properly
        $password = 'admin123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Update admin user password
        $query = "UPDATE users SET password = :password WHERE email = 'admin@safekeep.com'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>✅ Admin password updated successfully!</div>";
            
            // Test the login now
            require_once 'classes/User.php';
            $user = new User($db);
            $user->email = 'admin@safekeep.com';
            $user->password = 'admin123';
            
            if ($user->login()) {
                echo "<div class='alert alert-success'>✅ Admin login test now works!</div>";
                echo "<p><strong>Admin Details:</strong></p>";
                echo "<ul>";
                echo "<li>ID: " . $user->id . "</li>";
                echo "<li>Username: " . $user->username . "</li>";
                echo "<li>Email: " . $user->email . "</li>";
                echo "<li>Role: " . $user->role . "</li>";
                echo "</ul>";
            } else {
                echo "<div class='alert alert-danger'>❌ Login still failing after password update</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>❌ Failed to update admin password</div>";
        }
        
    } else {
        echo "<div class='alert alert-danger'>❌ Database connection failed</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeKeep - Fix Admin Password</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Admin Password Fix</h4>
                    </div>
                    <div class="card-body">
                        <?php // Results displayed above ?>
                        
                        <div class="mt-4">
                            <h6>Next Steps:</h6>
                            <div class="btn-group" role="group">
                                <a href="login.php" class="btn btn-primary">Try PHP Login</a>
                                <a href="login.html" class="btn btn-secondary">Try JavaScript Login</a>
                                <a href="debug.php" class="btn btn-info">Run Debug Again</a>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <h6>Test Credentials:</h6>
                                <p><strong>Email:</strong> admin@safekeep.com<br>
                                <strong>Password:</strong> admin123</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
