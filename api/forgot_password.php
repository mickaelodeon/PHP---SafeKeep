<?php
/**
 * Password Reset Request API
 * Handles forgot password requests and sends reset emails
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
    if (empty($data['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email is required']);
        exit();
    }

    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
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

    // Check if email exists
    $stmt = $db->prepare("SELECT id, username, email FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Don't reveal if email exists or not for security
        echo json_encode([
            'success' => true,
            'message' => 'If the email exists, a password reset link has been sent.'
        ]);
        exit();
    }

    // Generate reset token
    $reset_token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store reset token in database
    $stmt = $db->prepare("
        INSERT INTO password_resets (user_id, email, token, expires_at) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        token = VALUES(token), 
        expires_at = VALUES(expires_at),
        created_at = CURRENT_TIMESTAMP
    ");
    
    $success = $stmt->execute([$user['id'], $user['email'], $reset_token, $expires_at]);

    if (!$success) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to generate reset token']);
        exit();
    }

    // Send email (simplified for now - in production, use proper email service)
    $reset_link = "http://localhost/safekeep/reset_password.html?token=" . $reset_token;
    
    // For now, we'll just log the reset link (in production, send actual email)
    error_log("Password reset link for {$user['email']}: $reset_link");
    
    // Simple email simulation (you can replace this with actual email sending)
    $to = $user['email'];
    $subject = "SafeKeep - Password Reset Request";
    $message = "
        Hello {$user['username']},
        
        You have requested to reset your password for SafeKeep.
        
        Click the link below to reset your password:
        $reset_link
        
        This link will expire in 1 hour.
        
        If you did not request this reset, please ignore this email.
        
        Best regards,
        SafeKeep Team
    ";
    
    $headers = "From: noreply@safekeep.com\r\n";
    $headers .= "Reply-To: noreply@safekeep.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Attempt to send email (this may not work in local development)
    $email_sent = @mail($to, $subject, $message, $headers);
    
    echo json_encode([
        'success' => true,
        'message' => 'If the email exists, a password reset link has been sent.',
        'debug_info' => [
            'email_sent' => $email_sent,
            'reset_link' => $reset_link // Remove this in production
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
