<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeKeep - Login Test</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Login Test</h4>
                    </div>
                    <div class="card-body">
                        <form id="testLoginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="admin@safekeep.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" value="admin123" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Test Login</button>
                        </form>
                        
                        <div id="result" class="mt-3"></div>
                        
                        <div class="mt-4">
                            <h6>Debug Information:</h6>
                            <div id="debugInfo" class="bg-light p-3 rounded">
                                <small id="debugText">Click "Test Login" to see debug info...</small>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="login.html" class="btn btn-secondary">← Back to Login</a>
                            <a href="debug.php" class="btn btn-info">Database Debug</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('testLoginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const resultDiv = document.getElementById('result');
            const debugDiv = document.getElementById('debugText');
            
            // Clear previous results
            resultDiv.innerHTML = '';
            debugDiv.innerHTML = 'Testing login...';
            
            try {
                debugDiv.innerHTML += '<br>→ Sending request to api/login.php...';
                
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });
                
                debugDiv.innerHTML += '<br>→ Response status: ' + response.status;
                debugDiv.innerHTML += '<br>→ Response headers: ' + response.headers.get('content-type');
                
                const data = await response.json();
                debugDiv.innerHTML += '<br>→ Response data: ' + JSON.stringify(data, null, 2);
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <h5>✅ Login Successful!</h5>
                            <p><strong>User:</strong> ${data.user.username}</p>
                            <p><strong>Role:</strong> ${data.user.role}</p>
                            <p><strong>Email:</strong> ${data.user.email}</p>
                            <button class="btn btn-primary" onclick="testRedirect()">Test Redirect to Dashboard</button>
                        </div>
                    `;
                    
                    // Store user info
                    localStorage.setItem('username', data.user.username);
                    localStorage.setItem('userRole', data.user.role);
                    
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <h5>❌ Login Failed</h5>
                            <p>${data.error || 'Unknown error'}</p>
                        </div>
                    `;
                }
            } catch (error) {
                debugDiv.innerHTML += '<br>→ JavaScript Error: ' + error.message;
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>❌ Network Error</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        });
        
        function testRedirect() {
            document.getElementById('debugText').innerHTML += '<br>→ Testing redirect to dashboard.php...';
            window.location.href = 'dashboard.php';
        }
        
        // Test if dashboard is accessible directly
        async function testDashboard() {
            try {
                const response = await fetch('dashboard.php');
                const text = await response.text();
                console.log('Dashboard response:', response.status, text.substring(0, 100));
            } catch (error) {
                console.error('Dashboard test error:', error);
            }
        }
        
        // Run dashboard test on page load
        testDashboard();
    </script>
</body>
</html>
