<?php
/**
 * Item Class
 * Handles lost and found items management
 */

class Item {
    private $conn;
    private $table = 'items';

    // Item properties
    public $id;
    public $user_id;
    public $title;
    public $description;
    public $category;
    public $status; // lost, found, claimed, returned
    public $location;
    public $date_lost_found;
    public $image_path;
    public $contact_email;
    public $contact_phone;
    public $is_approved;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new item post
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET user_id = :user_id,
                      title = :title,
                      description = :description,
                      category = :category,
                      status = :status,
                      location = :location,
                      date_lost_found = :date_lost_found,
                      image_path = :image_path,
                      contact_email = :contact_email,
                      contact_phone = :contact_phone,
                      is_approved = FALSE,
                      created_at = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->contact_email = htmlspecialchars(strip_tags($this->contact_email));
        $this->contact_phone = htmlspecialchars(strip_tags($this->contact_phone));

        // Bind values
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':date_lost_found', $this->date_lost_found);
        $stmt->bindParam(':image_path', $this->image_path);
        $stmt->bindParam(':contact_email', $this->contact_email);
        $stmt->bindParam(':contact_phone', $this->contact_phone);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get all approved items
     */
    public function getApprovedItems($limit = 20, $offset = 0) {
        $query = "SELECT i.*, u.username, u.role, c.name as category_name
                  FROM " . $this->table . " i
                  LEFT JOIN users u ON i.user_id = u.id
                  LEFT JOIN categories c ON i.category = c.name
                  WHERE i.is_approved = TRUE
                  ORDER BY i.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get items by user
     */
    public function getItemsByUser($user_id) {
        $query = "SELECT i.*, c.name as category_name
                  FROM " . $this->table . " i
                  LEFT JOIN categories c ON i.category = c.name
                  WHERE i.user_id = :user_id
                  ORDER BY i.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get pending items for admin approval
     */
    public function getPendingItems() {
        $query = "SELECT i.*, u.username, u.role, c.name as category_name
                  FROM " . $this->table . " i
                  LEFT JOIN users u ON i.user_id = u.id
                  LEFT JOIN categories c ON i.category = c.name
                  WHERE i.is_approved = FALSE
                  ORDER BY i.created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single item by ID
     */
    public function getItemById($id) {
        $query = "SELECT i.*, u.username, u.role, u.email as user_email, c.name as category_name
                  FROM " . $this->table . " i
                  LEFT JOIN users u ON i.user_id = u.id
                  LEFT JOIN categories c ON i.category = c.name
                  WHERE i.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Update item
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title,
                      description = :description,
                      category = :category,
                      location = :location,
                      date_lost_found = :date_lost_found,
                      contact_email = :contact_email,
                      contact_phone = :contact_phone,
                      updated_at = NOW()
                  WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->contact_email = htmlspecialchars(strip_tags($this->contact_email));
        $this->contact_phone = htmlspecialchars(strip_tags($this->contact_phone));

        // Bind values
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':date_lost_found', $this->date_lost_found);
        $stmt->bindParam(':contact_email', $this->contact_email);
        $stmt->bindParam(':contact_phone', $this->contact_phone);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    /**
     * Delete item (only by owner or admin)
     */
    public function delete($user_id = null, $is_admin = false) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        if (!$is_admin) {
            $query .= " AND user_id = :user_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if (!$is_admin) {
            $stmt->bindParam(':user_id', $user_id);
        }

        return $stmt->execute();
    }

    /**
     * Approve item (admin only)
     */
    public function approve() {
        $query = "UPDATE " . $this->table . " 
                  SET is_approved = TRUE, updated_at = NOW() 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Search items
     */
    public function search($keyword, $category = '', $status = '', $limit = 20) {
        $query = "SELECT i.*, u.username, c.name as category_name
                  FROM " . $this->table . " i
                  LEFT JOIN users u ON i.user_id = u.id
                  LEFT JOIN categories c ON i.category = c.name
                  WHERE i.is_approved = TRUE";

        $params = [];

        if (!empty($keyword)) {
            $query .= " AND (i.title LIKE :keyword OR i.description LIKE :keyword OR i.location LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($category)) {
            $query .= " AND i.category = :category";
            $params[':category'] = $category;
        }

        if (!empty($status)) {
            $query .= " AND i.status = :status";
            $params[':status'] = $status;
        }

        $query .= " ORDER BY i.created_at DESC LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get categories
     */
    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
