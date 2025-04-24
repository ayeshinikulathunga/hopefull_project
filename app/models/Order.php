<?php
class Order {
    private $db;

    public function __construct() {
        $this->db = new Database;
        $this->ensureOrderItemsTableExists();
    }



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
    /*public function createOrder($data) {
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
    }*/

   
public function createOrder($data) {
    // Generate OrderID (Format: ORD + 5 random digits)
    $orderId = 'ORD' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
    
    // Start transaction
    $this->db->beginTransaction();
    
    try {
        // Insert order - now including PaymentMethod
        $this->db->query('INSERT INTO orders (OrderID, UserID, OrderDate, TotalAmount, Status, PaymentMethod) 
                         VALUES (:orderId, :userId, CURRENT_TIMESTAMP, :totalAmount, "Pending", :paymentMethod)');
        
        $this->db->bind(':orderId', $orderId);
        $this->db->bind(':userId', $data['user_id']);
        $this->db->bind(':totalAmount', $data['total_amount']);
        $this->db->bind(':paymentMethod', $data['payment_method']);
        
        $this->db->execute();
        
        
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
   
public function getOrderById($orderId) {
    $this->db->query('SELECT o.*, 
                    u.Username,
                    u.Email,
                    sd.ShippingAddress,
                    sd.ContactPhone,
                    sd.PaymentMethod AS SDPaymentMethod, 
                    sd.ShippingNotes
                    FROM orders o
                    JOIN users u ON o.UserID = u.UserID
                    LEFT JOIN shipping_details sd ON o.OrderID = sd.OrderID
                    WHERE o.OrderID = :orderId');
    
    $this->db->bind(':orderId', $orderId);
    
    $row = $this->db->single();
    
    // Use the payment method from shipping_details if available, or from the orders table
    if ($row) {
        if (!empty($row->SDPaymentMethod) && (empty($row->PaymentMethod) || $row->PaymentMethod == '')) {
            $row->PaymentMethod = $row->SDPaymentMethod;
        }
    }
    
    return $row;
}
  

    // Get order items

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

// Add these methods to your Order.php model class

// Request an order cancellation
public function requestCancellation($data) {
    // Generate CancellationID (Format: CAN + 5 random digits)
    $cancellationId = 'CAN' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
    
    try {
        $this->db->query('INSERT INTO order_cancellations 
                        (CancellationID, OrderID, UserID, Reason, Status) 
                        VALUES 
                        (:cancellationId, :orderId, :userId, :reason, "Pending")');
        
        $this->db->bind(':cancellationId', $cancellationId);
        $this->db->bind(':orderId', $data['order_id']);
        $this->db->bind(':userId', $data['user_id']);
        $this->db->bind(':reason', $data['reason']);
        
        return $this->db->execute();
    } catch (Exception $e) {
        error_log("Order cancellation request error: " . $e->getMessage());
        return false;
    }
}

// Check if an order has a pending cancellation request
public function hasPendingCancellation($orderId) {
    try {
        $this->db->query('SELECT COUNT(*) as count FROM order_cancellations 
                        WHERE OrderID = :orderId AND Status = "Pending"');
        
        $this->db->bind(':orderId', $orderId);
        $row = $this->db->single();
        
        return $row->count > 0;
    } catch (Exception $e) {
        error_log("Check pending cancellation error: " . $e->getMessage());
        return false;
    }
}

// Get cancellation request for an order
public function getCancellationRequest($orderId) {
    try {
        $this->db->query('SELECT oc.*, u.Username
                        FROM order_cancellations oc
                        JOIN users u ON oc.UserID = u.UserID
                        WHERE oc.OrderID = :orderId
                        ORDER BY oc.RequestDate DESC
                        LIMIT 1');
        
        $this->db->bind(':orderId', $orderId);
        return $this->db->single();
    } catch (Exception $e) {
        error_log("Get cancellation request error: " . $e->getMessage());
        return false;
    }
}

// Process a cancellation request (approve or reject)
public function processCancellationRequest($data) {
    try {
        $this->db->beginTransaction();
        
        // Update the cancellation request
        $this->db->query('UPDATE order_cancellations 
                        SET Status = :status,
                            ProcessedBy = :processedBy,
                            ProcessedDate = CURRENT_TIMESTAMP,
                            Notes = :notes
                        WHERE CancellationID = :cancellationId');
        
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':processedBy', $data['processed_by']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':cancellationId', $data['cancellation_id']);
        
        $this->db->execute();
        
        // If approved, update the order status to Cancelled
        if ($data['status'] === 'Approved') {
            $this->db->query('UPDATE orders SET Status = "Cancelled" WHERE OrderID = :orderId');
            $this->db->bind(':orderId', $data['order_id']);
            $this->db->execute();
            
            // Here you could add code to handle inventory updates
            // For example, restoring quantities for cancelled items
        }
        
        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("Process cancellation request error: " . $e->getMessage());
        return false;
    }
}


public function getSellerCancellationRequests($sellerId) {
    try {
        $this->db->query('SELECT oc.*, o.OrderID, o.Status AS OrderStatus, 
                        u.Username AS CustomerName
                        FROM order_cancellations oc
                        JOIN orders o ON oc.OrderID = o.OrderID
                        JOIN users u ON oc.UserID = u.UserID
                        JOIN orders_items oi ON o.OrderID = oi.OrderID
                        JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                        WHERE p.SellerID = :sellerId AND oc.Status = "Pending"
                        GROUP BY oc.CancellationID
                        ORDER BY oc.RequestDate DESC');
        
        $this->db->bind(':sellerId', $sellerId);
        return $this->db->resultSet();
    } catch (Exception $e) {
        error_log("Get seller cancellation requests error: " . $e->getMessage());
        return [];
    }
}

// Check if cancellation is allowed based on order status
public function isCancellationAllowed($orderId) {
    try {
        $this->db->query('SELECT Status FROM orders WHERE OrderID = :orderId');
        $this->db->bind(':orderId', $orderId);
        $order = $this->db->single();
        
        if (!$order) {
            return false;
        }
        
        // Orders can only be cancelled if they are Pending or Processing
        return in_array($order->Status, ['Pending', 'Processing']);
    } catch (Exception $e) {
        error_log("Check cancellation allowed error: " . $e->getMessage());
        return false;
    }
}







}