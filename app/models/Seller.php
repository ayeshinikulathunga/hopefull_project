<?php
class Seller {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get seller by UserID
    public function getSellerByUserId($userId) {
        $this->db->query('SELECT * FROM sellers WHERE UserID = :userId');
        $this->db->bind(':userId', $userId);

        return $this->db->single();
    }

    // Get seller by SellerID
    public function getSellerById($sellerId) {
        $this->db->query('SELECT * FROM sellers WHERE SellerID = :sellerId');
        $this->db->bind(':sellerId', $sellerId);

        return $this->db->single();
    }

    // Get all products for a seller
    public function getSellerProducts($sellerId) {
        $this->db->query('SELECT * FROM marketplace_inventory 
                          WHERE SellerID = :sellerId 
                          ORDER BY LastUpdated DESC');
        
        $this->db->bind(':sellerId', $sellerId);
        
        return $this->db->resultSet();
    }

    // Add new product
    public function addProduct($data) {
        // Generate ProductID (Format: P + 5 random digits)
        $productId = 'P' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        $this->db->query('INSERT INTO marketplace_inventory 
                         (ProductID, SellerID, ProductName, Description, Price, 
                          StockQuantity, Category, Status, ProductImage) 
                         VALUES 
                         (:productId, :sellerId, :productName, :description, :price, 
                          :stockQuantity, :category, :status, :productImage)');
        
        $this->db->bind(':productId', $productId);
        $this->db->bind(':sellerId', $data['seller_id']);
        $this->db->bind(':productName', $data['product_name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stockQuantity', $data['stock_quantity']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':status', $data['stock_quantity'] > 0 ? 'Available' : 'OutOfStock');
        $this->db->bind(':productImage', $data['product_image']);
        
        if($this->db->execute()) {
            return $productId;
        } else {
            return false;
        }
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
                             ' . (!empty($data['product_image']) ? ', ProductImage = :productImage' : '') . ' 
                         WHERE ProductID = :productId AND SellerID = :sellerId');
        
        $this->db->bind(':productName', $data['product_name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stockQuantity', $data['stock_quantity']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':status', $data['stock_quantity'] > 0 ? 'Available' : 'OutOfStock');
        $this->db->bind(':productId', $data['product_id']);
        $this->db->bind(':sellerId', $data['seller_id']);
        
        if(!empty($data['product_image'])) {
            $this->db->bind(':productImage', $data['product_image']);
        }
        
        return $this->db->execute();
    }

    // Delete product
    public function deleteProduct($productId, $sellerId) {
        $this->db->query('DELETE FROM marketplace_inventory 
                         WHERE ProductID = :productId AND SellerID = :sellerId');
        
        $this->db->bind(':productId', $productId);
        $this->db->bind(':sellerId', $sellerId);
        
        return $this->db->execute();
    }

    // Update product stock
    public function updateStock($productId, $quantity) {
        $this->db->query('UPDATE marketplace_inventory 
                         SET StockQuantity = StockQuantity - :quantity,
                             Status = CASE 
                                       WHEN (StockQuantity - :quantity) <= 0 THEN "OutOfStock" 
                                       ELSE "Available" 
                                     END
                         WHERE ProductID = :productId');
        
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':productId', $productId);
        
        return $this->db->execute();
    }

    // Get product by ID
    public function getProductById($productId) {
        $this->db->query('SELECT * FROM marketplace_inventory WHERE ProductID = :productId');
        $this->db->bind(':productId', $productId);
        
        return $this->db->single();
    }

    // Get seller orders (showing all orders for products of this seller)
    public function getSellerOrders($sellerId) {
        $this->db->query('SELECT DISTINCT o.*, u.Username, u.Email 
                         FROM orders o
                         JOIN orders_items oi ON o.OrderID = oi.OrderID
                         JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                         JOIN users u ON o.UserID = u.UserID
                         WHERE p.SellerID = :sellerId
                         ORDER BY o.OrderDate DESC');
        
        $this->db->bind(':sellerId', $sellerId);
        
        return $this->db->resultSet();
    }
    
    // Get order details (for products of this seller only)
    public function getSellerOrderItems($orderId, $sellerId) {
        $this->db->query('SELECT oi.*, p.ProductName, p.ProductImage 
                         FROM orders_items oi
                         JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                         WHERE oi.OrderID = :orderId AND p.SellerID = :sellerId');
        
        $this->db->bind(':orderId', $orderId);
        $this->db->bind(':sellerId', $sellerId);
        
        return $this->db->resultSet();
    }

    // Get total sales amount
    public function getTotalSales($sellerId) {
        $this->db->query('SELECT SUM(oi.Price * oi.Quantity) as total
                         FROM orders_items oi
                         JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                         JOIN orders o ON oi.OrderID = o.OrderID
                         WHERE p.SellerID = :sellerId
                         AND o.Status != "Cancelled"');
        
        $this->db->bind(':sellerId', $sellerId);
        
        $result = $this->db->single();
        return $result->total ?? 0;
    }

    // Update seller total sales
    public function updateTotalSales($sellerId, $amount) {
        $this->db->query('UPDATE sellers 
                         SET TotalSales = TotalSales + :amount
                         WHERE SellerID = :sellerId');
        
        $this->db->bind(':amount', $amount);
        $this->db->bind(':sellerId', $sellerId);
        
        return $this->db->execute();
    }

    // Get inventory status summary
    public function getInventorySummary($sellerId) {
        $this->db->query('SELECT 
                            COUNT(*) as totalProducts,
                            SUM(CASE WHEN Status = "Available" THEN 1 ELSE 0 END) as availableProducts,
                            SUM(CASE WHEN Status = "OutOfStock" THEN 1 ELSE 0 END) as outOfStockProducts,
                            SUM(StockQuantity) as totalStock
                         FROM marketplace_inventory
                         WHERE SellerID = :sellerId');
        
        $this->db->bind(':sellerId', $sellerId);
        
        return $this->db->single();
    }

    // Get low stock products
    public function getLowStockProducts($sellerId, $threshold = 5) {
        $this->db->query('SELECT *
                         FROM marketplace_inventory
                         WHERE SellerID = :sellerId
                         AND StockQuantity <= :threshold
                         AND StockQuantity > 0
                         ORDER BY StockQuantity ASC');
        
        $this->db->bind(':sellerId', $sellerId);
        $this->db->bind(':threshold', $threshold);
        
        return $this->db->resultSet();
    }

    // Get top selling products
    public function getTopSellingProducts($sellerId, $limit = 5) {
        $this->db->query('SELECT p.ProductID, p.ProductName, SUM(oi.Quantity) as totalSold, p.StockQuantity, p.Status
                         FROM orders_items oi
                         JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                         JOIN orders o ON oi.OrderID = o.OrderID
                         WHERE p.SellerID = :sellerId
                         AND o.Status != "Cancelled"
                         GROUP BY p.ProductID, p.ProductName, p.StockQuantity, p.Status
                         ORDER BY totalSold DESC
                         LIMIT :limit');
        
        $this->db->bind(':sellerId', $sellerId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    
    // Get recent product inquiries
    public function getProductInquiries($sellerId) {
        $this->db->query('SELECT pi.*, p.ProductName, u.Username
                          FROM product_inquiries pi
                          JOIN marketplace_inventory p ON pi.ProductID = p.ProductID
                          JOIN users u ON pi.UserID = u.UserID
                          WHERE p.SellerID = :sellerId
                          ORDER BY pi.CreatedDate DESC');
        
        $this->db->bind(':sellerId', $sellerId);
        
        return $this->db->resultSet();
    }
    
    // Answer product inquiry
    public function answerInquiry($inquiryId, $response) {
        $this->db->query('UPDATE product_inquiries
                          SET Response = :response,
                              Status = "Answered",
                              ResponseDate = CURRENT_TIMESTAMP
                          WHERE InquiryID = :inquiryId');
        
        $this->db->bind(':response', $response);
        $this->db->bind(':inquiryId', $inquiryId);
        
        return $this->db->execute();
    }

    public function getTotalSellers() {
        $this->db->query('SELECT COUNT(*) as count FROM sellers');
        $row = $this->db->single();
        return $row->count ?? 0;
    }
}