<?php
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

try {
    // Ensure session is started
    SessionManager::startSession();
    
    if (SessionManager::isLoggedIn() && SessionManager::getUserId()) {
        $database = new Database();
        $db = $database->getConnection();
        
        $stmt = $db->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
        $stmt->execute([SessionManager::getUserId()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } else {
            // User not found in database, destroy session
            SessionManager::logout();
            echo json_encode(['success' => false, 'error' => 'User not found']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
