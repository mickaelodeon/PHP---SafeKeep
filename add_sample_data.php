<?php
/**
 * Add Sample Items for Testing Browse Functionality
 */

require_once 'config/database.php';
require_once 'classes/Item.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $item = new Item($db);
    
    echo "Adding sample items for testing...\n\n";
    
    // Sample items data
    $sampleItems = [
        [
            'user_id' => 1, // Admin user
            'title' => 'Lost iPhone 14 Pro',
            'description' => 'Lost my black iPhone 14 Pro with a clear case. Has a small crack on the screen. Very important contacts inside!',
            'category' => 'Electronics',
            'status' => 'lost',
            'location' => 'University Library, 2nd Floor',
            'date_lost_found' => '2025-08-28',
            'contact_email' => 'admin@safekeep.com',
            'contact_phone' => '+1234567890'
        ],
        [
            'user_id' => 2, // Test user
            'title' => 'Found Black Wallet',
            'description' => 'Found a black leather wallet near the parking lot. Contains ID and credit cards. Looking for the owner.',
            'category' => 'Personal Items',
            'status' => 'found',
            'location' => 'Main Campus Parking Lot',
            'date_lost_found' => '2025-08-30',
            'contact_email' => 'test@example.com',
            'contact_phone' => ''
        ],
        [
            'user_id' => 1,
            'title' => 'Lost Red Backpack',
            'description' => 'Red Nike backpack with laptop inside. Contains important work documents and textbooks for Computer Science class.',
            'category' => 'Books & Stationery',
            'status' => 'lost',
            'location' => 'Computer Science Building',
            'date_lost_found' => '2025-08-29',
            'contact_email' => 'admin@safekeep.com',
            'contact_phone' => '+1234567890'
        ],
        [
            'user_id' => 2,
            'title' => 'Found Wireless Earbuds',
            'description' => 'Found white Apple AirPods in a charging case. Found them on a bench near the cafeteria.',
            'category' => 'Electronics',
            'status' => 'found',
            'location' => 'Student Cafeteria',
            'date_lost_found' => '2025-08-31',
            'contact_email' => 'test@example.com',
            'contact_phone' => '+9876543210'
        ],
        [
            'user_id' => 1,
            'title' => 'Lost Blue Winter Jacket',
            'description' => 'Navy blue North Face winter jacket, size Medium. Has my student ID in the pocket.',
            'category' => 'Clothing',
            'status' => 'lost',
            'location' => 'Gymnasium Locker Room',
            'date_lost_found' => '2025-08-27',
            'contact_email' => 'admin@safekeep.com',
            'contact_phone' => ''
        ],
        [
            'user_id' => 2,
            'title' => 'Found Basketball',
            'description' => 'Orange Spalding basketball found on the outdoor court. Seems to be in good condition.',
            'category' => 'Sports Equipment',
            'status' => 'found',
            'location' => 'Outdoor Basketball Court',
            'date_lost_found' => '2025-09-01',
            'contact_email' => 'test@example.com',
            'contact_phone' => '+9876543210'
        ]
    ];
    
    // Insert each sample item
    foreach ($sampleItems as $index => $itemData) {
        $item->user_id = $itemData['user_id'];
        $item->title = $itemData['title'];
        $item->description = $itemData['description'];
        $item->category = $itemData['category'];
        $item->status = $itemData['status'];
        $item->location = $itemData['location'];
        $item->date_lost_found = $itemData['date_lost_found'];
        $item->image_path = null; // No images for sample data
        $item->contact_email = $itemData['contact_email'];
        $item->contact_phone = $itemData['contact_phone'];
        
        if ($item->create()) {
            echo "✅ Added: " . $itemData['title'] . "\n";
            
            // Auto-approve the items for testing
            $lastInsertId = $db->lastInsertId();
            $approveQuery = "UPDATE items SET is_approved = 1 WHERE id = ?";
            $approveStmt = $db->prepare($approveQuery);
            $approveStmt->execute([$lastInsertId]);
            echo "   → Approved for public viewing\n";
        } else {
            echo "❌ Failed to add: " . $itemData['title'] . "\n";
        }
    }
    
    echo "\n🎉 Sample data added successfully!\n";
    echo "You can now test the browse functionality at: http://localhost/safekeep/browse.php\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
