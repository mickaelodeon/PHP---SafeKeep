<?php
/**
 * Password Reset API
 * Handles password reset with valid token
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

try {
    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);
    
    // If no JSON data, try form data
    if (!$data) {
        $data = $_POST;
    }

    // Validate required fields
    if (empty($data['token']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Token and password are required']);
        exit();
    }

    // Validate password length
    if (strlen($data['password']) < 6) {
        http_response_code(400);
        echo json_encode(['error' => 'Password must be at least 6 characters long']);
        exit();
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }

    // Verify reset token
    $stmt = $db->prepare("
        SELECT pr.user_id, pr.email, u.username 
        FROM password_resets pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.token = ? AND pr.expires_at > NOW()
    ");
    $stmt->execute([$data['token']]);
    $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset_data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or expired reset token']);
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Update user password
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $success = $stmt->execute([$hashed_password, $reset_data['user_id']]);

    if (!$success) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update password']);
        exit();
    }

    // Delete the used reset token
    $stmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
    $stmt->execute([$data['token']]);

    echo json_encode([
        'success' => true,
        'message' => 'Password has been successfully reset. You can now login with your new password.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
