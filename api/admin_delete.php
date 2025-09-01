<?php
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Admin access required']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['item_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing item ID']);
    exit;
}

$item_id = (int)$input['item_id'];

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get item details before deletion for logging
    $stmt = $db->prepare("SELECT title, image_url FROM items WHERE id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$item) {
        echo json_encode(['success' => false, 'error' => 'Item not found']);
        exit;
    }
    
    // Delete associated image file if exists
    if (!empty($item['image_url']) && file_exists($item['image_url'])) {
        unlink($item['image_url']);
    }
    
    // Delete the item
    $stmt = $db->prepare("DELETE FROM items WHERE id = ?");
    $success = $stmt->execute([$item_id]);
    
    if ($success && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Item '{$item['title']}' has been permanently deleted"
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete item']);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
