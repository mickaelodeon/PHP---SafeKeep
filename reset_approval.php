<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h3>Setting Items to Pending Approval Status</h3>";
    
    // Set all items to pending approval (is_approved = 0) except the first 2
    $stmt = $db->prepare("UPDATE items SET is_approved = 0 WHERE id > 2");
    $success = $stmt->execute();
    
    if ($success) {
        $affected_rows = $stmt->rowCount();
        echo "<p class='text-success'>✓ Set $affected_rows items to pending approval status</p>";
    } else {
        echo "<p class='text-danger'>✗ Failed to update items</p>";
    }
    
    // Keep first 2 items approved for testing
    $stmt = $db->prepare("UPDATE items SET is_approved = 1 WHERE id <= 2");
    $stmt->execute();
    
    echo "<p class='text-info'>ℹ First 2 items kept as approved for testing</p>";
    
    // Show current status
    echo "<h4>Current Item Status:</h4>";
    $stmt = $db->prepare("
        SELECT 
            i.id, 
            i.title, 
            i.is_approved,
            CASE 
                WHEN i.is_approved = 1 THEN 'Approved'
                WHEN i.is_approved = 0 THEN 'Pending'
                ELSE 'Rejected'
            END as status_text
        FROM items i 
        ORDER BY i.id
    ");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table class='table table-striped'>";
    echo "<tr><th>ID</th><th>Title</th><th>Status</th></tr>";
    foreach ($items as $item) {
        $statusClass = $item['is_approved'] == 1 ? 'success' : ($item['is_approved'] == 0 ? 'warning' : 'danger');
        echo "<tr><td>{$item['id']}</td><td>{$item['title']}</td><td><span class='badge bg-{$statusClass}'>{$item['status_text']}</span></td></tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<p><a href='admin.php' class='btn btn-primary'>Go to Admin Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Item Approval Status - SafeKeep</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
</body>
</html>
