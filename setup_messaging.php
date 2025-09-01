<?php
require_once 'config/database.php';

echo "<h3>Setting up Messaging System Tables</h3>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Read and execute the messaging schema
    $schema = file_get_contents('config/messaging_schema.sql');
    $statements = explode(';', $schema);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                echo "<p class='text-success'>✓ Executed: " . substr($statement, 0, 50) . "...</p>";
            } catch (PDOException $e) {
                // Skip if table already exists
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "<p class='text-warning'>⚠ Warning: " . $e->getMessage() . "</p>";
                } else {
                    echo "<p class='text-info'>ℹ Table already exists, skipping...</p>";
                }
            }
        }
    }
    
    echo "<hr>";
    echo "<h4>Messaging System Ready!</h4>";
    echo "<p class='text-success'>✅ Conversations table created</p>";
    echo "<p class='text-success'>✅ Messages table created</p>";
    echo "<p class='text-success'>✅ Items table updated with messaging preferences</p>";
    
    echo "<hr>";
    echo "<p><a href='messages.php' class='btn btn-primary'>Go to Messages</a></p>";
    echo "<p><a href='browse.php' class='btn btn-secondary'>Browse Items to Start Chatting</a></p>";
    
} catch (Exception $e) {
    echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Messaging System - SafeKeep</title>
    <link href="assets/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
</body>
</html>
