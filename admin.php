<?php
require_once 'includes/session.php';

// Check if user is logged in and is admin
if (!SessionManager::isLoggedIn() || SessionManager::getUserRole() !== 'admin') {
    header('Location: login.html');
    exit();
}

$username = SessionManager::getUsername();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SafeKeep</title>
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
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #feca57 100%);
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

        .main-container:hover {
            box-shadow: var(--shadow-hover);
        }

        /* Glass Cards */
        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        /* Stats Cards */
        .stats-card {
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stats-card:hover::before {
            transform: translateX(100%);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Navigation Tabs */
        .nav-tabs {
            border: none;
            background: var(--glass-bg);
            border-radius: 20px 20px 0 0;
            padding: 16px 16px 0;
            backdrop-filter: blur(20px);
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 16px;
            margin-right: 8px;
            color: var(--text-secondary);
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 12px 24px;
        }

        .nav-tabs .nav-link:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .nav-tabs .nav-link.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: var(--shadow-soft);
        }
        }

        .table {
            border-radius: var(--border-radius);
            overflow: hidden;
            background: white;
        }

        .table th {
            background: var(--primary-gradient);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            transition: transform 0.3s ease;
        }

        .item-image:hover {
            transform: scale(1.1);
        }

        .btn {
            border-radius: 25px;
            font-weight: 500;
            padding: 8px 20px;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .btn-primary {
            background: var(--primary-gradient);
            box-shadow: var(--shadow-light);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-success {
            background: var(--success-gradient);
        }

        .btn-outline-primary:hover, .btn-outline-danger:hover {
            transform: translateY(-2px);
        }

        .modal-content {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .stats-card h3 {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="admin.php">
                <img src="assets/safekeeplogo.png" alt="SafeKeep" height="30" class="me-2">
                SafeKeep Admin
            </a>
            <div class="navbar-nav ms-auto d-flex flex-row">
                <a class="nav-link me-3" href="browse.php">Browse Items</a>
                <span class="nav-link me-3">Welcome, <?php echo htmlspecialchars($username); ?></span>
                <a class="nav-link" href="api/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Stats Dashboard -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 id="totalItems" class="mb-1">-</h3>
                        <p class="mb-0">Total Items</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 id="pendingItems" class="mb-1">-</h3>
                        <p class="mb-0">Pending Approval</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 id="totalUsers" class="mb-1">-</h3>
                        <p class="mb-0">Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 id="returnedItems" class="mb-1">-</h3>
                        <p class="mb-0">Items Returned</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab">
                    Items Management
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    Pending Approval
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                    User Management
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="adminTabsContent">
            <!-- Items Management Tab -->
            <div class="tab-pane fade show active" id="items" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">All Items</h5>
                    </div>
                    <div class="card-body">
                        <div id="itemsContainer">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Approval Tab -->
            <div class="tab-pane fade" id="pending" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Items Pending Approval</h5>
                    </div>
                    <div class="card-body">
                        <div id="pendingContainer">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Management Tab -->
            <div class="tab-pane fade" id="users" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">User Management</h5>
                    </div>
                    <div class="card-body">
                        <div id="usersContainer">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Details Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalTitle">Item Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="itemModalContent">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>

    <script src="assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // NEW ADMIN VERSION - FRESH JAVASCRIPT
        console.log('=== NEW ADMIN.PHP LOADED - NO CACHE ===');
        console.log('Version: 3.0');
        console.log('Timestamp:', new Date().toISOString());

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadItems();
            loadPendingItems();
            loadUsers();
        });

        // Tab change handlers
        document.getElementById('items-tab').addEventListener('click', loadItems);
        document.getElementById('pending-tab').addEventListener('click', loadPendingItems);
        document.getElementById('users-tab').addEventListener('click', loadUsers);

        // Show alert
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alertHtml;
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) alert.remove();
            }, 5000);
        }

        // Load stats
        async function loadStats() {
            try {
                const response = await fetch('api/admin_stats.php');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('totalItems').textContent = data.stats.total_items;
                    document.getElementById('pendingItems').textContent = data.stats.pending_items;
                    document.getElementById('totalUsers').textContent = data.stats.total_users;
                    document.getElementById('returnedItems').textContent = data.stats.returned_items;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Load all items
        async function loadItems() {
            try {
                const response = await fetch('api/admin_items.php');
                const data = await response.json();
                
                const container = document.getElementById('itemsContainer');
                
                if (data.success && data.items.length > 0) {
                    container.innerHTML = `
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Date Found</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.items.map(item => `
                                        <tr>
                                            <td>
                                                <img src="${item.image_url || 'assets/safekeeplogo.png'}" 
                                                     class="item-image" alt="${item.title}">
                                            </td>
                                            <td><strong>${item.title}</strong></td>
                                            <td>${item.category}</td>
                                            <td>
                                                <span class="badge ${item.status === 'approved' ? 'bg-success' : 
                                                                   item.status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary'}">
                                                    ${item.status}
                                                </span>
                                            </td>
                                            <td>${new Date(item.date_found).toLocaleDateString()}</td>
                                            <td>
                                                <button class="btn btn-outline-primary btn-sm" onclick="viewItemDetails(${item.id})" title="View Details">
                                                    👁 View Details
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm ms-1" onclick="deleteItem(${item.id})" title="Delete Item">
                                                    🗑 Delete
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-muted">No items found</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading items:', error);
                showAlert('danger', 'Failed to load items');
            }
        }

        // Load pending items
        async function loadPendingItems() {
            try {
                const response = await fetch('api/admin_items.php?status=pending');
                const data = await response.json();
                
                const container = document.getElementById('pendingContainer');
                
                if (data.success && data.items.length > 0) {
                    container.innerHTML = `
                        <div class="row g-4">
                            ${data.items.map(item => `
                                <div class="col-md-6 col-lg-4">
                                    <div class="card pending-item-card h-100">
                                        <div class="position-relative">
                                            <img src="${item.image_url || 'assets/safekeeplogo.png'}" 
                                                 class="card-img-top" style="height: 200px; object-fit: cover;">
                                            <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                                                Pending
                                            </span>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title fw-bold text-primary">${item.title}</h6>
                                            <p class="card-text small text-muted mb-2">
                                                <i class="fas fa-tag me-1"></i>${item.category}
                                            </p>
                                            <p class="card-text small flex-grow-1">${item.description.substring(0, 100)}${item.description.length > 100 ? '...' : ''}</p>
                                            <div class="d-flex gap-2 mt-auto">
                                                <button class="btn btn-success btn-sm flex-fill" onclick="approveItem(${item.id})" title="Approve Item">
                                                    ✓ Approve
                                                </button>
                                                <button class="btn btn-warning btn-sm flex-fill" onclick="rejectItem(${item.id})" title="Reject Item">
                                                    ✗ Reject
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm" onclick="viewItemDetails(${item.id})" title="View Details">
                                                    👁 Details
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="deleteItem(${item.id})" title="Delete Item">
                                                    🗑 Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-muted">No pending items</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading pending items:', error);
                showAlert('danger', 'Failed to load pending items');
            }
        }

        // NEW VIEW ITEM DETAILS FUNCTION - USES DEBUG API
        async function viewItemDetails(itemId) {
            try {
                const apiUrl = `api/admin_item_details_full.php?id=${itemId}&_=${Date.now()}`;
                console.log('=== ADMIN ITEM DETAILS ===');
                console.log('Loading item details for ID:', itemId);
                
                // Show modal first
                const modal = new bootstrap.Modal(document.getElementById('itemModal'));
                modal.show();
                
                const response = await fetch(apiUrl);
                console.log('Response status:', response.status);
                console.log('Response URL:', response.url);
                
                if (!response.ok) {
                    console.error('Response not OK:', response.status, response.statusText);
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    const item = data.item;
                    document.getElementById('itemModalTitle').textContent = item.title;
                    document.getElementById('itemModalContent').innerHTML = `
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <img src="${item.image_url || 'assets/safekeeplogo.png'}" 
                                         class="img-fluid rounded shadow" alt="${item.title}" style="max-height: 300px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="item-details">
                                    <h6 class="text-primary mb-3">📋 Item Information</h6>
                                    <div class="mb-3">
                                        <strong>🏷 Category:</strong> 
                                        <span class="badge bg-secondary ms-2">${item.category}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>📊 Status:</strong> 
                                        <span class="badge ms-2 ${item.status === 'approved' ? 'bg-success' : 
                                                               item.status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary'}">
                                            ${item.status.toUpperCase()}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>📅 Date Found:</strong> 
                                        <span class="text-muted">${new Date(item.date_found).toLocaleDateString()}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>📍 Location:</strong> 
                                        <span class="text-muted">${item.location_found}</span>
                                    </div>
                                    <div class="mb-4">
                                        <strong>📝 Description:</strong>
                                        <p class="text-muted mt-2">${item.description}</p>
                                    </div>
                                    
                                    <h6 class="text-primary mb-3">👤 Contact Information</h6>
                                    <div class="mb-2">
                                        <strong>👨‍💼 Submitted by:</strong> 
                                        <span class="text-muted">${item.username || 'Unknown'}</span>
                                    </div>
                                    <div class="mb-4">
                                        <strong>📧 Email:</strong> 
                                        <span class="text-muted">${item.email || 'Not provided'}</span>
                                    </div>
                                    
                                    ${item.status === 'pending' ? `
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-success" onclick="approveItem(${item.id}); bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();">
                                                ✅ Approve This Item
                                            </button>
                                            <button class="btn btn-warning" onclick="rejectItem(${item.id}); bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();">
                                                ❌ Reject This Item
                                            </button>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    document.getElementById('itemModalContent').innerHTML = `
                        <div class="alert alert-danger">
                            ${data.message || 'Failed to load item details'}
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading item details:', error);
                document.getElementById('itemModalContent').innerHTML = `
                    <div class="alert alert-danger">
                        Error: ${error.message}
                    </div>
                `;
            }
        }

        // Approve item
        async function approveItem(itemId) {
            try {
                const response = await fetch('api/admin_approve.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        item_id: itemId,
                        action: 'approve'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', 'Item approved successfully');
                    loadStats();
                    loadItems();
                    loadPendingItems();
                } else {
                    showAlert('danger', data.error || data.message || 'Failed to approve item');
                }
            } catch (error) {
                console.error('Error approving item:', error);
                showAlert('danger', 'Error approving item');
            }
        }

        // Reject item
        async function rejectItem(itemId) {
            try {
                const response = await fetch('api/admin_approve.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        item_id: itemId,
                        action: 'reject'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('warning', 'Item rejected successfully');
                    loadStats();
                    loadItems();
                    loadPendingItems();
                } else {
                    showAlert('danger', data.error || data.message || 'Failed to reject item');
                }
            } catch (error) {
                console.error('Error rejecting item:', error);
                showAlert('danger', 'Error rejecting item');
            }
        }

        // Delete item
        async function deleteItem(itemId) {
            if (confirm('Are you sure you want to permanently delete this item?')) {
                try {
                    const response = await fetch('api/admin_delete.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ item_id: itemId })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showAlert('success', 'Item deleted successfully');
                        loadStats();
                        loadItems();
                        loadPendingItems();
                    } else {
                        showAlert('danger', data.message || 'Failed to delete item');
                    }
                } catch (error) {
                    console.error('Error deleting item:', error);
                    showAlert('danger', 'Error deleting item');
                }
            }
        }

        // Load users
        async function loadUsers() {
            try {
                const response = await fetch('api/admin_users.php');
                const data = await response.json();
                
                const container = document.getElementById('usersContainer');
                
                if (data.success && data.users.length > 0) {
                    container.innerHTML = `
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created</th>
                                        <th>Items Posted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.users.map(user => `
                                        <tr>
                                            <td><strong>${user.username}</strong></td>
                                            <td>${user.email}</td>
                                            <td>
                                                <span class="badge ${user.role === 'admin' ? 'bg-danger' : 'bg-primary'}">
                                                    ${user.role}
                                                </span>
                                            </td>
                                            <td>${new Date(user.created_at).toLocaleDateString()}</td>
                                            <td>${user.items_count || 0}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-muted">No users found</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showAlert('danger', 'Failed to load users');
            }
        }
    </script>
</body>
</html>
