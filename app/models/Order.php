<?php
class Order {
    private $db;

    public function __construct() {
        $this->db = new Database;
        $this->ensureOrderItemsTableExists();
    }


    // Check and create orders_items table if needed
    /*private function ensureOrderItemsTableExists() {
        try {
            // Check if table exists
            $this->db->query("SHOW TABLES LIKE 'orders_items'");
            $tableExists = $this->db->rowCount() > 0;
            
            if (!$tableExists) {
                // Create the table only if it doesn't exist
                $this->db->query("CREATE TABLE `orders_items` (...)");
                $this->db->execute();
                error_log("Created missing orders_items table");
            }
        } catch (Exception $e) {
            error_log("Error checking/creating orders_items table: " . $e->getMessage());
        }
    }*/

    private function ensureOrderItemsTableExists() {
        try {
            // Check if table exists
            $this->db->query("SHOW TABLES LIKE 'orders_items'");
            $tableExists = $this->db->rowCount() > 0;
            
            if (!$tableExists) {
                // Create the table with proper column definitions
                $this->db->query("CREATE TABLE `orders_items` (
                    ID INT AUTO_INCREMENT PRIMARY KEY,
                    OrderID VARCHAR(10) NOT NULL,
                    ProductID VARCHAR(10) NOT NULL,
                    Quantity INT NOT NULL,
                    Price DECIMAL(10,2) NOT NULL,
                    FOREIGN KEY (OrderID) REFERENCES orders(OrderID),
                    FOREIGN KEY (ProductID) REFERENCES marketplace_inventory(ProductID)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
                
                $this->db->execute();
                error_log("Created missing orders_items table");
            }
        } catch (Exception $e) {
            error_log("Error checking/creating orders_items table: " . $e->getMessage());
        }
    }

    // Create new order
    public function createOrder($data) {
        // Generate OrderID (Format: ORD + 5 random digits)
        $orderId = 'ORD' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Insert order
            $this->db->query('INSERT INTO orders (OrderID, UserID, OrderDate, TotalAmount, Status) 
                             VALUES (:orderId, :userId, CURRENT_TIMESTAMP, :totalAmount, "Pending")');
            
            $this->db->bind(':orderId', $orderId);
            $this->db->bind(':userId', $data['user_id']);
            $this->db->bind(':totalAmount', $data['total_amount']);
            
            $this->db->execute();
            
            // Insert delivery details if needed (for future implementation)
            // ...
            
            // Commit transaction
            $this->db->commit();
            
            return $orderId;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            error_log("Order creation error: " . $e->getMessage());
            return false;
        }
    }

    // Add order item
    public function addOrderItem($data) {
        try {
            $this->db->query('INSERT INTO orders_items (OrderID, ProductID, Quantity, Price) 
                             VALUES (:orderId, :productId, :quantity, :price)');
            
            $this->db->bind(':orderId', $data['order_id']);
            $this->db->bind(':productId', $data['product_id']);
            $this->db->bind(':quantity', $data['quantity']);
            $this->db->bind(':price', $data['price']);
            
            $this->db->execute();
            
            // Update product stock
            $productModel = new Product();
            $productModel->updateStock($data['product_id'], $data['quantity']);
            
            return true;
        } catch (Exception $e) {
            error_log("Add order item error: " . $e->getMessage());
            return false;
        }
    }

    // Get order by ID
    /* public function getOrderById($orderId) {
        $this->db->query('SELECT o.*, 
                        u.Username,
                        u.Email
                        FROM orders o
                        JOIN users u ON o.UserID = u.UserID
                        WHERE o.OrderID = :orderId');
        
        $this->db->bind(':orderId', $orderId);
        
        $row = $this->db->single();
        
        return $row;
    }*/

    // Get order by ID
    public function getOrderById($orderId) {
        $this->db->query('SELECT o.*, 
                        u.Username,
                        u.Email,
                        sd.ShippingAddress,
                        sd.ContactPhone,
                        sd.PaymentMethod,
                        sd.ShippingNotes
                        FROM orders o
                        JOIN users u ON o.UserID = u.UserID
                        LEFT JOIN shipping_details sd ON o.OrderID = sd.OrderID
                        WHERE o.OrderID = :orderId');
        
        $this->db->bind(':orderId', $orderId);
        
        $row = $this->db->single();
        
        return $row;
    }

    // Get order items
    /*public function getOrderItems($orderId) {
        $this->db->query('SELECT oi.*, p.ProductName, p.ProductImage
                        FROM orders_items oi
                        JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                        WHERE oi.OrderID = :orderId');
        
        $this->db->bind(':orderId', $orderId);
        
        $results = $this->db->resultSet();
        
        return $results;
    }*/
    public function getOrderItems($orderId) {
        try {
            // Always include the ProductImage column in the query
            $this->db->query('SELECT oi.*, p.ProductID, p.ProductName, p.ProductImage
                            FROM orders_items oi
                            JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                            WHERE oi.OrderID = :orderId');
            
            $this->db->bind(':orderId', $orderId);
            
            $results = $this->db->resultSet();
            
            return $results;
        } catch (Exception $e) {
            error_log("Error fetching order items: " . $e->getMessage());
            return [];
        }
    }

    // Get user orders
    public function getUserOrders($userId) {
        $this->db->query('SELECT * FROM orders 
                        WHERE UserID = :userId
                        ORDER BY OrderDate DESC');
        
        $this->db->bind(':userId', $userId);
        
        $results = $this->db->resultSet();
        
        return $results;
    }

    // Update order status
    public function updateOrderStatus($orderId, $status) {
        $this->db->query('UPDATE orders SET Status = :status WHERE OrderID = :orderId');
        
        $this->db->bind(':status', $status);
        $this->db->bind(':orderId', $orderId);
        
        return $this->db->execute();
    }

    
    public function createOrderItemsTable() {
        try {
            $this->db->query('CREATE TABLE IF NOT EXISTS orders_items (
                            ID INT AUTO_INCREMENT PRIMARY KEY,
                            OrderID VARCHAR(10) NOT NULL,
                            ProductID VARCHAR(10) NOT NULL,
                            Quantity INT NOT NULL,
                            Price DECIMAL(10,2) NOT NULL,
                            FOREIGN KEY (OrderID) REFERENCES orders(OrderID),
                            FOREIGN KEY (ProductID) REFERENCES marketplace_inventory(ProductID)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Create order items table error: " . $e->getMessage());
            return false;
        }
    }

    // Count user's orders (for statistics)
    public function countUserOrders($userId) {
        $this->db->query('SELECT COUNT(*) as count FROM orders WHERE UserID = :userId');
        $this->db->bind(':userId', $userId);
        $row = $this->db->single();
        return $row->count;
    }

    // Get total spent by user (for statistics)
    public function getTotalSpent($userId) {
        $this->db->query('SELECT SUM(TotalAmount) as total FROM orders WHERE UserID = :userId');
        $this->db->bind(':userId', $userId);
        $row = $this->db->single();
        return $row->total ?? 0;
    }

    // Create shipping details
public function createShippingDetails($data) {
    // Generate ShippingID (Format: SHP + 5 random digits)
    $shippingId = 'SHP' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
    
    try {
        $this->db->query('INSERT INTO shipping_details 
                         (ShippingID, OrderID, ShippingAddress, ContactPhone, PaymentMethod, ShippingNotes) 
                         VALUES (:shippingId, :orderId, :shippingAddress, :contactPhone, :paymentMethod, :shippingNotes)');
        
        $this->db->bind(':shippingId', $shippingId);
        $this->db->bind(':orderId', $data['order_id']);
        $this->db->bind(':shippingAddress', $data['shipping_address']);
        $this->db->bind(':contactPhone', $data['contact_phone']);
        $this->db->bind(':paymentMethod', $data['payment_method']);
        $this->db->bind(':shippingNotes', $data['shipping_notes']);
        
        $this->db->execute();
        
        return $shippingId;
    } catch (Exception $e) {
        error_log("Create shipping details error: " . $e->getMessage());
        return false;
    }
}

// Get shipping details for an order
public function getShippingDetails($orderId) {
    try {
        $this->db->query('SELECT * FROM shipping_details WHERE OrderID = :orderId');
        $this->db->bind(':orderId', $orderId);
        
        return $this->db->single();
    } catch (Exception $e) {
        error_log("Get shipping details error: " . $e->getMessage());
        return false;
    }
}

// Add these methods to the Order.php model

// Get seller orders count by date range
public function getSellerOrdersCountByDate($sellerId, $startDate, $endDate) {
    $this->db->query('SELECT COUNT(DISTINCT o.OrderID) as count
                    FROM orders o
                    JOIN orders_items oi ON o.OrderID = oi.OrderID
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    WHERE p.SellerID = :sellerId
                    AND DATE(o.OrderDate) BETWEEN :startDate AND :endDate');
    
    $this->db->bind(':sellerId', $sellerId);
    $this->db->bind(':startDate', $startDate);
    $this->db->bind(':endDate', $endDate);
    
    $row = $this->db->single();
    
    return $row->count;
}

// Get seller revenue by date range
public function getSellerRevenueByDate($sellerId, $startDate, $endDate) {
    $this->db->query('SELECT SUM(oi.Price * oi.Quantity) as total
                    FROM orders o
                    JOIN orders_items oi ON o.OrderID = oi.OrderID
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    WHERE p.SellerID = :sellerId
                    AND DATE(o.OrderDate) BETWEEN :startDate AND :endDate
                    AND o.Status != "Cancelled"');
    
    $this->db->bind(':sellerId', $sellerId);
    $this->db->bind(':startDate', $startDate);
    $this->db->bind(':endDate', $endDate);
    
    $row = $this->db->single();
    
    return $row->total ?? 0;
}

// Get seller products sold by date range
public function getSellerProductsSoldByDate($sellerId, $startDate, $endDate) {
    $this->db->query('SELECT SUM(oi.Quantity) as total
                    FROM orders o
                    JOIN orders_items oi ON o.OrderID = oi.OrderID
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    WHERE p.SellerID = :sellerId
                    AND DATE(o.OrderDate) BETWEEN :startDate AND :endDate
                    AND o.Status != "Cancelled"');
    
    $this->db->bind(':sellerId', $sellerId);
    $this->db->bind(':startDate', $startDate);
    $this->db->bind(':endDate', $endDate);
    
    $row = $this->db->single();
    
    return $row->total ?? 0;
}

// Get seller sales trend (daily data)
public function getSellerSalesTrend($sellerId, $startDate, $endDate) {
    $this->db->query('SELECT DATE(o.OrderDate) as date, 
                    SUM(oi.Price * oi.Quantity) as amount
                    FROM orders o
                    JOIN orders_items oi ON o.OrderID = oi.OrderID
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    WHERE p.SellerID = :sellerId
                    AND DATE(o.OrderDate) BETWEEN :startDate AND :endDate
                    AND o.Status != "Cancelled"
                    GROUP BY DATE(o.OrderDate)
                    ORDER BY DATE(o.OrderDate)');
    
    $this->db->bind(':sellerId', $sellerId);
    $this->db->bind(':startDate', $startDate);
    $this->db->bind(':endDate', $endDate);
    
    $results = $this->db->resultSet();
    
    $trendData = [];
    foreach($results as $row) {
        $trendData[] = [
            'date' => date('M j', strtotime($row->date)),
            'amount' => (float)$row->amount
        ];
    }
    
    return $trendData;
}

// Get seller top products
public function getSellerTopProducts($sellerId, $startDate, $endDate, $limit = 5) {
    $this->db->query('SELECT p.ProductID, p.ProductName as name, SUM(oi.Quantity) as quantity
                    FROM orders o
                    JOIN orders_items oi ON o.OrderID = oi.OrderID
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    WHERE p.SellerID = :sellerId
                    AND DATE(o.OrderDate) BETWEEN :startDate AND :endDate
                    AND o.Status != "Cancelled"
                    GROUP BY p.ProductID, p.ProductName
                    ORDER BY quantity DESC
                    LIMIT :limit');
    
    $this->db->bind(':sellerId', $sellerId);
    $this->db->bind(':startDate', $startDate);
    $this->db->bind(':endDate', $endDate);
    $this->db->bind(':limit', $limit);
    
    $results = $this->db->resultSet();
    
    $productsData = [];
    foreach($results as $row) {
        $productsData[] = [
            'id' => $row->ProductID,
            'name' => $row->name,
            'quantity' => (int)$row->quantity
        ];
    }
    
    return $productsData;
}

// Get seller sales by category
public function getSellerCategorySales($sellerId, $startDate, $endDate) {
    $this->db->query('SELECT p.Category as category, SUM(oi.Price * oi.Quantity) as amount
                    FROM orders o
                    JOIN orders_items oi ON o.OrderID = oi.OrderID
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    WHERE p.SellerID = :sellerId
                    AND DATE(o.OrderDate) BETWEEN :startDate AND :endDate
                    AND o.Status != "Cancelled"
                    GROUP BY p.Category
                    ORDER BY amount DESC');
    
    $this->db->bind(':sellerId', $sellerId);
    $this->db->bind(':startDate', $startDate);
    $this->db->bind(':endDate', $endDate);
    
    $results = $this->db->resultSet();
    
    $categoryData = [];
    foreach($results as $row) {
        $categoryData[] = [
            'category' => $row->category,
            'amount' => (float)$row->amount
        ];
    }
    
    return $categoryData;
}

// Get count of orders for a seller
public function getSellerOrdersCount($sellerId) {
    $this->db->query('SELECT COUNT(DISTINCT o.OrderID) as count
                    FROM orders o
                    JOIN orders_items oi ON o.OrderID = oi.OrderID
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    WHERE p.SellerID = :sellerId');
    
    $this->db->bind(':sellerId', $sellerId);
    
    $row = $this->db->single();
    
    return $row->count;
}

// Get recent orders for a seller
public function getRecentOrdersForSeller($sellerId, $limit = 5) {
    $this->db->query('SELECT DISTINCT o.*, u.Username
                    FROM orders o
                    JOIN orders_items oi ON o.OrderID = oi.OrderID
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    JOIN users u ON o.UserID = u.UserID
                    WHERE p.SellerID = :sellerId
                    ORDER BY o.OrderDate DESC
                    LIMIT :limit');
    
    $this->db->bind(':sellerId', $sellerId);
    $this->db->bind(':limit', $limit);
    
    $results = $this->db->resultSet();
    
    return $results;
}

public function getTotalRevenue() {
    $this->db->query('SELECT SUM(TotalAmount) as total FROM orders WHERE Status != "Cancelled"');
    $row = $this->db->single();
    return $row->total ?? 0;
}


}