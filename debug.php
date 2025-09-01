<?php
/**
 * Debug page to check database status and users
 */

require_once 'config/database.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeKeep - Debug Info</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">SafeKeep Debug Information</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        try {
                            $database = new Database();
                            $db = $database->getConnection();
                            
                            if ($db) {
                                echo '<div class="alert alert-success">';
                                echo '<h5>✅ Database Connection: OK</h5>';
                                echo '</div>';
                                
                                // Check if tables exist
                                echo '<h6>Tables Status:</h6>';
                                $tables = ['users', 'items', 'categories', 'messages', 'claims'];
                                foreach ($tables as $table) {
                                    try {
                                        $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
                                        $result = $stmt->fetch();
                                        echo "<div class='alert alert-info'>✅ Table '$table': {$result['count']} records</div>";
                                    } catch (Exception $e) {
                                        echo "<div class='alert alert-warning'>❌ Table '$table': Not found or error - {$e->getMessage()}</div>";
                                    }
                                }
                                
                                // Check users
                                echo '<h6>Users in Database:</h6>';
                                try {
                                    $stmt = $db->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
                                    $users = $stmt->fetchAll();
                                    
                                    if (count($users) > 0) {
                                        echo '<table class="table table-striped">';
                                        echo '<thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th></tr></thead>';
                                        echo '<tbody>';
                                        foreach ($users as $user) {
                                            echo "<tr>";
                                            echo "<td>{$user['id']}</td>";
                                            echo "<td>{$user['username']}</td>";
                                            echo "<td>{$user['email']}</td>";
                                            echo "<td><span class='badge bg-primary'>{$user['role']}</span></td>";
                                            echo "<td>{$user['created_at']}</td>";
                                            echo "</tr>";
                                        }
                                        echo '</tbody></table>';
                                    } else {
                                        echo '<div class="alert alert-warning">No users found in database</div>';
                                    }
                                } catch (Exception $e) {
                                    echo '<div class="alert alert-danger">Error reading users: ' . $e->getMessage() . '</div>';
                                }
                                
                                // Test admin login
                                echo '<h6>Testing Admin Login:</h6>';
                                try {
                                    require_once 'classes/User.php';
                                    $user = new User($db);
                                    $user->email = 'admin@safekeep.com';
                                    $user->password = 'admin123';
                                    
                                    if ($user->login()) {
                                        echo '<div class="alert alert-success">✅ Admin login test: SUCCESS</div>';
                                        echo '<div class="alert alert-info">Admin user ID: ' . $user->id . ', Username: ' . $user->username . '</div>';
                                    } else {
                                        echo '<div class="alert alert-danger">❌ Admin login test: FAILED</div>';
                                    }
                                } catch (Exception $e) {
                                    echo '<div class="alert alert-danger">Error testing admin login: ' . $e->getMessage() . '</div>';
                                }
                                
                            } else {
                                echo '<div class="alert alert-danger">';
                                echo '<h5>❌ Database Connection: FAILED</h5>';
                                echo '</div>';
                            }
                            
                        } catch (Exception $e) {
                            echo '<div class="alert alert-danger">';
                            echo '<h5>❌ Error: ' . $e->getMessage() . '</h5>';
                            echo '</div>';
                        }
                        ?>
                        
                        <div class="mt-4">
                            <a href="index.html" class="btn btn-primary">← Back to Home</a>
                            <a href="api_test.php" class="btn btn-info">API Test Page</a>
                            <a href="login.html" class="btn btn-success">Login Page</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
