<?php
class Admin {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }

    // Get total users count by type
    public function getUserCounts() {
        $this->db->query('SELECT 
            UserType, 
            COUNT(*) as count 
            FROM users 
            GROUP BY UserType');
        
        return $this->db->resultSet();
    }

    // Get recent user registrations
    public function getRecentUsers($limit = 10) {
        $this->db->query('SELECT 
            UserID, 
            Email, 
            Username, 
            UserType, 
            RegisteredDate 
            FROM users 
            ORDER BY RegisteredDate DESC 
            LIMIT :limit');
        
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // Get pending verification requests
    public function getPendingVerifications() {
        $this->db->query('SELECT 
            r.RecipientID, 
            u.Email, 
            r.FirstName, 
            r.LastName, 
            r.OrganizationType, 
            r.DocumentationURL
            FROM recipients r
            JOIN users u ON r.UserID = u.UserID
            WHERE r.VerificationStatus = "Pending"');
        
        return $this->db->resultSet();
    }

    // Update user status
    public function updateUserStatus($userId, $status) {
        $this->db->query('UPDATE users SET UserStatus = :status WHERE UserID = :userId');
        $this->db->bind(':status', $status);
        $this->db->bind(':userId', $userId);
        
        return $this->db->execute();
    }

    // Verify recipient
    public function verifyRecipient($recipientId, $status, $moderatorId) {
        $this->db->query('UPDATE recipients 
            SET VerificationStatus = :status, 
                ModeratorID = :moderatorId, 
                ApprovalDate = CURRENT_TIMESTAMP 
            WHERE RecipientID = :recipientId');
        
        $this->db->bind(':status', $status);
        $this->db->bind(':moderatorId', $moderatorId);
        $this->db->bind(':recipientId', $recipientId);
        
        return $this->db->execute();
    }
}