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
    public function getInventoryItems($officerId) {
        $this->db->query('SELECT * FROM regional_inventory 
                         WHERE RegionalOfficerID = :officerId 
                         ORDER BY LastUpdated DESC');
        $this->db->bind(':officerId', $officerId);
        
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
        // Generate ItemID (Format: RI + 5 random digits)
        $itemId = 'RI' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        $this->db->query('INSERT INTO regional_inventory (ItemID, ItemName, Category, Quantity, Status, RegionalOfficerID) 
                         VALUES (:itemId, :itemName, :category, :quantity, :status, :officerId)');
        
        $this->db->bind(':itemId', $itemId);
        $this->db->bind(':itemName', $data['itemName']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':officerId', $data['officerId']);
        
        return $this->db->execute();
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
    
    // Allocate inventory items to donation request
    public function allocateToRequest($requestId, $itemId, $quantity, $officerId) {
        // First check if we have enough quantity
        $item = $this->getInventoryItemById($itemId);
        
        if(!$item || $item->Quantity < $quantity || $item->Status != 'Available') {
            return false;
        }
        
        // Update item quantity and status if needed
        $this->db->query('UPDATE regional_inventory 
                         SET Quantity = Quantity - :quantity,
                             Status = CASE WHEN (Quantity - :quantity) <= 0 THEN "Distributed" ELSE Status END
                         WHERE ItemID = :itemId AND RegionalOfficerID = :officerId');
        
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':itemId', $itemId);
        $this->db->bind(':officerId', $officerId);
        
        if($this->db->execute()) {
            // Update the request received quantity
            $this->db->query('UPDATE nonmonetary_donation_details 
                             SET QuantityReceived = QuantityReceived + :quantity 
                             WHERE RequestID = :requestId');
            
            $this->db->bind(':quantity', $quantity);
            $this->db->bind(':requestId', $requestId);
            
            if($this->db->execute()) {
                // Check if all quantity is received, update request status if needed
                $this->db->query('UPDATE donation_requests dr
                                 JOIN nonmonetary_donation_details nmd ON dr.RequestID = nmd.RequestID
                                 SET dr.RequestStatus = "Completed"
                                 WHERE dr.RequestID = :requestId
                                 AND nmd.QuantityReceived >= nmd.QuantityNeeded');
                
                $this->db->bind(':requestId', $requestId);
                return $this->db->execute();
            }
        }
        
        return false;
    }
}