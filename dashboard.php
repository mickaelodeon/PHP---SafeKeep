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

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- Student Navigation Only -->
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="browse.php">Browse Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messages.php">Messages</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <span id="username"><?= htmlspecialchars($username) ?></span>
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

    <div class="container mt-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-primary text-white p-4 rounded">
                    <h2>Welcome to SafeKeep Dashboard</h2>
                    <p class="mb-0">Manage your lost and found items, search for missing belongings, and help others recover their items.</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-plus-circle text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Report Lost Item</h5>
                        <p class="card-text">Lost something? Post details to help others find it for you.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postItemModal" onclick="setItemType('lost')">
                            <i class="bi bi-search"></i> Report Lost
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Report Found Item</h5>
                        <p class="card-text">Found something? Help return it to its rightful owner.</p>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#postItemModal" onclick="setItemType('found')">
                            <i class="bi bi-gift"></i> Report Found
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- User's Items -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul"></i> My Posts</h5>
                    </div>
                    <div class="card-body">
                        <div id="userItemsContainer">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
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
                    <h5 class="modal-title" id="modalTitle">Post Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="postItemForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Electronics">Electronics</option>
                                        <option value="Clothing">Clothing</option>
                                        <option value="Books & Stationery">Books & Stationery</option>
                                        <option value="Sports Equipment">Sports Equipment</option>
                                        <option value="Personal Items">Personal Items</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Provide detailed description to help identify the item..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location *</label>
                                    <input type="text" class="form-control" id="location" name="location" required placeholder="Where was it lost/found?">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_lost_found" class="form-label">Date *</label>
                                    <input type="date" class="form-control" id="date_lost_found" name="date_lost_found" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label">Contact Email</label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" placeholder="Leave empty to use your account email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_phone" class="form-label">Contact Phone</label>
                                    <input type="tel" class="form-control" id="contact_phone" name="contact_phone" placeholder="Optional">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Photo (Optional)</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Max file size: 5MB. Formats: JPG, PNG, GIF, WebP</div>
                        </div>

                        <input type="hidden" id="status" name="status" value="lost">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Post Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Check if user is logged in
        async function checkAuth() {
            try {
                // Check session and get user data
                const response = await fetch('api/check_session.php');
                const data = await response.json();
                
                if (data.success && data.user) {
                    document.getElementById('username').textContent = data.user.username;
                    
                    // Show admin menu if user is admin
                    if (data.user.role === 'admin') {
                        document.getElementById('adminMenuItem').style.display = 'block';
                    }
                } else {
                    // Fallback to localStorage for now
                    const username = localStorage.getItem('username') || 'User';
                    document.getElementById('username').textContent = username;
                }
            } catch (error) {
                console.error('Auth check error:', error);
                const username = localStorage.getItem('username') || 'User';
                document.getElementById('username').textContent = username;
            }
        }

        // Set item type (lost/found)
        function setItemType(type) {
            document.getElementById('status').value = type;
            document.getElementById('modalTitle').textContent = type === 'lost' ? 'Report Lost Item' : 'Report Found Item';
            document.getElementById('submitBtn').textContent = type === 'lost' ? 'Report Lost' : 'Report Found';
            document.getElementById('submitBtn').className = type === 'lost' ? 'btn btn-primary' : 'btn btn-success';
        }

        // Load user's items
        async function loadUserItems() {
            try {
                const response = await fetch('api/get_user_items.php');
                const data = await response.json();

                const container = document.getElementById('userItemsContainer');

                if (data.success && data.items.length > 0) {
                    container.innerHTML = '';
                    data.items.forEach(item => {
                        const statusBadge = item.status === 'lost' ? 'bg-danger' : 'bg-success';
                        const approvalBadge = item.is_approved === '1' ? 
                            '<span class="badge bg-success ms-2">Approved</span>' : 
                            '<span class="badge bg-warning ms-2">Pending Approval</span>';

                        container.innerHTML += `
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="card-title">
                                                ${item.title}
                                                <span class="badge ${statusBadge}">${item.status.toUpperCase()}</span>
                                                ${approvalBadge}
                                            </h6>
                                            <p class="card-text">${item.description.substring(0, 100)}...</p>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt"></i> ${item.location} | 
                                                <i class="bi bi-calendar"></i> ${new Date(item.date_lost_found).toLocaleDateString()}
                                            </small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <button class="btn btn-sm btn-outline-primary me-2">Edit</button>
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    container.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">No items posted yet</h5>
                            <p>Start by reporting a lost or found item using the buttons above.</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading items:', error);
                document.getElementById('userItemsContainer').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Failed to load items. Please try again.
                    </div>
                `;
            }
        }

        // Submit form
        document.getElementById('postItemForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.textContent;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Posting...';

            try {
                const response = await fetch('api/create_item.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('postItemModal'));
                    modal.hide();

                    // Reset form
                    this.reset();

                    // Show success message
                    showAlert('success', data.message);

                    // Reload items
                    loadUserItems();
                } else {
                    showAlert('danger', data.error || 'Failed to post item');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'Network error. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });

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
                    localStorage.removeItem('username');
                    window.location.href = 'login.html';
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        });

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadUserItems();
            
            // Set max date to today
            document.getElementById('date_lost_found').max = new Date().toISOString().split('T')[0];
        });
    </script>
</body>
</html>
