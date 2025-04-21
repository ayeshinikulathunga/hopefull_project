<?php
class Feedback {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Get impact reports/feedback for a specific donor
    public function getFeedbackByDonor($donorId) {
        $this->db->query('SELECT fr.*, d.DonationID, d.DonationType, d.Amount, d.QuantityDonated,
                        dr.Title as RequestTitle, dr.Category,
                        r.FirstName as RecipientFirstName, r.LastName as RecipientLastName, 
                        r.OrganizationType
                        FROM feedback_reports fr
                        JOIN donations d ON fr.DonationID = d.DonationID
                        JOIN donation_requests dr ON d.RequestID = dr.RequestID
                        JOIN recipients r ON fr.RecipientID = r.RecipientID
                        WHERE d.DonorID = :donorId
                        ORDER BY fr.CreatedDate DESC');
                        
        $this->db->bind(':donorId', $donorId);
        return $this->db->resultSet();
    }
    
    // Get recent impact stories for the homepage
    public function getRecentImpactStories($limit = 3) {
        $this->db->query('SELECT fr.*, dr.Title as RequestTitle, dr.Category,
                        r.FirstName as RecipientFirstName, r.LastName as RecipientLastName, 
                        r.OrganizationType
                        FROM feedback_reports fr
                        JOIN donation_requests dr ON fr.RequestID = dr.RequestID
                        JOIN recipients r ON fr.RecipientID = r.RecipientID
                        WHERE fr.FeedbackType = "ImpactReport"
                        ORDER BY fr.CreatedDate DESC
                        LIMIT :limit');
                        
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
}