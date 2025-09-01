<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeKeep - API Test</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center mb-4">SafeKeep API Testing</h1>
            </div>
        </div>

        <div class="row">
            <!-- Registration Test -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Test Registration API</h5>
                    </div>
                    <div class="card-body">
                        <form id="registerForm">
                            <div class="mb-3">
                                <label for="regUsername" class="form-label">Username</label>
                                <input type="text" class="form-control" id="regUsername" value="testuser" required>
                            </div>
                            <div class="mb-3">
                                <label for="regEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="regEmail" value="test@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="regPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="regPassword" value="password123" required>
                            </div>
                            <div class="mb-3">
                                <label for="regRole" class="form-label">Role</label>
                                <select class="form-control" id="regRole" required>
                                    <option value="student">Student</option>
                                    <option value="staff">Staff</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Test Registration</button>
                        </form>
                        <div id="registerResult" class="mt-3"></div>
                    </div>
                </div>
            </div>

            <!-- Login Test -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Test Login API</h5>
                    </div>
                    <div class="card-body">
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="loginEmail" value="test@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="loginPassword" value="password123" required>
                            </div>
                            <button type="submit" class="btn btn-success">Test Login</button>
                            <button type="button" class="btn btn-warning" id="logoutBtn">Test Logout</button>
                        </form>
                        <div id="loginResult" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>API Response Log</h5>
                    </div>
                    <div class="card-body">
                        <div id="apiLog" style="height: 300px; overflow-y: auto; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                            <p><strong>API Test Results will appear here...</strong></p>
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" onclick="clearLog()">Clear Log</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function logMessage(message, type = 'info') {
            const log = document.getElementById('apiLog');
            const timestamp = new Date().toLocaleTimeString();
            const alertClass = type === 'error' ? 'alert-danger' : type === 'success' ? 'alert-success' : 'alert-info';
            
            log.innerHTML += `
                <div class="alert ${alertClass} alert-sm mb-2">
                    <small><strong>[${timestamp}]</strong> ${message}</small>
                </div>
            `;
            log.scrollTop = log.scrollHeight;
        }

        function clearLog() {
            document.getElementById('apiLog').innerHTML = '<p><strong>API Test Results will appear here...</strong></p>';
        }

        // Registration Form
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                username: document.getElementById('regUsername').value,
                email: document.getElementById('regEmail').value,
                password: document.getElementById('regPassword').value,
                role: document.getElementById('regRole').value
            };

            logMessage('Testing Registration API...', 'info');
            
            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                const resultDiv = document.getElementById('registerResult');
                
                if (response.ok) {
                    resultDiv.innerHTML = `<div class="alert alert-success">✅ ${result.message}</div>`;
                    logMessage(`Registration Success: ${result.message}`, 'success');
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger">❌ ${result.error}</div>`;
                    logMessage(`Registration Error: ${result.error}`, 'error');
                }
                
                logMessage(`Response Status: ${response.status}`, 'info');
                logMessage(`Response Data: ${JSON.stringify(result)}`, 'info');
                
            } catch (error) {
                const resultDiv = document.getElementById('registerResult');
                resultDiv.innerHTML = `<div class="alert alert-danger">❌ Network Error: ${error.message}</div>`;
                logMessage(`Network Error: ${error.message}`, 'error');
            }
        });

        // Login Form
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                email: document.getElementById('loginEmail').value,
                password: document.getElementById('loginPassword').value
            };

            logMessage('Testing Login API...', 'info');
            
            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                const resultDiv = document.getElementById('loginResult');
                
                if (response.ok) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            ✅ ${result.message}<br>
                            <small>User: ${result.user.username} (${result.user.role})</small>
                        </div>
                    `;
                    logMessage(`Login Success: ${result.message}`, 'success');
                    logMessage(`User Data: ${JSON.stringify(result.user)}`, 'info');
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger">❌ ${result.error}</div>`;
                    logMessage(`Login Error: ${result.error}`, 'error');
                }
                
                logMessage(`Response Status: ${response.status}`, 'info');
                
            } catch (error) {
                const resultDiv = document.getElementById('loginResult');
                resultDiv.innerHTML = `<div class="alert alert-danger">❌ Network Error: ${error.message}</div>`;
                logMessage(`Network Error: ${error.message}`, 'error');
            }
        });

        // Logout Button
        document.getElementById('logoutBtn').addEventListener('click', async function() {
            logMessage('Testing Logout API...', 'info');
            
            try {
                const response = await fetch('api/logout.php', {
                    method: 'POST'
                });

                const result = await response.json();
                const resultDiv = document.getElementById('loginResult');
                
                if (response.ok) {
                    resultDiv.innerHTML = `<div class="alert alert-info">✅ ${result.message}</div>`;
                    logMessage(`Logout Success: ${result.message}`, 'success');
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger">❌ ${result.error}</div>`;
                    logMessage(`Logout Error: ${result.error}`, 'error');
                }
                
            } catch (error) {
                const resultDiv = document.getElementById('loginResult');
                resultDiv.innerHTML = `<div class="alert alert-danger">❌ Network Error: ${error.message}</div>`;
                logMessage(`Network Error: ${error.message}`, 'error');
            }
        });

        // Load initial message
        logMessage('API Test Page Loaded. Ready to test SafeKeep APIs!', 'success');
    </script>
</body>
</html>
