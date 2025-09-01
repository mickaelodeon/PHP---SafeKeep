<?php
/**
 * Debug Admin Item Details API
 * Test version to debug the item details issue
 */

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/session.php';

try {
    // Debug session info
    $debug_info = [
        'session_started' => session_status() === PHP_SESSION_ACTIVE,
        'logged_in' => SessionManager::isLoggedIn(),
        'user_id' => SessionManager::getUserId(),
        'username' => SessionManager::getUsername(),
        'role' => SessionManager::getUserRole()
    ];

    // For now, let's skip admin check to see if the basic query works
    $database = new Database();
    $db = $database->getConnection();

    // Get item ID from query parameter
    $itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$itemId) {
        echo json_encode([
            'success' => false,
            'error' => 'Item ID is required',
            'debug' => $debug_info
        ]);
        exit;
    }

    // Get item details with user information (NO approval filter)
    $query = "SELECT i.*, u.username as posted_by, u.email as user_email
              FROM items i 
              LEFT JOIN users u ON i.user_id = u.id 
              WHERE i.id = ?";

    $stmt = $db->prepare($query);
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        echo json_encode([
            'success' => false,
            'error' => 'Item not found',
            'debug' => $debug_info,
            'query_id' => $itemId
        ]);
        exit;
    }

    // Process item data for display
    $item['title'] = htmlspecialchars($item['title']);
    $item['description'] = htmlspecialchars($item['description']);
    $item['approval_status'] = $item['is_approved'] ? 'Approved' : 'Pending Approval';

    // Handle image path
    if ($item['image_path']) {
        $item['image_url'] = 'uploads/items/' . $item['image_path'];
    } else {
        $item['image_url'] = null;
    }

    echo json_encode([
        'success' => true,
        'item' => $item,
        'debug' => $debug_info
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage(),
        'debug' => isset($debug_info) ? $debug_info : []
    ]);
}
?>
