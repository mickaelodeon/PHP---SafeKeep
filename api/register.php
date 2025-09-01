<?php
/**
 * Registration API
 * Handle user registration requests
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
require_once '../classes/User.php';

try {
    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);
    
    // If no JSON data, try form data
    if (!$data) {
        $data = $_POST;
    }

    // Validate required fields
    if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required']);
        exit();
    }

    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        exit();
    }

    // Validate password length
    if (strlen($data['password']) < 6) {
        http_response_code(400);
        echo json_encode(['error' => 'Password must be at least 6 characters long']);
        exit();
    }

    // Validate role
    $allowed_roles = ['student', 'staff', 'admin'];
    if (!in_array($data['role'], $allowed_roles)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid role selected']);
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

    // Create user object
    $user = new User($db);
    $user->username = $data['username'];
    $user->email = $data['email'];
    $user->password = $data['password'];
    $user->role = $data['role'];

    // Check if email already exists
    if ($user->emailExists()) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already exists']);
        exit();
    }

    // Check if username already exists
    if ($user->usernameExists()) {
        http_response_code(409);
        echo json_encode(['error' => 'Username already exists']);
        exit();
    }

    // Register user
    if ($user->register()) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Registration failed']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
