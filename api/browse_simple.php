<?php
/**
 * Simple Browse Items API for Testing
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once '../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();

    // Simple query to get approved items
    $query = "SELECT i.*, u.username as posted_by
              FROM items i 
              LEFT JOIN users u ON i.user_id = u.id 
              WHERE i.is_approved = 1 
              ORDER BY i.created_at DESC 
              LIMIT 12";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process items for safe display
    foreach ($items as &$item) {
        $item['title'] = htmlspecialchars($item['title']);
        $item['description'] = htmlspecialchars($item['description']);
        $item['location'] = htmlspecialchars($item['location']);
        $item['image_url'] = $item['image_path']; // Use image_path as image_url
        unset($item['contact_email']);
        unset($item['contact_phone']);
    }

    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => count($items),
        'page' => 1,
        'totalPages' => 1,
        'limit' => 12
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Simple test error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
