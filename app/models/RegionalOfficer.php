<?php
class RegionalOfficer {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Get regional officer by ID
    public function getOfficerById($id) {
        $this->db->query('SELECT * FROM regional_officers WHERE RegionalOfficerID = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    // Get regional officer by user ID
    public function getOfficerByUserId($userId) {
        $this->db->query('SELECT * FROM regional_officers WHERE UserID = :userId');
        $this->db->bind(':userId', $userId);
        
        return $this->db->single();
    }
    
    // Get inventory items for this officer
    public function getInventoryItems($officerId = null) {
        if ($officerId) {
            $this->db->query("SELECT * FROM regional_inventory WHERE Quantity > 0 AND RegionalOfficerID = :officerId");
            $this->db->bind(':officerId', $officerId);
        } else {
            $this->db->query("SELECT * FROM regional_inventory WHERE Quantity > 0");
        }
        return $this->db->resultSet();
    }
    
    // Get inventory item by ID
    public function getInventoryItemById($itemId) {
        $this->db->query('SELECT * FROM regional_inventory WHERE ItemID = :itemId');
        $this->db->bind(':itemId', $itemId);
        
        return $this->db->single();
    }
    
    // Add new inventory item
    public function addInventoryItem($data) {
        // Generate a unique ItemID (Format: RI + 5 random digits)
        do {
            $itemId = 'RI' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            
            // Check if the generated ItemID already exists
            $this->db->query('SELECT COUNT(*) as count FROM regional_inventory WHERE ItemID = :itemId');
            $this->db->bind(':itemId', $itemId);
            $exists = $this->db->single()->count > 0;
        } while ($exists);
        
        try {
            // Prepare the SQL query to insert the new inventory item
            $this->db->query('INSERT INTO regional_inventory (ItemID, ItemName, Category, Quantity, Status, RegionalOfficerID) 
                              VALUES (:itemId, :itemName, :category, :quantity, :status, :officerId)');
            
            // Bind the parameters
            $this->db->bind(':itemId', $itemId);
            $this->db->bind(':itemName', $data['itemName']);
            $this->db->bind(':category', $data['category']);
            $this->db->bind(':quantity', $data['quantity']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':officerId', $data['officerId']);
            
            // Execute the query and return the result
            return $this->db->execute();
        } catch (Exception $e) {
            // Log the error or handle it as needed
            error_log('Error adding inventory item: ' . $e->getMessage());
            return false;
        }
    }
    
    // Update inventory item
    public function updateInventoryItem($data) {
        $this->db->query('UPDATE regional_inventory 
                         SET ItemName = :itemName, 
                             Category = :category, 
                             Quantity = :quantity, 
                             Status = :status 
                         WHERE ItemID = :itemId AND RegionalOfficerID = :officerId');
        
        $this->db->bind(':itemName', $data['itemName']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':itemId', $data['itemId']);
        $this->db->bind(':officerId', $data['officerId']);
        
        return $this->db->execute();
    }
    
    // Delete inventory item
    public function deleteInventoryItem($itemId, $officerId) {
        $this->db->query('DELETE FROM regional_inventory 
                         WHERE ItemID = :itemId AND RegionalOfficerID = :officerId');
        
        $this->db->bind(':itemId', $itemId);
        $this->db->bind(':officerId', $officerId);
        
        return $this->db->execute();
    }
    
    // Get pending non-monetary donation requests
    public function getPendingNonMonetaryRequests() {
        $this->db->query('SELECT dr.*, r.FirstName, r.LastName, nmd.* 
                         FROM donation_requests dr 
                         JOIN recipients r ON dr.RecipientID = r.RecipientID 
                         JOIN nonmonetary_donation_details nmd ON dr.RequestID = nmd.RequestID 
                         WHERE dr.RequestType = "NonMonetary" 
                         AND dr.VerificationStatus = "Approved" 
                         AND dr.RequestStatus = "InProgress" 
                         ORDER BY dr.CreatedDate DESC');
        
        return $this->db->resultSet();
    }
    
    // Get inventory statistics
    public function getInventoryStats($officerId) {
        $this->db->query('SELECT 
                         COUNT(*) as totalItems,
                         SUM(Quantity) as totalQuantity,
                         SUM(CASE WHEN Status = "Available" THEN Quantity ELSE 0 END) as availableQuantity,
                         SUM(CASE WHEN Status = "Reserved" THEN Quantity ELSE 0 END) as reservedQuantity,
                         SUM(CASE WHEN Status = "Distributed" THEN Quantity ELSE 0 END) as distributedQuantity,
                         COUNT(DISTINCT Category) as categories
                         FROM regional_inventory
                         WHERE RegionalOfficerID = :officerId');
        
        $this->db->bind(':officerId', $officerId);
        return $this->db->single();
    }
    
    // Get approved non-monetary donation requests that still need items
    public function getApprovedRequests() {
        $this->db->query("SELECT nmd.DetailID, nmd.RequestID, nmd.ItemName, nmd.QuantityNeeded, nmd.QuantityReceived, 
                                 nmd.Province, nmd.DropOffLocation, nmd.DropOffTime 
                          FROM nonmonetary_donation_details nmd
                          WHERE nmd.QuantityNeeded > nmd.QuantityReceived
                          ORDER BY nmd.DropOffTime ASC");
        return $this->db->resultSet();
    }

    // Get nonmonetary donation detail by ID
    public function getNonMonetaryDetailById($detailId) {
        $this->db->query("SELECT * FROM nonmonetary_donation_details WHERE DetailID = :detailId");
        $this->db->bind(':detailId', $detailId);
        return $this->db->single();
    }

    // Get nonmonetary donation details by request ID
    public function getNonMonetaryDetailsByRequestId($requestId) {
        $this->db->query("SELECT * FROM nonmonetary_donation_details 
                          WHERE RequestID = :requestId AND QuantityNeeded > QuantityReceived");
        $this->db->bind(':requestId', $requestId);
        return $this->db->resultSet();
    }

    // Get available inventory items that match donation requirements
    public function getMatchingInventoryItems($itemName) {
        // First look for exact match
        $this->db->query("SELECT * FROM regional_inventory 
                          WHERE Status = 'Available' AND Quantity > 0 AND ItemName = :itemName
                          ORDER BY ItemName");
        $this->db->bind(':itemName', $itemName);
        $exactMatches = $this->db->resultSet();
        
        if (!empty($exactMatches)) {
            return $exactMatches;
        }
        
        // If no exact match, look for similar items
        $keywords = explode(' ', $itemName);
        $likeClauses = [];
        $params = [];
        
        foreach ($keywords as $index => $keyword) {
            if (strlen($keyword) >= 3) { // Only use keywords of sufficient length
                $param = ':keyword' . $index;
                $likeClauses[] = "ItemName LIKE $param";
                $params[$param] = '%' . $keyword . '%';
            }
        }
        
        if (!empty($likeClauses)) {
            $likeQuery = "SELECT * FROM regional_inventory 
                          WHERE Status = 'Available' AND Quantity > 0 
                          AND (" . implode(' OR ', $likeClauses) . ")
                          ORDER BY ItemName";
            
            $this->db->query($likeQuery);
            
            foreach ($params as $param => $value) {
                $this->db->bind($param, $value);
            }
            
            return $this->db->resultSet();
        }
        
        // If no matches or no valid keywords, return all available items
        $this->db->query("SELECT * FROM regional_inventory 
                          WHERE Status = 'Available' AND Quantity > 0
                          ORDER BY Category, ItemName");
        return $this->db->resultSet();
    }
    // Allocate inventory to donation request
    // Allocate inventory to donation request
// Allocate inventory to donation request
public function allocateInventoryToRequest($detailId, $itemId, $quantity) {
    // Start a transaction to ensure data integrity
    $this->db->beginTransaction();

    try {
        // 1. Get current inventory quantity
        $this->db->query("SELECT Quantity FROM regional_inventory WHERE ItemID = :itemId FOR UPDATE");
        $this->db->bind(':itemId', $itemId);
        $inventory = $this->db->single();

        if (!$inventory || $inventory->Quantity < $quantity) {
            // Not enough inventory
            $this->db->cancelTransaction();
            return false;
        }

        // 2. Deduct quantity from regional inventory
        $this->db->query("UPDATE regional_inventory 
                          SET Quantity = Quantity - :qty 
                          WHERE ItemID = :itemId");
        $this->db->bind(':qty', $quantity);
        $this->db->bind(':itemId', $itemId);
        $this->db->execute();

        // 3. Update quantity received in donation detail
        $this->db->query("UPDATE nonmonetary_donation_details 
                          SET QuantityReceived = QuantityReceived + :qty 
                          WHERE DetailID = :detailId");
        $this->db->bind(':qty', $quantity);
        $this->db->bind(':detailId', $detailId);
        $this->db->execute();

        // 4. Commit transaction
        $this->db->commit();
        return true;
    } catch (Exception $e) {
        // Rollback on error
        $this->db->cancelTransaction();
        return false;
    }
}

    }
