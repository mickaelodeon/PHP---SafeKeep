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
    
    $query = "
        SELECT 
            u.*,
            COUNT(i.id) as items_count
        FROM users u
        LEFT JOIN items i ON u.id = i.user_id
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
