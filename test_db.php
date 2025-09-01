<?php
/**
 * Database Connection Test
 * Test if database connection is working
 */

require_once 'config/database.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeKeep - Database Test</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">SafeKeep Database Test</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $database = new Database();
                        
                        if ($database->testConnection()) {
                            echo '<div class="alert alert-success">';
                            echo '<h5>✅ Database Connection Successful!</h5>';
                            echo '<p>Your SafeKeep database is ready for use.</p>';
                            echo '</div>';
                            
                            echo '<div class="mt-3">';
                            echo '<h6>Next Steps:</h6>';
                            echo '<ol>';
                            echo '<li>Import the database schema from <code>config/database_schema.sql</code></li>';
                            echo '<li>Access phpMyAdmin at <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></li>';
                            echo '<li>Create the <strong>safekeep_db</strong> database</li>';
                            echo '<li>Import the SQL file to create tables</li>';
                            echo '</ol>';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-danger">';
                            echo '<h5>❌ Database Connection Failed</h5>';
                            echo '<p>Please check the following:</p>';
                            echo '<ul>';
                            echo '<li>MySQL service is running in XAMPP</li>';
                            echo '<li>Database credentials in <code>config/database.php</code></li>';
                            echo '<li>Database <strong>safekeep_db</strong> exists</li>';
                            echo '</ul>';
                            echo '</div>';
                        }
                        ?>
                        
                        <div class="mt-4">
                            <a href="index.html" class="btn btn-primary">← Back to Home</a>
                            <a href="http://localhost/phpmyadmin" target="_blank" class="btn btn-info">Open phpMyAdmin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
