<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get statistics
    $stats = [];
    
    // Pending items count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM items WHERE is_approved = 0");
    $stmt->execute();
    $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Approved items count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM items WHERE is_approved = 1");
    $stmt->execute();
    $stats['approved'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total users count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $stats['users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total items count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM items");
    $stmt->execute();
    $stats['total_items'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'message' => 'Test stats API without auth'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
