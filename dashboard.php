<?php
require_once 'includes/session.php';

// Check if user is logged in
if (!SessionManager::isLoggedIn()) {
    header('Location: login.html');
    exit();
}

$username = SessionManager::getUsername();
$role = SessionManager::getUserRole();

// Redirect admins to their admin dashboard
if ($role === 'admin') {
    header('Location: admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeKeep - Dashboard</title>
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
            padding: 32px;
            transition: all 0.3s ease;
        }

        .main-container:hover {
            box-shadow: var(--shadow-hover);
        }

        /* Welcome Section */
        .welcome-hero {
            background: var(--primary-gradient);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            color: white;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .welcome-hero::before {
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

        .welcome-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .welcome-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
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
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: var(--shadow-soft);
        }

        .glass-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .card-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .card-icon.lost {
            background: var(--secondary-gradient);
        }

        .card-icon.found {
            background: var(--success-gradient);
        }

        .card-icon.browse {
            background: var(--warning-gradient);
        }

        .card-icon.messages {
            background: var(--primary-gradient);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 16px;
            text-align: center;
        }

        .card-description {
            color: var(--text-secondary);
            text-align: center;
            line-height: 1.6;
            margin-bottom: 24px;
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
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: white;
        }

        .modern-btn.secondary {
            background: var(--success-gradient);
        }

        .modern-btn.warning {
            background: var(--warning-gradient);
        }

        .modern-btn.info {
            background: var(--secondary-gradient);
        }

        /* Stats Cards */
        .stats-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .stats-label {
            color: var(--text-secondary);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 1px;
        }

        /* Recent Activity */
        .activity-item {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .activity-item:hover {
            background: rgba(255, 255, 255, 0.98);
            transform: translateX(8px);
        }

        .activity-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            color: white;
            font-size: 1.25rem;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .activity-time {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        /* Modal Styles */
        .modal-content {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
        }

        .modal-header {
            border-bottom: 1px solid var(--glass-border);
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px 20px 0 0;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
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

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 20px;
            }

            .welcome-hero {
                padding: 24px;
            }

            .welcome-hero h1 {
                font-size: 2rem;
            }

            .glass-card {
                padding: 24px;
                margin-bottom: 20px;
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
                    <!-- Student Navigation Only -->
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><span id="username"><?= htmlspecialchars($username) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="main-container fade-in-up">
            <!-- Welcome Section -->
            <div class="welcome-hero">
                <h1><i class="bi bi-shield-check me-3"></i>Welcome to SafeKeep</h1>
                <p>Your trusted platform for lost and found items. Help reunite belongings with their owners and find your missing items.</p>
            </div>

            <!-- Quick Stats -->
            <div class="row mb-5">
                <div class="col-md-3 col-6 mb-3">
                    <div class="stats-card">
                        <div class="stats-number" id="totalItems">0</div>
                        <div class="stats-label">Total Items</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stats-card">
                        <div class="stats-number" id="myItems">0</div>
                        <div class="stats-label">My Items</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stats-card">
                        <div class="stats-number" id="foundItems">0</div>
                        <div class="stats-label">Found Items</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stats-card">
                        <div class="stats-number" id="lostItems">0</div>
                        <div class="stats-label">Lost Items</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-5">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="glass-card text-center">
                        <div class="card-icon lost">
                            <i class="bi bi-search"></i>
                        </div>
                        <h3 class="card-title">Report Lost Item</h3>
                        <p class="card-description">Lost something? Post details to help others find it for you.</p>
                        <button class="modern-btn" data-bs-toggle="modal" data-bs-target="#postItemModal" onclick="setItemType('lost')">
                            <i class="bi bi-plus-circle me-2"></i>Report Lost
                        </button>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="glass-card text-center">
                        <div class="card-icon found">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h3 class="card-title">Report Found Item</h3>
                        <p class="card-description">Found something? Help return it to its rightful owner.</p>
                        <button class="modern-btn secondary" data-bs-toggle="modal" data-bs-target="#postItemModal" onclick="setItemType('found')">
                            <i class="bi bi-plus-circle me-2"></i>Report Found
                        </button>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="glass-card text-center">
                        <div class="card-icon browse">
                            <i class="bi bi-binoculars"></i>
                        </div>
                        <h3 class="card-title">Browse Items</h3>
                        <p class="card-description">Search through lost and found items to find what you're looking for.</p>
                        <a href="browse.php" class="modern-btn warning">
                            <i class="bi bi-search me-2"></i>Browse Now
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="glass-card text-center">
                        <div class="card-icon messages">
                            <i class="bi bi-chat-heart"></i>
                        </div>
                        <h3 class="card-title">Messages</h3>
                        <p class="card-description">Communicate securely with other users about lost and found items.</p>
                        <a href="messages.php" class="modern-btn info">
                            <i class="bi bi-chat-dots me-2"></i>View Messages
                        </a>
                    </div>
                </div>
            </div>

            <!-- My Items Section -->
            <div class="row">
                <div class="col-12 mb-4">
                    <h2 class="text-center mb-4" style="font-weight: 700; color: var(--text-primary);">
                        <i class="bi bi-collection me-2"></i>My Recent Items
                    </h2>
                    <div id="myItemsList">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading your items...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Post Item Modal -->
    <div class="modal fade" id="postItemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="bi bi-plus-circle me-2"></i>Post Item
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="postItemForm">
                        <input type="hidden" id="itemType" name="status" value="">
                        
                        <div class="mb-3">
                            <label for="itemName" class="form-label">Item Name *</label>
                            <input type="text" class="form-control" id="itemName" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="itemDescription" class="form-label">Description *</label>
                            <textarea class="form-control" id="itemDescription" name="description" rows="4" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="itemCategory" class="form-label">Category *</label>
                                <select class="form-control" id="itemCategory" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="electronics">Electronics</option>
                                    <option value="clothing">Clothing</option>
                                    <option value="books">Books</option>
                                    <option value="accessories">Accessories</option>
                                    <option value="keys">Keys</option>
                                    <option value="bags">Bags</option>
                                    <option value="documents">Documents</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="itemLocation" class="form-label">Location *</label>
                                <input type="text" class="form-control" id="itemLocation" name="location" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="itemDate" class="form-label">Date Lost/Found *</label>
                                <input type="date" class="form-control" id="itemDate" name="date_lost_found" required value="2024-01-15">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="itemPhone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control" id="itemPhone" name="contact_phone" placeholder="Optional">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="itemEmail" class="form-label">Contact Email</label>
                            <input type="email" class="form-control" id="itemEmail" name="contact_email" placeholder="Optional - defaults to your school email">
                        </div>
                        
                        <div class="mb-3">
                            <label for="itemImage" class="form-label">Item Image</label>
                            <input type="file" class="form-control" id="itemImage" name="image" accept="image/*">
                            <small class="text-muted">Optional: Upload an image to help identify the item</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="modern-btn" id="submitItemBtn">
                        <i class="bi bi-check-circle me-2"></i>Post Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentItemType = '';

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadStats();
            loadMyItems();
        });

        // Check authentication
        async function checkAuth() {
            try {
                const response = await fetch('api/check_session.php');
                const data = await response.json();
                
                if (data.success && data.user) {
                    document.getElementById('username').textContent = data.user.username;
                } else {
                    // Fallback to server-side session
                    document.getElementById('username').textContent = '<?php echo htmlspecialchars($username); ?>';
                }
            } catch (error) {
                console.error('Auth check error:', error);
                document.getElementById('username').textContent = '<?php echo htmlspecialchars($username); ?>';
            }
        }

        // Load dashboard stats
        async function loadStats() {
            try {
                const response = await fetch('api/browse_items.php');
                const data = await response.json();
                
                if (data.success && data.items) {
                    const totalItems = data.items.length;
                    const foundItems = data.items.filter(item => item.type === 'found').length;
                    const lostItems = data.items.filter(item => item.type === 'lost').length;
                    
                    // Animate numbers
                    animateNumber('totalItems', totalItems);
                    animateNumber('foundItems', foundItems);
                    animateNumber('lostItems', lostItems);
                }
                
                // Load user's items count
                const userResponse = await fetch('api/get_user_items.php');
                const userData = await userResponse.json();
                
                if (userData.success && userData.items) {
                    animateNumber('myItems', userData.items.length);
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Load user's items
        async function loadMyItems() {
            try {
                const response = await fetch('api/get_user_items.php');
                const data = await response.json();
                
                const itemsList = document.getElementById('myItemsList');
                
                if (data.success && data.items && data.items.length > 0) {
                    itemsList.innerHTML = '';
                    
                    // Show only recent 5 items
                    const recentItems = data.items.slice(0, 5);
                    
                    recentItems.forEach(item => {
                        const itemEl = createItemElement(item);
                        itemsList.appendChild(itemEl);
                    });
                } else {
                    itemsList.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: var(--text-secondary); opacity: 0.5;"></i>
                            <h4 style="color: var(--text-secondary); margin-top: 20px;">No items posted yet</h4>
                            <p style="color: var(--text-secondary);">Start by reporting a lost or found item</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading items:', error);
                document.getElementById('myItemsList').innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                        <h4 class="text-danger mt-3">Error loading items</h4>
                        <p class="text-muted">Please refresh the page and try again</p>
                    </div>
                `;
            }
        }

        // Create item element
        function createItemElement(item) {
            const div = document.createElement('div');
            div.className = 'activity-item';
            
            const iconClass = item.type === 'lost' ? 'search' : 'check-circle';
            const iconBg = item.type === 'lost' ? 'var(--secondary-gradient)' : 'var(--success-gradient)';
            const statusText = item.type === 'lost' ? 'Lost' : 'Found';
            const timeAgo = formatTimeAgo(item.created_at);
            
            div.innerHTML = `
                <div class="activity-icon" style="background: ${iconBg}">
                    <i class="bi bi-${iconClass}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${item.name} - ${statusText}</div>
                    <div class="activity-time">${timeAgo} • ${item.location}</div>
                </div>
                <div class="ms-3">
                    <span class="badge" style="background: ${iconBg}; color: white; border-radius: 20px; padding: 6px 12px;">
                        ${item.status}
                    </span>
                </div>
            `;
            
            return div;
        }

        // Set item type for modal
        function setItemType(type) {
            currentItemType = type;
            document.getElementById('itemType').value = type;
            
            const modalTitle = document.getElementById('modalTitle');
            const submitBtn = document.getElementById('submitItemBtn');
            
            if (type === 'lost') {
                modalTitle.innerHTML = '<i class="bi bi-search me-2"></i>Report Lost Item';
                submitBtn.innerHTML = '<i class="bi bi-search me-2"></i>Report Lost';
                submitBtn.className = 'modern-btn';
            } else {
                modalTitle.innerHTML = '<i class="bi bi-check-circle me-2"></i>Report Found Item';
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Report Found';
                submitBtn.className = 'modern-btn secondary';
            }
        }

        // Handle form submission
        document.getElementById('submitItemBtn').addEventListener('click', async function() {
            const form = document.getElementById('postItemForm');
            const formData = new FormData(form);
            
            // Validate required fields
            const requiredFields = ['title', 'description', 'category', 'location', 'date_lost_found'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                let input;
                if (field === 'title') {
                    input = document.getElementById('itemName');
                } else if (field === 'date_lost_found') {
                    input = document.getElementById('itemDate');
                } else {
                    input = document.getElementById('item' + field.charAt(0).toUpperCase() + field.slice(1));
                }
                
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Posting...';
            
            try {
                const response = await fetch('api/create_item.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Close modal and refresh
                    bootstrap.Modal.getInstance(document.getElementById('postItemModal')).hide();
                    form.reset();
                    loadStats();
                    loadMyItems();
                    
                    // Show success message
                    showSuccessMessage(`${currentItemType === 'lost' ? 'Lost' : 'Found'} item posted successfully!`);
                } else {
                    alert('Error posting item: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error posting item:', error);
                alert('Network error. Please try again.');
            } finally {
                // Reset button
                this.disabled = false;
                const originalText = currentItemType === 'lost' ? 
                    '<i class="bi bi-search me-2"></i>Report Lost' : 
                    '<i class="bi bi-check-circle me-2"></i>Report Found';
                this.innerHTML = originalText;
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

        function showSuccessMessage(message) {
            // Create success toast
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast show" role="alert">
                    <div class="toast-header bg-success text-white">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong class="me-auto">Success</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>
            `;
            document.body.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.remove();
            }, 5000);
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
