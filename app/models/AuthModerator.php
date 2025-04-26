<?php
class AuthModerator {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Get moderator by ID
    public function getModeratorById($id) {
        $this->db->query('SELECT * FROM auth_moderators WHERE ModeratorID = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    // Get moderator by user ID
    public function getModeratorByUserId($userId) {
        $this->db->query('SELECT * FROM auth_moderators WHERE UserID = :userId');
        $this->db->bind(':userId', $userId);
        
        return $this->db->single();
    }
    
    // Get all pending recipients
    public function getPendingRecipients() {
        $this->db->query('SELECT r.*, u.Email, u.Username, u.RegisteredDate 
                        FROM recipients r 
                        JOIN users u ON r.UserID = u.UserID 
                        WHERE r.VerificationStatus = "Pending" 
                        ORDER BY u.RegisteredDate DESC');
        
        return $this->db->resultSet();
    }
    
    // Get all pending donation requests
    public function getPendingRequests() {
        $this->db->query('SELECT dr.*, r.FirstName, r.LastName 
                        FROM donation_requests dr 
                        JOIN recipients r ON dr.RecipientID = r.RecipientID 
                        WHERE dr.VerificationStatus = "Pending" 
                        ORDER BY dr.CreatedDate DESC');
        
        return $this->db->resultSet();
    }

    public function getApprovedRequests() {
        $this->db->query("SELECT * FROM donation_requests WHERE VerificationStatus = 'Approved'");
        return $this->db->resultSet();
    }
    
    
    // Get recently verified recipients
    public function getRecentlyVerifiedRecipients($limit = 5) {
        $this->db->query('SELECT r.*, u.Email, am.FirstName as ModeratorFirstName, am.LastName as ModeratorLastName 
                        FROM recipients r 
                        JOIN users u ON r.UserID = u.UserID 
                        LEFT JOIN auth_moderators am ON r.ModeratorID = am.ModeratorID
                        WHERE r.VerificationStatus != "Pending" 
                        ORDER BY r.ApprovalDate DESC 
                        LIMIT :limit');
        
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
    
    // Get verification statistics
    public function getVerificationStats() {
        $this->db->query('SELECT 
                         (SELECT COUNT(*) FROM recipients WHERE VerificationStatus = "Pending") as pendingRecipients,
                         (SELECT COUNT(*) FROM recipients WHERE VerificationStatus = "Approved") as approvedRecipients,
                         (SELECT COUNT(*) FROM recipients WHERE VerificationStatus = "Rejected") as rejectedRecipients,
                         (SELECT COUNT(*) FROM donation_requests WHERE VerificationStatus = "Pending") as pendingRequests,
                         (SELECT COUNT(*) FROM donation_requests WHERE VerificationStatus = "Approved") as approvedRequests,
                         (SELECT COUNT(*) FROM donation_requests WHERE VerificationStatus = "Rejected") as rejectedRequests');
        
        return $this->db->single();
    }

    public function getRequestStats() {
        $this->db->query('SELECT 
                        (SELECT COUNT(*) FROM donation_requests WHERE VerificationStatus = "Pending") as pendingRequests,
                        (SELECT COUNT(*) FROM donation_requests WHERE RequestStatus = "InProgress") as inProgressRequests,
                        (SELECT COUNT(*) FROM donation_requests WHERE RequestStatus = "Completed") as completedRequests,
                        (SELECT COUNT(*) FROM donation_requests WHERE RequestStatus = "Expired") as expiredRequests');
        
        return $this->db->single();
    }
    
    // Verify recipient
    public function verifyRecipient($recipientId, $status) {
        $this->db->query('UPDATE recipients 
                          SET VerificationStatus = :status 
                          WHERE RecipientID = :recipientId');
        $this->db->bind(':status', $status);
        $this->db->bind(':recipientId', $recipientId);
        
        return $this->db->execute();
    }
    
    // Approve a recipient
    public function approve($recipientID) {
        $this->db->query('UPDATE recipients SET VerificationStatus = :status WHERE RecipientID = :recipientID');
        $this->db->bind(':status', 'Approved'); // Use a string for the status
        $this->db->bind(':recipientID', $recipientID);
        
        return $this->db->execute(); // Execute the query
    }
    
    public function reject($recipientID) {
        $this->db->query('UPDATE recipients SET VerificationStatus = :status WHERE RecipientID = :recipientID');
        $this->db->bind(':status', 'Rejected'); // Use a string for the status
        $this->db->bind(':recipientID', $recipientID);
        
        return $this->db->execute(); // Execute the query
    }
    
    // Approve a donation request
    public function approveRequest($requestId, $moderatorId) {
        $this->db->query('UPDATE donation_requests SET 
                         VerificationStatus = "Approved",
                         RequestStatus = "InProgress",
                         ModeratorID = :moderatorId 
                         WHERE RequestID = :requestId');
        
        $this->db->bind(':moderatorId', $moderatorId);
        $this->db->bind(':requestId', $requestId);
        
        // If successful, update the verification count for the moderator
        if($this->db->execute()) {
            $this->db->query('UPDATE auth_moderators SET 
                             VerificationCount = VerificationCount + 1 
                             WHERE ModeratorID = :moderatorId');
            
            $this->db->bind(':moderatorId', $moderatorId);
            return $this->db->execute();
        }
        
        return false;
    }
    
    // Reject a donation request
    public function rejectRequest($requestId, $moderatorId) {
        $this->db->query('UPDATE donation_requests SET 
                         VerificationStatus = "Rejected",
                         ModeratorID = :moderatorId 
                         WHERE RequestID = :requestId');
        
        $this->db->bind(':moderatorId', $moderatorId);
        $this->db->bind(':requestId', $requestId);
        
        // If successful, update the verification count for the moderator
        if($this->db->execute()) {
            $this->db->query('UPDATE auth_moderators SET 
                             VerificationCount = VerificationCount + 1 
                             WHERE ModeratorID = :moderatorId');
            
            $this->db->bind(':moderatorId', $moderatorId);
            return $this->db->execute();
        }
        
        return false;
    }
    
}