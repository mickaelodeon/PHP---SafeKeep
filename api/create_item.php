<?php
/**
 * Create Item API
 * Handle creating new lost/found items
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

require_once '../config/database.php';
require_once '../classes/Item.php';
require_once '../includes/session.php';
require_once '../includes/utils.php';

try {
    // Check if user is logged in
    if (!SessionManager::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit();
    }

    // Handle file upload and form data
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? '';
    $location = $_POST['location'] ?? '';
    $date_lost_found = $_POST['date_lost_found'] ?? '';
    $contact_email = $_POST['contact_email'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';

    // Validate required fields
    if (empty($title) || empty($description) || empty($category) || empty($status) || empty($location) || empty($date_lost_found)) {
        http_response_code(400);
        echo json_encode(['error' => 'All required fields must be filled']);
        exit();
    }

    // Validate status
    if (!in_array($status, ['lost', 'found'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid status. Must be lost or found']);
        exit();
    }

    // Validate date
    if (!strtotime($date_lost_found)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid date format']);
        exit();
    }

    // Handle image upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        try {
            // Create uploads directory if it doesn't exist
            $upload_dir = '../uploads/items/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $image_path = Utils::uploadFile($_FILES['image'], $upload_dir);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Image upload failed: ' . $e->getMessage()]);
            exit();
        }
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }

    // Create item object
    $item = new Item($db);
    $item->user_id = SessionManager::getUserId();
    $item->title = $title;
    $item->description = $description;
    $item->category = $category;
    $item->status = $status;
    $item->location = $location;
    $item->date_lost_found = $date_lost_found;
    $item->image_path = $image_path;
    $item->contact_email = $contact_email ?: SessionManager::getUsername() . '@school.edu';
    $item->contact_phone = $contact_phone;

    // Create item
    if ($item->create()) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Item posted successfully! It will be visible after admin approval.',
            'item_id' => $item->id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create item']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
