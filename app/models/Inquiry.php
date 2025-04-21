<?php
class Inquiry {
    private $db;

    public function __construct() {
        $this->db = new Database;
        $this->ensureInquiriesTableExists();
    }

    // Create the inquiries table if it doesn't exist
    private function ensureInquiriesTableExists() {
        try {
            // Check if table exists
            $this->db->query("SHOW TABLES LIKE 'product_inquiries'");
            $tableExists = $this->db->rowCount() > 0;
            
            if (!$tableExists) {
                // Create the table
                $this->db->query("CREATE TABLE `product_inquiries` (
                    `InquiryID` varchar(10) NOT NULL CHECK (`InquiryID` like 'INQ%'),
                    `UserID` varchar(10) NOT NULL,
                    `ProductID` varchar(10) NOT NULL,
                    `Message` text NOT NULL,
                    `Response` text DEFAULT NULL,
                    `Status` enum('Pending','Answered','Closed') DEFAULT 'Pending',
                    `CreatedDate` datetime DEFAULT current_timestamp(),
                    `ResponseDate` datetime DEFAULT NULL,
                    PRIMARY KEY (`InquiryID`),
                    KEY `UserID` (`UserID`),
                    KEY `ProductID` (`ProductID`),
                    CONSTRAINT `product_inquiries_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
                    CONSTRAINT `product_inquiries_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `marketplace_inventory` (`ProductID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
                
                $this->db->execute();
                error_log("Created missing product_inquiries table");
            }
        } catch (Exception $e) {
            error_log("Error checking/creating product_inquiries table: " . $e->getMessage());
        }
    }

    // Create a new inquiry
    public function createInquiry($data) {
        // Generate InquiryID (Format: INQ + 5 random digits)
        $inquiryId = 'INQ' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        $this->db->query('INSERT INTO product_inquiries (InquiryID, UserID, ProductID, Message, Status) 
                         VALUES (:inquiryId, :userId, :productId, :message, "Pending")');
        
        $this->db->bind(':inquiryId', $inquiryId);
        $this->db->bind(':userId', $data['user_id']);
        $this->db->bind(':productId', $data['product_id']);
        $this->db->bind(':message', $data['message']);
        
        // Execute
        if($this->db->execute()) {
            return $inquiryId;
        } else {
            return false;
        }
    }

    // Get all inquiries for a specific user
    public function getUserInquiries($userId) {
        $this->db->query('SELECT pi.*, p.ProductName 
                         FROM product_inquiries pi
                         JOIN marketplace_inventory p ON pi.ProductID = p.ProductID
                         WHERE pi.UserID = :userId
                         ORDER BY pi.CreatedDate DESC');
        
        $this->db->bind(':userId', $userId);
        
        $results = $this->db->resultSet();
        
        return $results;
    }

    // Get a single inquiry by ID
    public function getInquiryById($inquiryId) {
        $this->db->query('SELECT pi.*, p.ProductName
                         FROM product_inquiries pi
                         JOIN marketplace_inventory p ON pi.ProductID = p.ProductID
                         WHERE pi.InquiryID = :inquiryId');
        
        $this->db->bind(':inquiryId', $inquiryId);
        
        $row = $this->db->single();
        
        return $row;
    }

    // Update inquiry response
    public function respondToInquiry($inquiryId, $response) {
        $this->db->query('UPDATE product_inquiries 
                         SET Response = :response, 
                             Status = "Answered",
                             ResponseDate = CURRENT_TIMESTAMP
                         WHERE InquiryID = :inquiryId');
        
        $this->db->bind(':response', $response);
        $this->db->bind(':inquiryId', $inquiryId);
        
        return $this->db->execute();
    }

    // Close an inquiry
    public function closeInquiry($inquiryId) {
        $this->db->query('UPDATE product_inquiries 
                         SET Status = "Closed" 
                         WHERE InquiryID = :inquiryId');
        
        $this->db->bind(':inquiryId', $inquiryId);
        
        return $this->db->execute();
    }

    // Count pending inquiries (for admin dashboard)
    public function countPendingInquiries() {
        $this->db->query('SELECT COUNT(*) as count FROM product_inquiries WHERE Status = "Pending"');
        $row = $this->db->single();
        return $row->count;
    }
}