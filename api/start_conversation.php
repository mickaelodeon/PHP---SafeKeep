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

if (!isset($input['item_id']) || !isset($input['subject']) || !isset($input['message'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$item_id = (int)$input['item_id'];
$subject = trim($input['subject']);
$message = trim($input['message']);

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Message cannot be empty']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $user_id = $_SESSION['user_id'];
    
    // Get item details and owner
    $item_query = "
        SELECT i.*, u.username as owner_name 
        FROM items i 
        JOIN users u ON i.user_id = u.id 
        WHERE i.id = ? AND i.is_approved = 1
    ";
    $item_stmt = $db->prepare($item_query);
    $item_stmt->execute([$item_id]);
    $item = $item_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$item) {
        echo json_encode(['success' => false, 'error' => 'Item not found or not approved']);
        exit;
    }
    
    // Can't message yourself
    if ($item['user_id'] == $user_id) {
        echo json_encode(['success' => false, 'error' => 'Cannot message yourself']);
        exit;
    }
    
    // Check if conversation already exists
    $conv_check = "
        SELECT id FROM conversations 
        WHERE item_id = ? 
        AND ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?))
    ";
    $conv_stmt = $db->prepare($conv_check);
    $conv_stmt->execute([$item_id, $user_id, $item['user_id'], $item['user_id'], $user_id]);
    $existing_conv = $conv_stmt->fetch();
    
    if ($existing_conv) {
        $conversation_id = $existing_conv['id'];
    } else {
        // Create new conversation
        $create_conv = "
            INSERT INTO conversations (item_id, user1_id, user2_id) 
            VALUES (?, ?, ?)
        ";
        $create_stmt = $db->prepare($create_conv);
        $create_stmt->execute([$item_id, $user_id, $item['user_id']]);
        $conversation_id = $db->lastInsertId();
    }
    
    // Create subject-prefixed message
    $full_message = "**Subject: " . $subject . "**\n\n" . $message;
    
    // Insert the message
    $insert_query = "
        INSERT INTO messages (conversation_id, sender_id, message) 
        VALUES (?, ?, ?)
    ";
    $insert_stmt = $db->prepare($insert_query);
    $success = $insert_stmt->execute([$conversation_id, $user_id, $full_message]);
    
    if ($success) {
        // Update conversation timestamp
        $update_conv = "UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $db->prepare($update_conv)->execute([$conversation_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully',
            'conversation_id' => $conversation_id,
            'other_user' => $item['owner_name'],
            'item_title' => $item['title']
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
