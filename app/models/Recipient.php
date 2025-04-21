<?php
class Recipient {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Get recipient by ID
    public function getRecipientById($id) {
        $this->db->query('SELECT * FROM recipients WHERE RecipientID = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    // Get all donation requests by recipient
    public function getRequestsByRecipient($recipientId) {
        $this->db->query('SELECT * FROM donation_requests 
                         WHERE RecipientID = :recipientId 
                         ORDER BY CreatedDate DESC');
        $this->db->bind(':recipientId', $recipientId);
        
        return $this->db->resultSet();
    }
    
    // Get request details including monetary or non-monetary specific details
    public function getRequestDetails($requestId) {
        $this->db->query('SELECT * FROM donation_requests WHERE RequestID = :requestId');
        $this->db->bind(':requestId', $requestId);
        
        $request = $this->db->single();
        
        if($request) {
            if($request->RequestType == 'Monetary') {
                $this->db->query('SELECT * FROM monetary_donation_details WHERE RequestID = :requestId');
                $this->db->bind(':requestId', $requestId);
                $request->details = $this->db->single();
            } else {
                $this->db->query('SELECT * FROM nonmonetary_donation_details WHERE RequestID = :requestId');
                $this->db->bind(':requestId', $requestId);
                $request->details = $this->db->single();
            }
        }
        
        return $request;
    }
}