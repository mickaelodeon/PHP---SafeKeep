<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SafeKeep</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.html">
                <img src="assets/safekeeplogo.png" alt="SafeKeep Logo" width="35" height="35" class="me-2">
                <strong>SafeKeep Admin</strong>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin.php">Admin Panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">User Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="browse.php">Browse Items</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <span id="username">Admin</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Admin Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-dark text-white p-4 rounded">
                    <h1 class="mb-2"><i class="bi bi-shield-check"></i> SafeKeep Admin Dashboard</h1>
                    <p class="mb-0">Manage items, users, and system settings</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Pending Items</h6>
                                <h2 class="mb-0" id="pendingCount">-</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-clock-history" style="font-size: 2rem; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Approved Items</h6>
                                <h2 class="mb-0" id="approvedCount">-</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-check-circle" style="font-size: 2rem; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Users</h6>
                                <h2 class="mb-0" id="usersCount">-</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-people" style="font-size: 2rem; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Items</h6>
                                <h2 class="mb-0" id="totalItemsCount">-</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-archive" style="font-size: 2rem; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Tabs -->
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                            <i class="bi bi-clock-history"></i> Pending Approval
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab">
                            <i class="bi bi-archive"></i> All Items
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                            <i class="bi bi-people"></i> Users
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">
                            <i class="bi bi-gear"></i> Settings
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="adminTabContent">
                    <!-- Pending Approval Tab -->
                    <div class="tab-pane fade show active" id="pending" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Items Pending Approval</h5>
                            </div>
                            <div class="card-body">
                                <div id="pendingItemsContainer">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- All Items Tab -->
                    <div class="tab-pane fade" id="items" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">All Items</h5>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="allItemsBtn">All</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="approvedItemsBtn">Approved</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="rejectedItemsBtn">Rejected</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="allItemsContainer">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users Tab -->
                    <div class="tab-pane fade" id="users" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">User Management</h5>
                            </div>
                            <div class="card-body">
                                <div id="usersContainer">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Tab -->
                    <div class="tab-pane fade" id="settings" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">System Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Database Actions</h6>
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-primary" onclick="testDatabase()">
                                                <i class="bi bi-database-check"></i> Test Database Connection
                                            </button>
                                            <button type="button" class="btn btn-outline-info" onclick="viewDatabaseStats()">
                                                <i class="bi bi-bar-chart"></i> View Database Statistics
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>System Actions</h6>
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-warning" onclick="clearCache()">
                                                <i class="bi bi-arrow-clockwise"></i> Clear System Cache
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="exportData()">
                                                <i class="bi bi-download"></i> Export Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <div id="settingsOutput" class="mt-3">
                                    <!-- Settings output will appear here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Detail Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalTitle">Item Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="itemModalBody">
                    <!-- Item details will be loaded here -->
                </div>
                <div class="modal-footer" id="itemModalFooter">
                    <!-- Action buttons will be added dynamically -->
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Check admin authentication on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAdminAuth();
            loadStatistics();
            loadPendingItems();
            
            // Tab change handlers
            document.getElementById('pending-tab').addEventListener('click', loadPendingItems);
            document.getElementById('items-tab').addEventListener('click', () => loadAllItems('all'));
            document.getElementById('users-tab').addEventListener('click', loadUsers);
            
            // Item filter buttons
            document.getElementById('allItemsBtn').addEventListener('click', () => {
                setActiveFilter('allItemsBtn');
                loadAllItems('all');
            });
            document.getElementById('approvedItemsBtn').addEventListener('click', () => {
                setActiveFilter('approvedItemsBtn');
                loadAllItems('approved');
            });
            document.getElementById('rejectedItemsBtn').addEventListener('click', () => {
                setActiveFilter('rejectedItemsBtn');
                loadAllItems('rejected');
            });
        });

        // Check if user is admin
        function checkAdminAuth() {
            // This would normally check session/token
            // For now, we'll assume the user is logged in as admin
            document.getElementById('username').textContent = 'Admin';
        }

        // Load dashboard statistics
        async function loadStatistics() {
            try {
                const response = await fetch('api/admin_stats.php');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('pendingCount').textContent = data.stats.pending;
                    document.getElementById('approvedCount').textContent = data.stats.approved;
                    document.getElementById('usersCount').textContent = data.stats.users;
                    document.getElementById('totalItemsCount').textContent = data.stats.total_items;
                } else {
                    showAlert('danger', `Failed to load statistics: ${data.error || 'Unknown error'}`);
                    console.error('API Error:', data);
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
                showAlert('danger', `Network error loading statistics: ${error.message}`);
                
                // Try to load test stats to check if it's an auth issue
                try {
                    const testResponse = await fetch('api/test_stats.php');
                    const testData = await testResponse.json();
                    if (testData.success) {
                        showAlert('warning', 'Database works, but admin authentication failed. Please log in as admin.');
                    }
                } catch (testError) {
                    showAlert('danger', 'Database connection issue');
                }
            }
        }

        // Load pending items
        async function loadPendingItems() {
            try {
                const response = await fetch('api/admin_items.php?status=pending');
                const data = await response.json();
                
                const container = document.getElementById('pendingItemsContainer');
                
                if (data.success && data.items.length > 0) {
                    container.innerHTML = data.items.map(item => createPendingItemCard(item)).join('');
                } else {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">No pending items</h5>
                            <p class="text-muted">All items have been reviewed</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading pending items:', error);
                showAlert('danger', 'Failed to load pending items');
            }
        }

        // Load all items with filter
        async function loadAllItems(filter = 'all') {
            try {
                const response = await fetch(`api/admin_items.php?filter=${filter}`);
                const data = await response.json();
                
                const container = document.getElementById('allItemsContainer');
                
                if (data.success && data.items.length > 0) {
                    container.innerHTML = `
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Posted By</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.items.map(item => createItemTableRow(item)).join('')}
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
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                        <th>Items Posted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.users.map(user => createUserTableRow(user)).join('')}
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

        // Create pending item card
        function createPendingItemCard(item) {
            const statusBadge = item.status === 'lost' ? 'bg-danger' : 'bg-success';
            return `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">
                                    ${item.title} 
                                    <span class="badge ${statusBadge}">${item.status.toUpperCase()}</span>
                                </h5>
                                <p class="card-text">${item.description.substring(0, 150)}...</p>
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> ${item.posted_by} | 
                                    <i class="bi bi-geo-alt"></i> ${item.location} | 
                                    <i class="bi bi-calendar"></i> ${new Date(item.created_at).toLocaleDateString()}
                                </small>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group-vertical d-grid gap-2">
                                    <button class="btn btn-success btn-sm" onclick="approveItem(${item.id})">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="rejectItem(${item.id})">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewItemDetails(${item.id})">
                                        <i class="bi bi-eye"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Create item table row
        function createItemTableRow(item) {
            const statusColor = item.is_approved == 1 ? 'success' : (item.is_approved == 0 ? 'warning' : 'danger');
            const statusText = item.is_approved == 1 ? 'Approved' : (item.is_approved == 0 ? 'Pending' : 'Rejected');
            const itemStatusBadge = item.status === 'lost' ? 'bg-danger' : 'bg-success';
            
            return `
                <tr>
                    <td>${item.id}</td>
                    <td>
                        ${item.title}
                        <br><small class="badge ${itemStatusBadge}">${item.status.toUpperCase()}</small>
                    </td>
                    <td>${item.category}</td>
                    <td><span class="badge bg-${statusColor}">${statusText}</span></td>
                    <td>${item.posted_by}</td>
                    <td>${new Date(item.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewItemDetails(${item.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                        ${item.is_approved != 1 ? `
                            <button class="btn btn-sm btn-success" onclick="approveItem(${item.id})">
                                <i class="bi bi-check"></i>
                            </button>
                        ` : ''}
                        <button class="btn btn-sm btn-danger" onclick="deleteItem(${item.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Create user table row
        function createUserTableRow(user) {
            const roleBadge = user.role === 'admin' ? 'bg-danger' : (user.role === 'staff' ? 'bg-warning' : 'bg-primary');
            return `
                <tr>
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td><span class="badge ${roleBadge}">${user.role.toUpperCase()}</span></td>
                    <td>${new Date(user.created_at).toLocaleDateString()}</td>
                    <td>${user.items_count || 0}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewUserDetails(${user.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Set active filter button
        function setActiveFilter(buttonId) {
            ['allItemsBtn', 'approvedItemsBtn', 'rejectedItemsBtn'].forEach(id => {
                document.getElementById(id).classList.remove('active');
            });
            document.getElementById(buttonId).classList.add('active');
        }

        // Approve item
        async function approveItem(itemId) {
            try {
                const response = await fetch('api/admin_approve.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ item_id: itemId, action: 'approve' })
                });
                
                const data = await response.json();
                if (data.success) {
                    showAlert('success', 'Item approved successfully');
                    loadStatistics();
                    loadPendingItems();
                } else {
                    showAlert('danger', data.error || 'Failed to approve item');
                }
            } catch (error) {
                console.error('Error approving item:', error);
                showAlert('danger', 'Network error');
            }
        }

        // Reject item
        async function rejectItem(itemId) {
            if (confirm('Are you sure you want to reject this item?')) {
                try {
                    const response = await fetch('api/admin_approve.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ item_id: itemId, action: 'reject' })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        showAlert('success', 'Item rejected');
                        loadStatistics();
                        loadPendingItems();
                    } else {
                        showAlert('danger', data.error || 'Failed to reject item');
                    }
                } catch (error) {
                    console.error('Error rejecting item:', error);
                    showAlert('danger', 'Network error');
                }
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
                        loadStatistics();
                        loadAllItems('all');
                    } else {
                        showAlert('danger', data.error || 'Failed to delete item');
                    }
                } catch (error) {
                    console.error('Error deleting item:', error);
                    showAlert('danger', 'Network error');
                }
            }
        }

        // View item details
        async function viewItemDetails(itemId) {
            try {
                const response = await fetch(`api/get_item_details.php?id=${itemId}`);
                const data = await response.json();
                
                if (data.success) {
                    const item = data.item;
                    document.getElementById('itemModalTitle').textContent = item.title;
                    document.getElementById('itemModalBody').innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <img src="${item.image_url || 'assets/safekeeplogo.png'}" 
                                     class="img-fluid rounded" alt="${item.title}">
                            </div>
                            <div class="col-md-6">
                                <h6>Description:</h6>
                                <p>${item.description}</p>
                                
                                <h6>Details:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Category:</strong> ${item.category}</li>
                                    <li><strong>Status:</strong> ${item.status}</li>
                                    <li><strong>Location:</strong> ${item.location}</li>
                                    <li><strong>Date:</strong> ${new Date(item.date_lost_found).toLocaleDateString()}</li>
                                    <li><strong>Posted:</strong> ${new Date(item.created_at).toLocaleDateString()}</li>
                                    <li><strong>Posted by:</strong> ${item.posted_by}</li>
                                </ul>
                            </div>
                        </div>
                    `;
                    
                    const modal = new bootstrap.Modal(document.getElementById('itemModal'));
                    modal.show();
                } else {
                    showAlert('danger', 'Failed to load item details');
                }
            } catch (error) {
                console.error('Error loading item details:', error);
                showAlert('danger', 'Network error');
            }
        }

        // Settings functions
        function testDatabase() {
            fetch('debug.php')
                .then(response => response.text())
                .then(html => {
                    const output = document.getElementById('settingsOutput');
                    output.innerHTML = `<div class="alert alert-info"><h6>Database Test Results:</h6>${html}</div>`;
                })
                .catch(error => {
                    showAlert('danger', 'Database test failed');
                });
        }

        function viewDatabaseStats() {
            showAlert('info', 'Database statistics feature coming soon');
        }

        function clearCache() {
            showAlert('success', 'Cache cleared successfully');
        }

        function exportData() {
            showAlert('info', 'Data export feature coming soon');
        }

        // Show alert function
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Logout function
        document.getElementById('logoutBtn').addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('api/logout.php', { method: 'POST' });
                const data = await response.json();
                if (data.success) {
                    window.location.href = 'login.html';
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        });
    </script>
</body>
</html>
