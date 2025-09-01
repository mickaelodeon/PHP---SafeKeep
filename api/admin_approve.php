<?php
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

// Start session and check admin access
SessionManager::startSession();

if (!SessionManager::isLoggedIn() || SessionManager::getUserRole() !== 'admin') {
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

if (!isset($input['item_id']) || !isset($input['action'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$item_id = (int)$input['item_id'];
$action = $input['action'];

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Validate action
    if (!in_array($action, ['approve', 'reject'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        exit;
    }
    
    // Set approval status
    $approval_status = ($action === 'approve') ? 1 : -1;
    
    // Update item approval status
    $stmt = $db->prepare("UPDATE items SET is_approved = ? WHERE id = ?");
    $success = $stmt->execute([$approval_status, $item_id]);
    
    if ($success && $stmt->rowCount() > 0) {
        // Get item details for logging
        $stmt = $db->prepare("SELECT title FROM items WHERE id = ?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => "Item '{$item['title']}' has been " . ($action === 'approve' ? 'approved' : 'rejected')
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Item not found or no changes made']);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
