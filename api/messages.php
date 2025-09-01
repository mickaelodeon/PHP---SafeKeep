<?php
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

// Check if user is logged in using SessionManager
if (!SessionManager::isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if (!isset($_GET['conversation_id'])) {
    echo json_encode(['success' => false, 'error' => 'Conversation ID required']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $user_id = SessionManager::getUserId();
    $conversation_id = (int)$_GET['conversation_id'];
    
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
    
    // Get messages for this conversation
    $query = "
        SELECT 
            m.id,
            m.message,
            m.created_at,
            m.sender_id,
            u.username as sender_name,
            CASE WHEN m.sender_id = ? THEN TRUE ELSE FALSE END as is_own
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.conversation_id = ?
        ORDER BY m.created_at ASC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id, $conversation_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
