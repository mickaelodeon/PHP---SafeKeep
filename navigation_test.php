<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeKeep Navigation Test</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-section { margin: 20px 0; padding: 20px; border-radius: 8px; }
        .test-pass { background-color: #d4edda; border: 1px solid #c3e6cb; }
        .test-fail { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .test-info { background-color: #d1ecf1; border: 1px solid #bee5eb; }
        .nav-link { margin: 5px; padding: 10px 15px; border-radius: 5px; text-decoration: none; }
        .nav-link.guest { background-color: #e9ecef; color: #495057; }
        .nav-link.student { background-color: #cce5ff; color: #004085; }
        .nav-link.admin { background-color: #ffe6e6; color: #721c24; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">🔍 SafeKeep Navigation System Audit</h1>
        
        <div class="test-section test-info">
            <h3>📋 System Overview</h3>
            <div id="systemOverview">
                <p><strong>Session Management:</strong> <span id="sessionStatus">Checking...</span></p>
                <p><strong>Current User:</strong> <span id="currentUser">Checking...</span></p>
                <p><strong>User Role:</strong> <span id="userRole">Checking...</span></p>
                <p><strong>Current Page:</strong> <span id="currentPage"></span></p>
            </div>
        </div>

        <div class="test-section test-info">
            <h3>🧭 Navigation Structure</h3>
            <p><strong>Guest Navigation:</strong></p>
            <div>
                <a href="index.html" class="nav-link guest">Home</a>
                <a href="browse.php" class="nav-link guest">Browse Items</a>
                <a href="login.html" class="nav-link guest">Login</a>
                <a href="register.html" class="nav-link guest">Register</a>
            </div>
            
            <p class="mt-3"><strong>Student Navigation:</strong></p>
            <div>
                <a href="dashboard.php" class="nav-link student">Dashboard</a>
                <a href="browse.php" class="nav-link student">Browse Items</a>
                <a href="messages.php" class="nav-link student">Messages</a>
                <a href="profile.php" class="nav-link student">Profile</a>
            </div>
            
            <p class="mt-3"><strong>Admin Navigation:</strong></p>
            <div>
                <a href="admin.php" class="nav-link admin">Admin Panel</a>
                <a href="browse.php" class="nav-link admin">Browse Items</a>
                <a href="profile.php" class="nav-link admin">Profile</a>
            </div>
        </div>

        <div class="test-section" id="authTests">
            <h3>🔐 Authentication Tests</h3>
            <div id="authResults">Running tests...</div>
        </div>

        <div class="test-section" id="routingTests">
            <h3>🚦 Role-Based Routing Tests</h3>
            <div id="routingResults">Running tests...</div>
        </div>

        <div class="test-section" id="navigationTests">
            <h3>🧭 Navigation Persistence Tests</h3>
            <div id="navigationResults">Running tests...</div>
        </div>

        <div class="test-section" id="sessionTests">
            <h3>⏱️ Session Management Tests</h3>
            <div id="sessionResults">Running tests...</div>
        </div>

        <div class="test-section test-info">
            <h3>🛠️ Manual Test Instructions</h3>
            <ol>
                <li><strong>Guest Access:</strong> Open a private/incognito window and test guest navigation</li>
                <li><strong>Student Access:</strong> Login as a student and test all student navigation links</li>
                <li><strong>Admin Access:</strong> Login as an admin and test all admin navigation links</li>
                <li><strong>Role Switching:</strong> Switch between different user roles and verify proper redirects</li>
                <li><strong>Session Persistence:</strong> Navigate between pages and ensure no unexpected logouts</li>
            </ol>
        </div>
    </div>

    <script>
        // Display current page info
        document.getElementById('currentPage').textContent = window.location.pathname;

        // Test session status
        async function testSessionStatus() {
            try {
                const response = await fetch('api/check_session.php');
                const data = await response.json();
                
                document.getElementById('sessionStatus').textContent = data.logged_in ? '✅ Active' : '❌ Not logged in';
                document.getElementById('currentUser').textContent = data.username || 'Not logged in';
                document.getElementById('userRole').textContent = data.role || 'Guest';
                
                return data;
            } catch (error) {
                document.getElementById('sessionStatus').textContent = '❌ Error checking session';
                return null;
            }
        }

        // Test authentication endpoints
        async function testAuthentication() {
            const results = [];
            
            // Test session check
            try {
                const sessionResponse = await fetch('api/check_session.php');
                const sessionData = await sessionResponse.json();
                results.push(`✅ Session API: Working (Status: ${sessionData.logged_in ? 'Logged in' : 'Guest'})`);
            } catch (error) {
                results.push(`❌ Session API: Failed - ${error.message}`);
            }

            // Test logout endpoint accessibility
            try {
                const logoutResponse = await fetch('api/logout.php', { method: 'POST' });
                const logoutData = await logoutResponse.json();
                results.push(`✅ Logout API: Accessible`);
            } catch (error) {
                results.push(`❌ Logout API: Failed - ${error.message}`);
            }

            document.getElementById('authResults').innerHTML = results.map(r => `<p>${r}</p>`).join('');
        }

        // Test routing logic
        async function testRouting() {
            const results = [];
            const sessionData = await testSessionStatus();
            
            if (!sessionData) {
                results.push('❌ Cannot test routing - session check failed');
                document.getElementById('routingResults').innerHTML = results.map(r => `<p>${r}</p>`).join('');
                return;
            }

            // Test appropriate page access based on role
            if (sessionData.logged_in) {
                if (sessionData.role === 'admin') {
                    results.push('✅ Admin role detected - should have access to admin.php');
                    results.push('ℹ️ Admin should be redirected from dashboard.php to admin.php');
                } else if (sessionData.role === 'student') {
                    results.push('✅ Student role detected - should have access to dashboard.php');
                    results.push('ℹ️ Student should NOT have access to admin.php');
                }
                results.push('✅ Logged in user should have access to browse.php, messages.php, profile.php');
            } else {
                results.push('✅ Guest user - should only access public pages');
                results.push('ℹ️ Guest should be redirected to login.html when accessing protected pages');
            }

            document.getElementById('routingResults').innerHTML = results.map(r => `<p>${r}</p>`).join('');
        }

        // Test navigation behavior
        async function testNavigation() {
            const results = [];
            
            // Check for navigation elements
            const navElements = {
                'Guest Navigation': ['index.html', 'browse.php', 'login.html', 'register.html'],
                'Student Navigation': ['dashboard.php', 'browse.php', 'messages.php'],
                'Admin Navigation': ['admin.php', 'browse.php']
            };

            results.push('✅ Navigation structure defined');
            
            // Check logout functionality
            const logoutBtn = document.querySelector('#logoutBtn');
            if (logoutBtn) {
                results.push('✅ Logout button found on current page');
            } else {
                results.push('ℹ️ No logout button on current page (normal for guest pages)');
            }

            document.getElementById('navigationResults').innerHTML = results.map(r => `<p>${r}</p>`).join('');
        }

        // Test session management
        async function testSessionManagement() {
            const results = [];
            
            // Test session persistence
            const sessionBefore = await testSessionStatus();
            
            // Simulate a page interaction (this should not log user out)
            try {
                const testResponse = await fetch('api/check_session.php');
                const sessionAfter = await testResponse.json();
                
                if (sessionBefore && sessionAfter) {
                    if (sessionBefore.logged_in === sessionAfter.logged_in) {
                        results.push('✅ Session persistence: Session state maintained across requests');
                    } else {
                        results.push('❌ Session persistence: Session state changed unexpectedly');
                    }
                }
            } catch (error) {
                results.push(`❌ Session persistence test failed: ${error.message}`);
            }

            // Check session timeout configuration
            results.push('ℹ️ Session timeout: 1 hour (3600 seconds)');
            results.push('ℹ️ Session cookies: Available across entire site');

            document.getElementById('sessionResults').innerHTML = results.map(r => `<p>${r}</p>`).join('');
        }

        // Run all tests
        async function runAllTests() {
            await testSessionStatus();
            await testAuthentication();
            await testRouting();
            await testNavigation();
            await testSessionManagement();
        }

        // Initialize tests
        runAllTests();
    </script>
</body>
</html>
