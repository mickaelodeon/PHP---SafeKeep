<?php
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Admin access required']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $status = $_GET['status'] ?? 'all';
    $filter = $_GET['filter'] ?? 'all';
    
    // Build query based on parameters
    $whereClause = '';
    $params = [];
    
    if ($status === 'pending') {
        $whereClause = 'WHERE i.is_approved = 0';
    } elseif ($filter === 'approved') {
        $whereClause = 'WHERE i.is_approved = 1';
    } elseif ($filter === 'rejected') {
        $whereClause = 'WHERE i.is_approved = -1';
    }
    
    $query = "
        SELECT 
            i.*,
            u.username as posted_by
        FROM items i
        LEFT JOIN users u ON i.user_id = u.id
        $whereClause
        ORDER BY i.created_at DESC
        LIMIT 50
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'items' => $items
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
