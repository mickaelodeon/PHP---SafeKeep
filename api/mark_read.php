<?php
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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

if (!isset($input['conversation_id'])) {
    echo json_encode(['success' => false, 'error' => 'Conversation ID required']);
    exit;
}

$conversation_id = (int)$input['conversation_id'];

try {
    $database = new Database();
    $db = $database->getConnection();
    $user_id = $_SESSION['user_id'];
    
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
    
    // Mark all messages in this conversation as read (where current user is not the sender)
    $update_query = "
        UPDATE messages 
        SET is_read = TRUE 
        WHERE conversation_id = ? AND sender_id != ? AND is_read = FALSE
    ";
    $update_stmt = $db->prepare($update_query);
    $success = $update_stmt->execute([$conversation_id, $user_id]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Messages marked as read'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to mark as read']);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
