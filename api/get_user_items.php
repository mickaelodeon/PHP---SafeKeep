<?php
/**
 * Get User Items API
 * Retrieve items posted by the current user
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/database.php';
require_once '../classes/Item.php';
require_once '../includes/session.php';

try {
    // Check if user is logged in
    if (!SessionManager::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit();
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }

    // Get user's items
    $item = new Item($db);
    $items = $item->getItemsByUser(SessionManager::getUserId());

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => count($items)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
