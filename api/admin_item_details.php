<?php
/**
 * Admin Get Item Details API
 * Returns detailed information about any item (approved or unapproved) for admin use
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../includes/session.php';

try {
    // Check if user is logged in and is admin
    if (!SessionManager::isLoggedIn() || SessionManager::getUserRole() !== 'admin') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Admin access required',
            'debug' => [
                'logged_in' => SessionManager::isLoggedIn(),
                'role' => SessionManager::getUserRole()
            ]
        ]);
        exit;
    }

    $database = new Database();
    $db = $database->getConnection();

    // Get item ID from query parameter
    $itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$itemId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Item ID is required'
        ]);
        exit;
    }

    // Get item details with user information (NO approval filter for admin)
    $query = "SELECT i.*, u.username as posted_by, u.email as user_email
              FROM items i 
              LEFT JOIN users u ON i.user_id = u.id 
              WHERE i.id = ?";

    $stmt = $db->prepare($query);
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Item not found'
        ]);
        exit;
    }

    // Process item data for display
    $item['title'] = htmlspecialchars($item['title']);
    $item['description'] = htmlspecialchars($item['description']);
    $item['category'] = htmlspecialchars($item['category']);
    $item['location'] = htmlspecialchars($item['location']);
    $item['contact_email'] = htmlspecialchars($item['contact_email']);
    $item['contact_phone'] = htmlspecialchars($item['contact_phone'] ?? '');

    // Format dates
    $item['date_lost_found'] = date('Y-m-d', strtotime($item['date_lost_found']));
    $item['created_at'] = date('Y-m-d H:i:s', strtotime($item['created_at']));
    $item['updated_at'] = date('Y-m-d H:i:s', strtotime($item['updated_at']));

    // Handle image path
    if ($item['image_path']) {
        $item['image_url'] = 'uploads/items/' . $item['image_path'];
    } else {
        $item['image_url'] = null;
    }

    // Add approval status info
    $item['approval_status'] = $item['is_approved'] ? 'Approved' : 'Pending Approval';

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'item' => $item
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
