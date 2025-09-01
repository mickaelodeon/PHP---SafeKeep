<?php
/**
 * Browse Items API
 * Handles searching and filtering of lost/found items
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once '../config/database.php';

    $database = new Database();
    $db = $database->getConnection();

    // Get query parameters with defaults
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'date_desc';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 12;

    $offset = ($page - 1) * $limit;

    // Build WHERE clause
    $whereConditions = ["i.is_approved = 1"];
    $params = [];

    if (!empty($search)) {
        $whereConditions[] = "(i.title LIKE ? OR i.description LIKE ? OR i.location LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
    }

    if (!empty($category)) {
        $whereConditions[] = "i.category = ?";
        $params[] = $category;
    }

    if (!empty($status)) {
        $whereConditions[] = "i.status = ?";
        $params[] = $status;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

    // Build ORDER BY clause
    switch ($sort) {
        case 'date_asc':
            $orderClause = 'ORDER BY i.date_lost_found ASC, i.created_at ASC';
            break;
        case 'title_asc':
            $orderClause = 'ORDER BY i.title ASC';
            break;
        case 'title_desc':
            $orderClause = 'ORDER BY i.title DESC';
            break;
        case 'date_desc':
        default:
            $orderClause = 'ORDER BY i.date_lost_found DESC, i.created_at DESC';
            break;
    }

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM items i LEFT JOIN users u ON i.user_id = u.id $whereClause";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute($params);
    $totalResult = $countStmt->fetch(PDO::FETCH_ASSOC);
    $total = (int)$totalResult['total'];
    $totalPages = ceil($total / $limit);

    // Get items with pagination
    $query = "SELECT i.*, u.username as posted_by
              FROM items i 
              LEFT JOIN users u ON i.user_id = u.id 
              $whereClause 
              $orderClause 
              LIMIT $limit OFFSET $offset";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process items for display
    foreach ($items as &$item) {
        // Remove sensitive information
        unset($item['contact_email']);
        unset($item['contact_phone']);
        
        // Format dates
        $item['date_lost_found'] = date('Y-m-d', strtotime($item['date_lost_found']));
        $item['created_at'] = date('Y-m-d H:i:s', strtotime($item['created_at']));
        
        // Ensure description is safe for display
        $item['description'] = htmlspecialchars($item['description']);
        $item['title'] = htmlspecialchars($item['title']);
        $item['location'] = htmlspecialchars($item['location']);
        
        // Handle image
        $item['image_url'] = $item['image_path'];
        unset($item['image_path']);
    }

    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'page' => $page,
        'totalPages' => $totalPages,
        'limit' => $limit,
        'filters' => [
            'search' => $search,
            'category' => $category,
            'status' => $status,
            'sort' => $sort
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred',
        'debug' => $e->getMessage()
    ]);
}
?>
