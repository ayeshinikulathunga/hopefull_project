<?php
class Feedback {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Create a feedback report for a donation
     * @param array $data The feedback data
     * @return bool True if successful, false otherwise
     */
    public function createFeedbackReport($data) {
        try {
            // First get the RequestID from the donation
            $this->db->query('SELECT RequestID FROM donations WHERE DonationID = :donationId');
            $this->db->bind(':donationId', $data['donationId']);
            $donation = $this->db->single();
            
            if (!$donation) {
                error_log("Feedback Creation Error: Donation not found with ID " . $data['donationId']);
                return false;
            }
            
            $requestId = $donation->RequestID;
            
            // Generate FeedbackID (Format: FB + 5 random digits)
            $feedbackId = 'FB' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            
            // Insert feedback record with RequestID
            $this->db->query('INSERT INTO feedback_reports (
                FeedbackID, DonationID, RecipientID, RequestID, FeedbackType, Content, Rating,
                CreatedDate
            ) VALUES (
                :feedbackId, :donationId, :recipientId, :requestId, :feedbackType, :content, :rating,
                NOW()
            )');
            
            $this->db->bind(':feedbackId', $feedbackId);
            $this->db->bind(':donationId', $data['donationId']);
            $this->db->bind(':recipientId', $data['recipientId']);
            $this->db->bind(':requestId', $requestId);  // Include the RequestID
            $this->db->bind(':feedbackType', $data['feedbackType']);
            $this->db->bind(':content', $data['content']);
            $this->db->bind(':rating', $data['rating']);
            
            return $this->db->execute();
            
        } catch(Exception $e) {
            error_log("Feedback Creation Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all feedback reports for a donation
     * @param string $donationId The donation ID
     * @return array Array of feedback reports
     */
    public function getFeedbackByDonation($donationId) {
        $this->db->query('SELECT fr.*, 
                         r.FirstName as RecipientFirstName, r.LastName as RecipientLastName
                         FROM feedback_reports fr
                         JOIN recipients r ON fr.RecipientID = r.RecipientID
                         WHERE fr.DonationID = :donationId
                         ORDER BY fr.CreatedDate DESC');
        
        $this->db->bind(':donationId', $donationId);
        
        return $this->db->resultSet();
    }

    /**
     * Get all feedback reports created by a recipient
     * @param string $recipientId The recipient ID
     * @return array Array of feedback reports
     */
    public function getFeedbackByRecipient($recipientId) {
        $this->db->query('SELECT fr.*, 
                         d.DonationID, d.DonationType, d.Amount, d.QuantityDonated, d.Status,
                         dr.Title as RequestTitle, dr.Category,
                         dn.FirstName as DonorFirstName, dn.LastName as DonorLastName
                         FROM feedback_reports fr
                         JOIN donations d ON fr.DonationID = d.DonationID
                         JOIN donation_requests dr ON fr.RequestID = dr.RequestID
                         JOIN donors dn ON d.DonorID = dn.DonorID
                         WHERE fr.RecipientID = :recipientId
                         ORDER BY fr.CreatedDate DESC');
        
        $this->db->bind(':recipientId', $recipientId);
        
        return $this->db->resultSet();
    }

    /**
     * Delete a feedback report
     * @param string $feedbackId The feedback ID
     * @param string $recipientId The recipient ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function deleteFeedback($feedbackId, $recipientId) {
        // Check if feedback belongs to this recipient
        $this->db->query('SELECT * FROM feedback_reports 
                         WHERE FeedbackID = :feedbackId 
                         AND RecipientID = :recipientId');
        
        $this->db->bind(':feedbackId', $feedbackId);
        $this->db->bind(':recipientId', $recipientId);
        
        $feedback = $this->db->single();
        
        if(!$feedback) {
            return false;
        }
        
        // Delete feedback
        $this->db->query('DELETE FROM feedback_reports 
                         WHERE FeedbackID = :feedbackId');
        
        $this->db->bind(':feedbackId', $feedbackId);
        
        return $this->db->execute();
    }

    /**
 * Add a ViewedByDonor column to the feedback_reports table 
 * (You would need to run this SQL in your database):
 * 
 * ALTER TABLE feedback_reports ADD COLUMN ViewedByDonor TINYINT(1) DEFAULT 0;
 */

/**
 * Get feedback by ID
 * @param string $feedbackId The feedback ID
 * @return object|bool Feedback or false if not found
 */
public function getFeedbackById($feedbackId) {
    $this->db->query('SELECT fr.*, 
                     d.DonorID, d.DonationType, d.Amount, d.QuantityDonated,
                     dr.Title as RequestTitle
                     FROM feedback_reports fr
                     JOIN donations d ON fr.DonationID = d.DonationID
                     JOIN donation_requests dr ON fr.RequestID = dr.RequestID
                     WHERE fr.FeedbackID = :feedbackId');
    
    $this->db->bind(':feedbackId', $feedbackId);
    
    return $this->db->single();
}

/**
 * Mark feedback as viewed by donor
 * @param string $feedbackId The feedback ID
 * @return bool True if successful, false otherwise
 */
public function markAsViewed($feedbackId) {
    $this->db->query('UPDATE feedback_reports SET ViewedByDonor = 1 WHERE FeedbackID = :feedbackId');
    $this->db->bind(':feedbackId', $feedbackId);
    
    return $this->db->execute();
}

/**
 * Get donor's feedback statistics
 * @param string $donorId The donor ID
 * @return object Feedback statistics
 */
public function getDonorFeedbackStats($donorId) {
    $this->db->query('SELECT 
                     COUNT(*) as TotalFeedback,
                     SUM(CASE WHEN fr.ViewedByDonor = 0 THEN 1 ELSE 0 END) as UnreadFeedback,
                     SUM(CASE WHEN fr.FeedbackType = "ImpactReport" THEN 1 ELSE 0 END) as ImpactReports,
                     SUM(CASE WHEN fr.FeedbackType = "General" THEN 1 ELSE 0 END) as GeneralFeedback
                     FROM feedback_reports fr
                     JOIN donations d ON fr.DonationID = d.DonationID
                     WHERE d.DonorID = :donorId');
    
    $this->db->bind(':donorId', $donorId);
    
    return $this->db->single();
}

// Get feedback metrics for a recipient
public function getFeedbackMetricsForRecipient($recipientId) {
    // Initialize metrics object
    $metrics = (object)[
        'totalFeedback' => 0,
        'averageRating' => 0,
        'positiveCount' => 0,
        'neutralCount' => 0,
        'negativeCount' => 0,
        'impactReports' => 0
    ];
    
    // Get general feedback metrics
    $this->db->query('SELECT 
                        COUNT(*) as totalFeedback,
                        AVG(Rating) as averageRating,
                        SUM(CASE WHEN Rating >= 4 THEN 1 ELSE 0 END) as positiveCount,
                        SUM(CASE WHEN Rating = 3 THEN 1 ELSE 0 END) as neutralCount,
                        SUM(CASE WHEN Rating <= 2 THEN 1 ELSE 0 END) as negativeCount,
                        SUM(CASE WHEN FeedbackType = "ImpactReport" THEN 1 ELSE 0 END) as impactReports
                     FROM feedback_reports
                     WHERE RecipientID = :recipientId');
    
    $this->db->bind(':recipientId', $recipientId);
    $feedbackStats = $this->db->single();
    
    if ($feedbackStats) {
        $metrics->totalFeedback = $feedbackStats->totalFeedback;
        $metrics->averageRating = $feedbackStats->averageRating ? round($feedbackStats->averageRating, 1) : 0;
        $metrics->positiveCount = $feedbackStats->positiveCount;
        $metrics->neutralCount = $feedbackStats->neutralCount;
        $metrics->negativeCount = $feedbackStats->negativeCount;
        $metrics->impactReports = $feedbackStats->impactReports;
    }
    
    return $metrics;
}



}