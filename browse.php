<?php
require_once 'includes/session.php';

// Check user authentication and role
$isLoggedIn = SessionManager::isLoggedIn();
$username = $isLoggedIn ? SessionManager::getUsername() : null;
$userRole = $isLoggedIn ? SessionManager::getUserRole() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Items - SafeKeep</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/auth.css">
    <style>
        /* Modern UI styling matching admin.php */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --shadow-light: 0 2px 15px rgba(0,0,0,0.1);
            --border-radius: 12px;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .navbar {
            background: var(--primary-gradient) !important;
            box-shadow: var(--shadow-light);
            border: none;
        }

        .navbar-brand img {
            filter: brightness(1.2);
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <!-- Navigation - Role-based design matching admin.php -->
    <?php if ($isLoggedIn && $userRole === 'admin'): ?>
        <!-- Admin Navigation (matches admin.php exactly) -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="admin.php">
                    <img src="assets/safekeeplogo.png" alt="SafeKeep" height="30" class="me-2">
                    SafeKeep Admin
                </a>
                <div class="navbar-nav ms-auto d-flex flex-row">
                    <a class="nav-link me-3" href="admin.php">Admin Panel</a>
                    <a class="nav-link me-3 active" href="browse.php">Browse Items</a>
                    <span class="nav-link me-3">Welcome, <?php echo htmlspecialchars($username); ?></span>
                    <a class="nav-link" href="api/logout.php">Logout</a>
                </div>
            </div>
        </nav>
    <?php elseif ($isLoggedIn && $userRole === 'student'): ?>
        <!-- Student Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                    <img src="assets/safekeeplogo.png" alt="SafeKeep" height="30" class="me-2">
                    SafeKeep
                </a>
                <div class="navbar-nav ms-auto d-flex flex-row">
                    <a class="nav-link me-3" href="dashboard.php">Dashboard</a>
                    <a class="nav-link me-3 active" href="browse.php">Browse Items</a>
                    <a class="nav-link me-3" href="messages.php">Messages</a>
                    <span class="nav-link me-3">Welcome, <?php echo htmlspecialchars($username); ?></span>
                    <a class="nav-link" href="api/logout.php">Logout</a>
                </div>
            </div>
        </nav>
    <?php else: ?>
        <!-- Guest Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.html">
                    <img src="assets/safekeeplogo.png" alt="SafeKeep" height="30" class="me-2">
                    SafeKeep
                </a>
                <div class="navbar-nav ms-auto d-flex flex-row">
                    <a class="nav-link me-3" href="index.html">Home</a>
                    <a class="nav-link me-3 active" href="browse.php">Browse Items</a>
                    <a class="nav-link me-3" href="login.html">Login</a>
                    <a class="nav-link me-3" href="register.html">Register</a>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <div class="container mt-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-primary text-white p-4 rounded">
                    <h1 class="mb-2">Browse Lost & Found Items</h1>
                    <p class="mb-0">Search through posted items to find what you're looking for or help return found items to their owners.</p>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-search"></i> Search & Filter</h5>
                    </div>
                    <div class="card-body">
                        <form id="searchForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="searchQuery" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="searchQuery" name="search" 
                                           placeholder="Search by title, description, or location...">
                                </div>
                                <div class="col-md-3">
                                    <label for="categoryFilter" class="form-label">Category</label>
                                    <select class="form-control" id="categoryFilter" name="category">
                                        <option value="">All Categories</option>
                                        <option value="Electronics">Electronics</option>
                                        <option value="Clothing">Clothing</option>
                                        <option value="Books & Stationery">Books & Stationery</option>
                                        <option value="Sports Equipment">Sports Equipment</option>
                                        <option value="Personal Items">Personal Items</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="statusFilter" class="form-label">Status</label>
                                    <select class="form-control" id="statusFilter" name="status">
                                        <option value="">All Items</option>
                                        <option value="lost">Lost Items</option>
                                        <option value="found">Found Items</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="sortBy" class="form-label">Sort By</label>
                                    <select class="form-control" id="sortBy" name="sort">
                                        <option value="date_desc">Newest First</option>
                                        <option value="date_asc">Oldest First</option>
                                        <option value="title_asc">Title A-Z</option>
                                        <option value="title_desc">Title Z-A</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Summary -->
        <div class="row mb-3">
            <div class="col-12">
                <div id="resultsInfo" class="text-muted">
                    <div class="d-flex justify-content-between align-items-center">
                        <span id="resultsCount">Loading items...</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" id="gridView">
                                <i class="bi bi-grid"></i> Grid
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="listView">
                                <i class="bi bi-list"></i> List
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Grid -->
        <div id="itemsContainer" class="row g-4">
            <!-- Loading spinner -->
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row mt-5">
            <div class="col-12">
                <nav id="paginationNav" class="d-none">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Pagination will be inserted here -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Item Detail Modal -->
    <div class="modal fade" id="itemDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalTitle">Item Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="itemModalBody">
                    <!-- Item details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="contactOwnerBtn">
                        <i class="bi bi-envelope"></i> Contact Owner
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let totalPages = 1;
        let currentView = 'grid';
        let currentFilters = {};

        // Load items on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadItems();
            
            // Add logout functionality for authenticated users
            <?php if ($isLoggedIn): ?>
            const logoutLinks = document.querySelectorAll('a[href="api/logout.php"]');
            logoutLinks.forEach(link => {
                link.addEventListener('click', async function(e) {
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
            });
            <?php endif; ?>
            
            // Search form submission
            document.getElementById('searchForm').addEventListener('submit', function(e) {
                e.preventDefault();
                currentPage = 1;
                loadItems();
            });

            // View toggle buttons
            document.getElementById('gridView').addEventListener('click', function() {
                setView('grid');
            });
            
            document.getElementById('listView').addEventListener('click', function() {
                setView('list');
            });
            
            // Contact owner button
            document.getElementById('contactOwnerBtn').addEventListener('click', function() {
                openContactModal();
            });
        });

        // Load items with filters
        async function loadItems() {
            try {
                const formData = new FormData(document.getElementById('searchForm'));
                const params = new URLSearchParams();
                
                for (let [key, value] of formData.entries()) {
                    if (value.trim()) {
                        params.append(key, value.trim());
                    }
                }
                
                params.append('page', currentPage);
                params.append('limit', 12);

                const response = await fetch('api/browse_items.php?' + params);
                const data = await response.json();

                if (data.success) {
                    displayItems(data.items);
                    updateResultsInfo(data.total, data.page, data.totalPages);
                    updatePagination(data.page, data.totalPages);
                    totalPages = data.totalPages;
                } else {
                    showError(data.error || 'Failed to load items');
                }
            } catch (error) {
                console.error('Error loading items:', error);
                showError('Network error. Please try again.');
            }
        }

        // Display items in grid or list view
        function displayItems(items) {
            const container = document.getElementById('itemsContainer');
            
            if (!items || items.length === 0) {
                container.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d;"></i>
                        <h4 class="mt-3 text-muted">No items found</h4>
                        <p class="text-muted">Try adjusting your search criteria</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = '';
            
            items.forEach(item => {
                const itemElement = createItemElement(item);
                container.appendChild(itemElement);
            });
        }

        // Create item element
        function createItemElement(item) {
            const col = document.createElement('div');
            col.className = currentView === 'grid' ? 'col-md-4 col-lg-3' : 'col-12';
            
            const statusBadge = item.status === 'lost' ? 'bg-danger' : 'bg-success';
            const statusText = item.status === 'lost' ? 'LOST' : 'FOUND';
            const imageUrl = item.image_url ? item.image_url : 'assets/safekeeplogo.png';
            
            const cardClass = currentView === 'grid' ? 'card h-100' : 'card mb-3';
            const cardBody = currentView === 'grid' ? 
                `<div class="card-body">
                    <h6 class="card-title">
                        ${item.title}
                        <span class="badge ${statusBadge} ms-2">${statusText}</span>
                    </h6>
                    <p class="card-text text-muted small">${item.description.substring(0, 80)}...</p>
                    <div class="mt-auto">
                        <small class="text-muted">
                            <i class="bi bi-geo-alt"></i> ${item.location}<br>
                            <i class="bi bi-calendar"></i> ${new Date(item.date_lost_found).toLocaleDateString()}
                        </small>
                    </div>
                </div>` :
                `<div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">
                                ${item.title}
                                <span class="badge ${statusBadge} ms-2">${statusText}</span>
                            </h5>
                            <p class="card-text">${item.description.substring(0, 150)}...</p>
                            <small class="text-muted">
                                <i class="bi bi-geo-alt"></i> ${item.location} | 
                                <i class="bi bi-calendar"></i> ${new Date(item.date_lost_found).toLocaleDateString()} |
                                <i class="bi bi-tag"></i> ${item.category}
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-primary btn-sm" onclick="showItemDetails(${item.id})">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>`;

            col.innerHTML = `
                <div class="${cardClass} shadow-sm border-0" style="cursor: pointer;" onclick="showItemDetails(${item.id})">
                    ${currentView === 'grid' ? `<img src="${imageUrl}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="${item.title}">` : ''}
                    ${cardBody}
                </div>
            `;
            
            return col;
        }

        // Set view mode
        function setView(view) {
            currentView = view;
            
            // Update buttons
            document.getElementById('gridView').classList.toggle('active', view === 'grid');
            document.getElementById('listView').classList.toggle('active', view === 'list');
            
            // Reload current items with new view
            loadItems();
        }

        // Update results info
        function updateResultsInfo(total, page, totalPages) {
            const start = (page - 1) * 12 + 1;
            const end = Math.min(page * 12, total);
            
            document.getElementById('resultsCount').textContent = 
                `Showing ${start}-${end} of ${total} items`;
        }

        // Update pagination
        function updatePagination(page, totalPages) {
            const paginationNav = document.getElementById('paginationNav');
            const pagination = document.getElementById('pagination');
            
            if (totalPages <= 1) {
                paginationNav.classList.add('d-none');
                return;
            }
            
            paginationNav.classList.remove('d-none');
            pagination.innerHTML = '';
            
            // Previous button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${page - 1})">Previous</a>`;
            pagination.appendChild(prevLi);
            
            // Page numbers
            for (let i = Math.max(1, page - 2); i <= Math.min(totalPages, page + 2); i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
                pagination.appendChild(li);
            }
            
            // Next button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${page + 1})">Next</a>`;
            pagination.appendChild(nextLi);
        }

        // Change page
        function changePage(page) {
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                loadItems();
            }
        }

        // Show item details in modal
        async function showItemDetails(itemId) {
            try {
                const response = await fetch(`api/get_item_details.php?id=${itemId}`);
                const data = await response.json();

                if (data.success) {
                    const item = data.item;
                    const statusBadge = item.status === 'lost' ? 'bg-danger' : 'bg-success';
                    const statusText = item.status === 'lost' ? 'LOST ITEM' : 'FOUND ITEM';
                    
                    document.getElementById('itemModalTitle').innerHTML = `
                        ${item.title} <span class="badge ${statusBadge}">${statusText}</span>
                    `;
                    
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
                                    <li><strong>Location:</strong> ${item.location}</li>
                                    <li><strong>Date:</strong> ${new Date(item.date_lost_found).toLocaleDateString()}</li>
                                    <li><strong>Posted:</strong> ${new Date(item.created_at).toLocaleDateString()}</li>
                                </ul>
                                
                                <h6>Contact Information:</h6>
                                <p><strong>Posted by:</strong> ${item.posted_by || 'Anonymous'}</p>
                            </div>
                        </div>
                    `;
                    
                    // Store item ID for contact
                    document.getElementById('contactOwnerBtn').setAttribute('data-item-id', itemId);
                    
                    const modal = new bootstrap.Modal(document.getElementById('itemDetailModal'));
                    modal.show();
                } else {
                    showError(data.error || 'Failed to load item details');
                }
            } catch (error) {
                console.error('Error loading item details:', error);
                showError('Network error. Please try again.');
            }
        }

        // Show error message
        function showError(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
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

        // Contact owner functionality
        function openContactModal() {
            // Check if user is logged in first
            fetch('api/check_session.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Please login to contact the item owner.');
                        window.location.href = 'login.html';
                        return;
                    }
                    
                    const itemId = document.getElementById('contactOwnerBtn').getAttribute('data-item-id');
                    if (itemId) {
                        // Redirect to messages page with contact modal
                        window.location.href = `messages.php?contact=${itemId}`;
                    }
                })
                .catch(error => {
                    console.error('Auth check error:', error);
                    alert('Please login to contact the item owner.');
                    window.location.href = 'login.html';
                });
        }
    </script>
</body>
</html>
