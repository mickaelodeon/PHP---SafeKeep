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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fdbb2d 0%, #ff7b00 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-soft: 0 8px 32px rgba(31, 38, 135, 0.37);
            --shadow-hover: 0 15px 35px rgba(31, 38, 135, 0.2);
            --text-primary: #2d3748;
            --text-secondary: #718096;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--text-primary);
        }

        /* Glass Morphism Navigation */
        .navbar {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: var(--shadow-soft);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--text-primary) !important;
        }

        .nav-link {
            color: var(--text-primary) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            border-radius: 20px;
            padding: 8px 16px !important;
            margin: 0 4px;
        }

        .nav-link:hover {
            color: #667eea !important;
            transform: translateY(-1px);
            background: rgba(102, 126, 234, 0.1);
        }

        .nav-link.active {
            background: var(--primary-gradient) !important;
            color: white !important;
        }

        /* Main Container */
        .main-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-soft);
            margin: 20px 0;
            overflow: hidden;
        }

        /* Profile Header */
        .profile-header {
            background: var(--primary-gradient);
            padding: 40px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 8s infinite linear;
        }

        @keyframes shimmer {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--secondary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 3rem;
            font-weight: 700;
            color: white;
            border: 4px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 1;
        }

        .profile-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .profile-role {
            font-size: 1.2rem;
            opacity: 0.9;
            text-transform: capitalize;
            position: relative;
            z-index: 1;
        }

        /* Glass Cards */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            color: var(--text-primary);
        }

        .card-title i {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-right: 12px;
        }

        /* Form Styles */
        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        /* Modern Buttons */
        .modern-btn {
            background: var(--primary-gradient);
            border: none;
            border-radius: 50px;
            padding: 14px 32px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: white;
        }

        .modern-btn.danger {
            background: var(--secondary-gradient);
        }

        .modern-btn.success {
            background: var(--success-gradient);
        }

        /* Info Cards */
        .info-item {
            display: flex;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 16px;
            font-size: 1.25rem;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 1px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
            }

            .profile-header {
                padding: 24px;
            }

            .profile-avatar {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }

            .glass-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.html">
                <img src="assets/safekeeplogo.png" alt="SafeKeep Logo" width="35" height="35" class="me-2">
                <strong>SafeKeep</strong>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($role === 'admin'): ?>
                        <!-- Admin Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">
                                <i class="bi bi-shield-check me-1"></i>Admin Panel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browse.php">
                                <i class="bi bi-search me-1"></i>Browse Items
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Student Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-house me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browse.php">
                                <i class="bi bi-search me-1"></i>Browse Items
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="messages.php">
                                <i class="bi bi-chat-dots me-1"></i>Messages
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><span id="username"><?= htmlspecialchars($username) ?></span>
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

    <div class="container">
        <div class="main-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <?= strtoupper(substr($username, 0, 1)) ?>
                </div>
                <h1 class="profile-name"><?= htmlspecialchars($username) ?></h1>
                <p class="profile-role">
                    <i class="bi bi-<?= $role === 'admin' ? 'shield-check' : 'person' ?> me-2"></i>
                    <?= ucfirst($role) ?>
                </p>
            </div>

            <div class="container py-4">
                <!-- Stats Section -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number" id="totalItems">0</div>
                        <div class="stat-label">Total Items</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="activeItems">0</div>
                        <div class="stat-label">Active Items</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="foundItems">0</div>
                        <div class="stat-label">Items Found</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="memberSince">2024</div>
                        <div class="stat-label">Member Since</div>
                    </div>
                </div>

                <div class="row">
                    <!-- Account Information -->
                    <div class="col-lg-6">
                        <div class="glass-card">
                            <h3 class="card-title">
                                <i class="bi bi-person-circle"></i>
                                Account Information
                            </h3>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Username</div>
                                    <div class="info-value"><?= htmlspecialchars($username) ?></div>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-shield"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Role</div>
                                    <div class="info-value"><?= ucfirst($role) ?></div>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-calendar"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Member Since</div>
                                    <div class="info-value" id="joinDate">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="col-lg-6">
                        <div class="glass-card">
                            <h3 class="card-title">
                                <i class="bi bi-lock"></i>
                                Security Settings
                            </h3>
                            
                            <form id="changePasswordForm">
                                <div class="mb-3">
                                    <label for="currentPassword" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" required>
                                </div>
                                
                                <button type="submit" class="modern-btn success w-100">
                                    <i class="bi bi-shield-check me-2"></i>Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="glass-card">
                    <h3 class="card-title">
                        <i class="bi bi-clock-history"></i>
                        Recent Activity
                    </h3>
                    
                    <div id="recentActivity">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading activity...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadUserStats();
            loadRecentActivity();
        });

        // Load user statistics
        async function loadUserStats() {
            try {
                const response = await fetch('api/get_user_items.php');
                const data = await response.json();
                
                if (data.success && data.items) {
                    const totalItems = data.items.length;
                    const activeItems = data.items.filter(item => item.status === 'open').length;
                    const foundItems = data.items.filter(item => item.status === 'found').length;
                    
                    // Animate numbers
                    animateNumber('totalItems', totalItems);
                    animateNumber('activeItems', activeItems);
                    animateNumber('foundItems', foundItems);
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Load recent activity
        async function loadRecentActivity() {
            try {
                const response = await fetch('api/get_user_items.php');
                const data = await response.json();
                
                const activityContainer = document.getElementById('recentActivity');
                
                if (data.success && data.items && data.items.length > 0) {
                    activityContainer.innerHTML = '';
                    
                    // Show only recent 5 items
                    const recentItems = data.items.slice(0, 5);
                    
                    recentItems.forEach(item => {
                        const activityEl = createActivityElement(item);
                        activityContainer.appendChild(activityEl);
                    });
                } else {
                    activityContainer.innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: var(--text-secondary); opacity: 0.5;"></i>
                            <h5 style="color: var(--text-secondary); margin-top: 16px;">No activity yet</h5>
                            <p style="color: var(--text-secondary);">Start by posting lost or found items</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading activity:', error);
                document.getElementById('recentActivity').innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                        <h5 class="text-danger mt-3">Error loading activity</h5>
                    </div>
                `;
            }
        }

        // Create activity element
        function createActivityElement(item) {
            const div = document.createElement('div');
            div.className = 'info-item';
            
            const iconClass = item.type === 'lost' ? 'search' : 'check-circle';
            const timeAgo = formatTimeAgo(item.created_at);
            
            div.innerHTML = `
                <div class="info-icon">
                    <i class="bi bi-${iconClass}"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">${item.type === 'lost' ? 'Lost Item' : 'Found Item'}</div>
                    <div class="info-value">${item.name}</div>
                    <small style="color: var(--text-secondary);">${timeAgo} • ${item.location}</small>
                </div>
                <div class="ms-3">
                    <span class="badge ${item.status === 'open' ? 'bg-primary' : 'bg-success'}" style="border-radius: 20px;">
                        ${item.status}
                    </span>
                </div>
            `;
            
            return div;
        }

        // Handle password change
        document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match');
                return;
            }
            
            if (newPassword.length < 6) {
                alert('Password must be at least 6 characters long');
                return;
            }
            
            try {
                const response = await fetch('api/change_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        new_password: newPassword
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Password updated successfully!');
                    this.reset();
                } else {
                    alert('Error: ' + (data.error || 'Failed to update password'));
                }
            } catch (error) {
                console.error('Error changing password:', error);
                alert('Network error. Please try again.');
            }
        });

        // Utility functions
        function animateNumber(elementId, targetNumber) {
            const element = document.getElementById(elementId);
            let currentNumber = 0;
            const increment = targetNumber / 30;
            const timer = setInterval(() => {
                currentNumber += increment;
                if (currentNumber >= targetNumber) {
                    element.textContent = targetNumber;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(currentNumber);
                }
            }, 50);
        }

        function formatTimeAgo(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diffInHours = (now - date) / (1000 * 60 * 60);
            
            if (diffInHours < 1) return 'Just now';
            if (diffInHours < 24) return `${Math.floor(diffInHours)} hours ago`;
            if (diffInHours < 48) return 'Yesterday';
            return date.toLocaleDateString();
        }

        // Logout
        document.getElementById('logoutBtn').addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('api/logout.php', { method: 'POST' });
                if (response.ok) {
                    window.location.href = 'login.html';
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        });
    </script>
</body>
</html>
