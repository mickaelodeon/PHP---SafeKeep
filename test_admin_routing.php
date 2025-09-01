<?php
echo "<h2>Admin Dashboard Detection Test</h2>";
echo "<p><strong>This file is located at:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Current directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

if (file_exists('admin.php')) {
    $filesize = filesize('admin.php');
    $modified = date('Y-m-d H:i:s', filemtime('admin.php'));
    echo "<p><strong>admin.php exists:</strong> Yes</p>";
    echo "<p><strong>File size:</strong> " . number_format($filesize) . " bytes</p>";
    echo "<p><strong>Last modified:</strong> $modified</p>";
} else {
    echo "<p><strong>admin.php exists:</strong> No</p>";
}

echo "<hr>";
echo "<p><a href='admin.php' class='btn btn-primary'>Go to Admin Dashboard</a></p>";
echo "<p><a href='dashboard.php' class='btn btn-secondary'>Go to User Dashboard</a></p>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Detection Test</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
</body>
</html>
