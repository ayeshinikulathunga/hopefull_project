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

    // Add these methods to the RegionalOfficer class

// Get non-monetary donation status for allocation
// This fixes the getNonMonetaryDonationStatus method to accept the officer ID parameter

// Updated getNonMonetaryDonationStatus method without filtering
// Corrected getNonMonetaryDonationStatus method
public function getNonMonetaryDonationStatus() {
    $this->db->query('SELECT d.DonationID, d.DonorID, 
                     CONCAT(dn.FirstName, " ", dn.LastName) as DonorName, 
                     nmd.ItemName, d.QuantityDonated, d.Status, 
                     nms.DropOffDate, nms.DropOffTime
                     FROM donations d
                     JOIN donors dn ON d.DonorID = dn.DonorID
                     JOIN donation_requests r ON d.RequestID = r.RequestID
                     JOIN nonmonetary_donation_details nmd ON r.RequestID = nmd.RequestID
                     LEFT JOIN non_monetary_donation_scheduling nms ON d.DonationID = nms.DonationID
                     WHERE d.DonationType = "NonMonetary" 
                     AND d.Status IN ("Pending", "Completed")
                     ORDER BY d.DonationDate DESC');
    
    return $this->db->resultSet();
}

// Corrected getPendingCancellationRequests method
public function getPendingCancellationRequests() {
    $this->db->query('SELECT dc.ID, dc.DonationID, dc.CancellationReason, dc.CancellationDate,
                     CONCAT(dn.FirstName, " ", dn.LastName) as DonorName, nmd.ItemName
                     FROM donation_cancellations dc
                     JOIN donations d ON dc.DonationID = d.DonationID
                     JOIN donors dn ON d.DonorID = dn.DonorID
                     JOIN donation_requests r ON d.RequestID = r.RequestID
                     JOIN nonmonetary_donation_details nmd ON r.RequestID = nmd.RequestID
                     ORDER BY dc.CancellationDate DESC');
    
    return $this->db->resultSet();
}
// Mark donation as received
public function markReceived($donationId) {
    $this->db->query('UPDATE donations SET Status = "Completed" WHERE DonationID = :donationId');
    $this->db->bind(':donationId', $donationId);
    return $this->db->execute();
}

public function markPending($donationId) {
        
        $this->db->query('UPDATE donations SET Status = "Pending" WHERE DonationID = :donationId');
        $this->db->bind(':donationId', $donationId);
        
        return $this->db->execute();
}

// Approve cancellation request
public function approveCancellation($donationId) {
    $this->db->query('DELETE FROM donation_cancellations WHERE DonationID = :donationId');
    $this->db->bind(':donationId', $donationId);
    $this->db->execute();

    $this->db->query('UPDATE donations SET Status = "Cancelled" WHERE DonationID = :donationId');
    $this->db->bind(':donationId', $donationId);
    $this->db->execute();

    return true;
}

public function rejectCancellation($donationId) {
    $this->db->beginTransaction();
    
        $this->db->query('DELETE FROM donation_cancellations WHERE DonationID = :donationId');
        $this->db->bind(':donationId', $donationId);
        $this->db->execute();
        

        $this->db->query('UPDATE donations SET Status = :status WHERE DonationID = :donationId');
        $this->db->bind(':status', 'pending');
        $this->db->bind(':donationId', $donationId);
        $this->db->execute();

        $this->db->commit();
        return true;
   
}

}