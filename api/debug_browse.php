<?php
/**
 * Debug Browse Items API
 */

header('Content-Type: text/plain');

echo "=== DEBUG BROWSE API ===\n\n";

// Test 1: Basic PHP
echo "1. PHP is working\n";

// Test 2: File includes
echo "2. Testing file includes...\n";
try {
    require_once '../config/database.php';
    echo "   ✅ Database config loaded\n";
} catch (Exception $e) {
    echo "   ❌ Database config error: " . $e->getMessage() . "\n";
    exit;
}

try {
    require_once '../classes/Item.php';
    echo "   ✅ Item class loaded\n";
} catch (Exception $e) {
    echo "   ❌ Item class error: " . $e->getMessage() . "\n";
    exit;
}

// Test 3: Database connection
echo "3. Testing database connection...\n";
try {
    $database = new Database();
    $db = $database->getConnection();
    echo "   ✅ Database connected\n";
} catch (Exception $e) {
    echo "   ❌ Database connection error: " . $e->getMessage() . "\n";
    exit;
}

// Test 4: Simple query
echo "4. Testing simple query...\n";
try {
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM items");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✅ Query successful - " . $result['count'] . " items found\n";
} catch (Exception $e) {
    echo "   ❌ Query error: " . $e->getMessage() . "\n";
    exit;
}

// Test 5: Complex query (like browse API)
echo "5. Testing browse query...\n";
try {
    $whereConditions = ["i.is_approved = 1"];
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    $orderClause = 'ORDER BY i.date_lost_found DESC, i.created_at DESC';
    
    $query = "SELECT i.*, u.username as posted_by,
                     CASE 
                        WHEN i.image_path IS NOT NULL AND i.image_path != '' 
                        THEN i.image_path 
                        ELSE NULL 
                     END as image_url
              FROM items i 
              LEFT JOIN users u ON i.user_id = u.id 
              $whereClause 
              $orderClause 
              LIMIT 5";
              
    $stmt = $db->prepare($query);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   ✅ Browse query successful - " . count($items) . " items retrieved\n";
    
    if (count($items) > 0) {
        echo "   Sample item: " . $items[0]['title'] . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Browse query error: " . $e->getMessage() . "\n";
    echo "   SQL State: " . $e->getCode() . "\n";
    echo "   File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
?>
