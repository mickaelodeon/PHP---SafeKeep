<?php
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

// Start session and check if user is logged in
SessionManager::startSession();

if (!SessionManager::isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['conversation_id']) || !isset($input['message'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$conversation_id = (int)$input['conversation_id'];
$message = trim($input['message']);

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Message cannot be empty']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $user_id = SessionManager::getUserId();
    
    // Verify user is part of this conversation
    $verify_query = "
        SELECT id FROM conversations 
        WHERE id = ? AND (user1_id = ? OR user2_id = ?)
    ";
    $verify_stmt = $db->prepare($verify_query);
    $verify_stmt->execute([$conversation_id, $user_id, $user_id]);
    
    if (!$verify_stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Access denied']);
        exit;
    }
    
    // Insert the message
    $insert_query = "
        INSERT INTO messages (conversation_id, sender_id, message) 
        VALUES (?, ?, ?)
    ";
    $insert_stmt = $db->prepare($insert_query);
    $success = $insert_stmt->execute([$conversation_id, $user_id, $message]);
    
    if ($success) {
        // Update conversation timestamp
        $update_conv = "UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $db->prepare($update_conv)->execute([$conversation_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to send message']);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
