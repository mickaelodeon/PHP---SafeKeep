<?php
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'includes/session.php';

// Check if user is logged in
if (!SessionManager::isLoggedIn()) {
    header('Location: login.html');
    exit();
}

$user_id = SessionManager::getUserId();
$username = SessionManager::getUsername();
$role = SessionManager::getUserRole();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - SafeKeep</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <img src="assets/safekeeplogo2.png" alt="SafeKeep" height="30">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($role === 'admin'): ?>
                        <!-- Admin Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Admin Panel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browse.php">Browse Items</a>
                        </li>
                    <?php else: ?>
                        <!-- Student Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browse.php">Browse Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="messages.php">Messages</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <span id="username"><?= htmlspecialchars($username) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <?php if ($role === 'admin'): ?>
                            <li><a class="dropdown-item" href="admin.php"><i class="bi bi-shield-check"></i> Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="bi bi-person-circle"></i> User Profile</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Username:</strong></label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($username) ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Role:</strong></label>
                                    <p class="form-control-plaintext text-capitalize">
                                        <?php if ($role === 'admin'): ?>
                                            <span class="badge bg-danger">Administrator</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">User</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Contact Email:</strong></label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($username) ?>@school.edu</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <i class="bi bi-person-circle display-1 text-muted"></i>
                                    <p class="text-muted mt-2">Profile Picture</p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Quick Actions</h5>
                                <div class="d-grid gap-2">
                                    <a href="dashboard.php" class="btn btn-primary">
                                        <i class="bi bi-speedometer2"></i> Go to Dashboard
                                    </a>
                                    <a href="browse.php" class="btn btn-outline-primary">
                                        <i class="bi bi-search"></i> Browse Items
                                    </a>
                                    <a href="messages.php" class="btn btn-outline-success">
                                        <i class="bi bi-chat-dots"></i> View Messages
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Account Stats</h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col">
                                                <h6 class="text-muted">Items Posted</h6>
                                                <h4 id="itemsPosted">-</h4>
                                            </div>
                                            <div class="col">
                                                <h6 class="text-muted">Messages</h6>
                                                <h4 id="messageCount">-</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load user stats
        async function loadStats() {
            try {
                // Get user items count
                const itemsResponse = await fetch('api/get_user_items.php');
                if (itemsResponse.ok) {
                    const itemsData = await itemsResponse.json();
                    document.getElementById('itemsPosted').textContent = itemsData.items?.length || 0;
                }

                // Get conversations count
                const conversationsResponse = await fetch('api/conversations.php');
                if (conversationsResponse.ok) {
                    const conversationsData = await conversationsResponse.json();
                    document.getElementById('messageCount').textContent = conversationsData.conversations?.length || 0;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
                document.getElementById('itemsPosted').textContent = '0';
                document.getElementById('messageCount').textContent = '0';
            }
        }

        // Logout functionality
        document.getElementById('logoutBtn').addEventListener('click', async function(e) {
            e.preventDefault();
            
            try {
                const response = await fetch('api/logout.php', {
                    method: 'POST'
                });
                
                if (response.ok) {
                    window.location.href = 'index.html';
                } else {
                    alert('Logout failed. Please try again.');
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('Logout failed. Please try again.');
            }
        });

        // Load stats on page load
        document.addEventListener('DOMContentLoaded', loadStats);
    </script>
</body>
</html>
