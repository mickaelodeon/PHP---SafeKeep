<?php
session_start();

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    require_once 'classes/User.php';
    require_once 'includes/session.php';
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $message = '';
    
    if (!empty($email) && !empty($password)) {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                $user = new User($db);
                $user->email = $email;
                $user->password = $password;
                
                if ($user->login()) {
                    // Login successful
                    $user_data = [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'role' => $user->role
                    ];
                    
                    SessionManager::login($user_data);
                    
                    // Redirect to dashboard
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $message = '<div class="alert alert-danger">Invalid email or password</div>';
                }
            } else {
                $message = '<div class="alert alert-danger">Database connection failed</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">Please fill in all fields</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeKeep - Login (PHP)</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.html">
                <img src="assets/safekeeplogo.png" alt="SafeKeep Logo" width="35" height="35" class="me-2">
                <strong>SafeKeep</strong>
            </a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.html">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="form-wrapper">
                        <div class="form-card">
                            <div class="form-header text-center mb-4">
                                <img src="assets/safekeeplogo.png" alt="SafeKeep Logo" width="60" height="60" class="mb-3">
                                <h1 class="form-title">SafeKeep Login</h1>
                                <p class="form-subtitle">Secure School Management System</p>
                            </div>

                            <?php echo $message ?? ''; ?>

                            <form method="POST" class="login-form">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : 'admin@safekeep.com'; ?>" 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           value="admin123" required>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    Login
                                </button>
                            </form>

                            <div class="text-center">
                                <p>Don't have an account? <a href="register.html">Register here</a></p>
                                <p><a href="forgotpassword.html">Forgot Password?</a></p>
                            </div>

                            <div class="mt-4">
                                <h6>Debug Links:</h6>
                                <a href="debug.php" class="btn btn-sm btn-info me-2">Database Debug</a>
                                <a href="login_test.php" class="btn btn-sm btn-warning me-2">API Test</a>
                                <a href="login.html" class="btn btn-sm btn-secondary">JavaScript Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
