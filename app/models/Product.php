<?php
class Product {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all products - modified to include out of stock products
    public function getAllProducts() {
        $this->db->query('SELECT p.*, s.FirstName, s.LastName
                        FROM marketplace_inventory p
                        JOIN sellers s ON p.SellerID = s.SellerID
                        ORDER BY p.LastUpdated DESC');
        
        $results = $this->db->resultSet();
        
        return $results;
    }
    
    // Get recent products (with limit) - modified to include out of stock products
    public function getRecentProducts($limit = 3) {
        $this->db->query('SELECT p.*, s.FirstName, s.LastName
                        FROM marketplace_inventory p
                        JOIN sellers s ON p.SellerID = s.SellerID
                        ORDER BY p.LastUpdated DESC
                        LIMIT :limit');
        
        $this->db->bind(':limit', $limit);
        
        $results = $this->db->resultSet();
        
        return $results;
    }
    
    // Get paginated products - modified to include out of stock products
    public function getPaginatedProducts($page = 1, $perPage = 12, $category = null) {
        $offset = ($page - 1) * $perPage;
        
        if ($category) {
            $this->db->query('SELECT p.*, s.FirstName, s.LastName
                            FROM marketplace_inventory p
                            JOIN sellers s ON p.SellerID = s.SellerID
                            WHERE p.Category = :category
                            ORDER BY p.LastUpdated DESC
                            LIMIT :offset, :limit');
            
            $this->db->bind(':category', $category);
        } else {
            $this->db->query('SELECT p.*, s.FirstName, s.LastName
                            FROM marketplace_inventory p
                            JOIN sellers s ON p.SellerID = s.SellerID
                            ORDER BY p.LastUpdated DESC
                            LIMIT :offset, :limit');
        }
        
        $this->db->bind(':offset', $offset);
        $this->db->bind(':limit', $perPage);
        
        $results = $this->db->resultSet();
        
        return $results;
    }
    
    // Get total products count (for pagination) - modified to include out of stock products
    public function getTotalProductsCount($category = null) {
        if ($category) {
            $this->db->query('SELECT COUNT(*) as count
                            FROM marketplace_inventory
                            WHERE Category = :category');
            
            $this->db->bind(':category', $category);
        } else {
            $this->db->query('SELECT COUNT(*) as count
                            FROM marketplace_inventory');
        }
        
        $row = $this->db->single();
        
        return $row->count;
    }

    // Get product by ID - unchanged
    public function getProductById($id) {
        $this->db->query('SELECT p.*, s.FirstName, s.LastName
                        FROM marketplace_inventory p
                        JOIN sellers s ON p.SellerID = s.SellerID
                        WHERE p.ProductID = :id');
        
        $this->db->bind(':id', $id);
        
        $row = $this->db->single();
        
        return $row;
    }

    // Get products by category - modified to include out of stock products
    public function getProductsByCategory($category) {
        $this->db->query('SELECT p.*, s.FirstName, s.LastName
                        FROM marketplace_inventory p
                        JOIN sellers s ON p.SellerID = s.SellerID
                        WHERE p.Category = :category
                        ORDER BY p.LastUpdated DESC');
        
        $this->db->bind(':category', $category);
        
        $results = $this->db->resultSet();
        
        return $results;
    }

    // Search products - modified to include out of stock products and search by seller name
    public function searchProducts($term) {
        $this->db->query('SELECT p.*, s.FirstName, s.LastName
                        FROM marketplace_inventory p
                        JOIN sellers s ON p.SellerID = s.SellerID
                        WHERE (p.ProductName LIKE :term 
                              OR p.Description LIKE :term 
                              OR p.Category LIKE :term
                              OR CONCAT(s.FirstName, " ", s.LastName) LIKE :term)
                        ORDER BY p.LastUpdated DESC');
        
        $this->db->bind(':term', '%' . $term . '%');
        
        $results = $this->db->resultSet();
        
        return $results;
    }

    // Get product categories with product counts - modified to include all products
    public function getCategories() {
        $this->db->query('SELECT Category, COUNT(*) as product_count
                         FROM marketplace_inventory
                         GROUP BY Category
                         ORDER BY Category');
        
        $results = $this->db->resultSet();
        
        return $results;
    }

    // Check if product is in stock and has enough quantity - unchanged
    public function checkStock($productId, $quantityNeeded) {
        $this->db->query('SELECT ProductID, StockQuantity FROM marketplace_inventory
                        WHERE ProductID = :id AND Status = "Available" AND StockQuantity >= :quantity');
        
        $this->db->bind(':id', $productId);
        $this->db->bind(':quantity', $quantityNeeded);
        
        $row = $this->db->single();
        
        return !empty($row);
    }
    
    // Add new product (for admin use) - unchanged
    public function addProduct($data) {
        // Generate ProductID (Format: P + 5 random digits)
        $productId = 'P' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        $this->db->query('INSERT INTO marketplace_inventory 
                        (ProductID, SellerID, ProductName, Description, Price, StockQuantity, Category, Status) 
                        VALUES 
                        (:productId, :sellerId, :productName, :description, :price, :stockQuantity, :category, "Available")');
        
        $this->db->bind(':productId', $productId);
        $this->db->bind(':sellerId', $data['seller_id']);
        $this->db->bind(':productName', $data['product_name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stockQuantity', $data['stock_quantity']);
        $this->db->bind(':category', $data['category']);
        
        // Execute
        if($this->db->execute()) {
            return $productId;
        } else {
            return false;
        }
    }
    
    // Update stock quantity after order - unchanged
    public function updateStock($productId, $quantityOrdered) {
        $this->db->query('UPDATE marketplace_inventory 
                        SET StockQuantity = StockQuantity - :quantity,
                            Status = CASE 
                                        WHEN (StockQuantity - :quantity) <= 0 THEN "OutOfStock" 
                                        ELSE "Available" 
                                    END
                        WHERE ProductID = :id');
        
        $this->db->bind(':id', $productId);
        $this->db->bind(':quantity', $quantityOrdered);
        
        return $this->db->execute();
    }

    // Update product
    public function updateProduct($data) {
        $this->db->query('UPDATE marketplace_inventory 
                        SET ProductName = :productName, 
                            Description = :description, 
                            Price = :price, 
                            StockQuantity = :stockQuantity, 
                            Category = :category,
                            Status = :status
                        WHERE ProductID = :productId');
        
        $this->db->bind(':productId', $data['product_id']);
        $this->db->bind(':productName', $data['product_name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stockQuantity', $data['stock_quantity']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':status', $data['status']);
        
        // Execute
        return $this->db->execute();
    }

    public function getProductsBySeller($sellerId) {
        $this->db->query('SELECT * FROM products WHERE seller_id = :seller_id');
        $this->db->bind(':seller_id', $sellerId);
        return $this->db->resultSet();
    }

}