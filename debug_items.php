<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "=== ITEMS TABLE STRUCTURE ===\n\n";
    
    // Check items table structure
    $stmt = $conn->prepare("DESCRIBE items");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Items table columns:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    echo "\n=== CHECKING EXISTING ITEMS ===\n\n";
    
    // Check existing items
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM items");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Total items in database: " . $result['count'] . "\n";
    
    if ($result['count'] > 0) {
        echo "\nFirst few items:\n";
        $stmt = $conn->prepare("SELECT id, title, status, is_approved FROM items LIMIT 3");
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($items as $item) {
            echo "- ID: " . $item['id'] . " | Title: " . $item['title'] . " | Status: " . $item['status'] . " | Approved: " . $item['is_approved'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
