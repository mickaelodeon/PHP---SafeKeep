<?php
session_start();

echo "<h3>Session Debug</h3>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['user_id'])) {
    require_once 'config/database.php';
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h4>User Data from Database:</h4>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>No user_id in session</p>";
}
?>
