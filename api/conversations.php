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

try {
    $database = new Database();
    $db = $database->getConnection();
    $user_id = $_SESSION['user_id'];
    
    // Get all conversations for the current user
    $query = "
        SELECT 
            c.id,
            c.item_id,
            c.created_at,
            c.updated_at,
            i.title as item_title,
            CASE 
                WHEN c.user1_id = ? THEN u2.username 
                ELSE u1.username 
            END as other_user,
            CASE 
                WHEN c.user1_id = ? THEN c.user2_id 
                ELSE c.user1_id 
            END as other_user_id,
            (
                SELECT m.message 
                FROM messages m 
                WHERE m.conversation_id = c.id 
                ORDER BY m.created_at DESC 
                LIMIT 1
            ) as last_message,
            (
                SELECT m.created_at 
                FROM messages m 
                WHERE m.conversation_id = c.id 
                ORDER BY m.created_at DESC 
                LIMIT 1
            ) as last_message_time,
            (
                SELECT COUNT(*) 
                FROM messages m 
                WHERE m.conversation_id = c.id 
                AND m.sender_id != ? 
                AND m.is_read = FALSE
            ) as unread_count
        FROM conversations c
        JOIN items i ON c.item_id = i.id
        JOIN users u1 ON c.user1_id = u1.id
        JOIN users u2 ON c.user2_id = u2.id
        WHERE c.user1_id = ? OR c.user2_id = ?
        ORDER BY c.updated_at DESC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'conversations' => $conversations
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
