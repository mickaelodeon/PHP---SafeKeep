<?php
/**
 * Get Item Details API
 * Returns detailed information about a specific item
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
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

    // Get item details with user information
    $query = "SELECT i.*, u.username as posted_by
              FROM items i 
              LEFT JOIN users u ON i.user_id = u.id 
              WHERE i.id = ? AND i.is_approved = 1";

    $stmt = $db->prepare($query);
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Item not found or not approved'
        ]);
        exit;
    }

    // Process item data for display
    $item['title'] = htmlspecialchars($item['title']);
    $item['description'] = htmlspecialchars($item['description']);
    $item['location'] = htmlspecialchars($item['location']);
    $item['posted_by'] = htmlspecialchars($item['posted_by'] ?? 'Anonymous');
    
    // Format dates
    $item['date_lost_found'] = date('Y-m-d', strtotime($item['date_lost_found']));
    $item['created_at'] = date('Y-m-d H:i:s', strtotime($item['created_at']));
    $item['updated_at'] = date('Y-m-d H:i:s', strtotime($item['updated_at']));

    // Handle image URL
    $item['image_url'] = $item['image_path'];
    if (empty($item['image_url'])) {
        $item['image_url'] = null;
    }
    unset($item['image_path']); // Remove the original field

    // Remove sensitive information for public view
    unset($item['user_id']);
    
    // Contact information is only shown if user is logged in
    // For now, we'll hide it for public browsing
    $item['contact_email'] = null;
    $item['contact_phone'] = null;

    echo json_encode([
        'success' => true,
        'item' => $item
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>
