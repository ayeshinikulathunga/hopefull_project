<?php
class Donor {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getDonorStats($donorId) {
        $this->db->query('SELECT * FROM donors WHERE DonorID = :donorId');
        $this->db->bind(':donorId', $donorId);
        
        return $this->db->single();
    }

    public function getDonorDonationHistory($donorId) {
        $this->db->query('SELECT d.*, dr.Title, dr.Category, dr.RequestType 
                         FROM donations d 
                         JOIN donation_requests dr ON d.RequestID = dr.RequestID 
                         WHERE d.DonorID = :donorId 
                         ORDER BY d.DonationDate DESC');
        
        $this->db->bind(':donorId', $donorId);
        
        return $this->db->resultSet();
    }

    public function updateDonorStatistics($donorId, $amount = 0) {
        // Get current stats
        $this->db->query('SELECT TotalDonations, DonationCount FROM donors WHERE DonorID = :donorId');
        $this->db->bind(':donorId', $donorId);
        $currentStats = $this->db->single();
        
        // Calculate new values
        $newTotal = $currentStats->TotalDonations + $amount;
        $newCount = $currentStats->DonationCount + 1;
        
        // Update donor statistics
        $this->db->query('UPDATE donors SET TotalDonations = :totalDonations, DonationCount = :donationCount WHERE DonorID = :donorId');
        $this->db->bind(':totalDonations', $newTotal);
        $this->db->bind(':donationCount', $newCount);
        $this->db->bind(':donorId', $donorId);
        
        return $this->db->execute();
    }


    /**
 * Get donor by ID
 * @param string $donorId The donor ID
 * @return object|bool The donor data or false if not found
 */
public function getDonorById($donorId) {
    $this->db->query('SELECT * FROM donors WHERE DonorID = :donorId');
    $this->db->bind(':donorId', $donorId);
    
    return $this->db->single();
}

/**
 * Update donor information
 * @param array $data The donor data to update
 * @return bool True if successful, false otherwise
 */
public function updateDonor($data) {
    $this->db->query('UPDATE donors 
                     SET FirstName = :firstName, 
                         LastName = :lastName, 
                         ContactNumber = :contactNumber, 
                         AnonymousPreference = :anonymousPreference 
                     WHERE DonorID = :donorId');
    
    $this->db->bind(':firstName', $data['firstName']);
    $this->db->bind(':lastName', $data['lastName']);
    $this->db->bind(':contactNumber', $data['contactNumber']);
    $this->db->bind(':anonymousPreference', $data['anonymousPreference']);
    $this->db->bind(':donorId', $data['donorId']);
    
    return $this->db->execute();
}

/**
 * Delete donor record
 * @param string $donorId The donor ID
 * @return bool True if successful, false otherwise
 */
public function deleteDonor($donorId) {
    $this->db->query('DELETE FROM donors WHERE DonorID = :donorId');
    $this->db->bind(':donorId', $donorId);
    
    return $this->db->execute();
}

/**
 * Get donor's ranking compared to others
 * @param string $donorId The donor ID
 * @return int The donor's rank position
 */
public function getDonorRanking($donorId) {
    // First, get the donor's total donations
    $this->db->query('SELECT TotalDonations FROM donors WHERE DonorID = :donorId');
    $this->db->bind(':donorId', $donorId);
    $donor = $this->db->single();
    
    if (!$donor) {
        return 0;
    }
    
    $totalDonations = $donor->TotalDonations;
    
    // Count how many donors have a higher total
    $this->db->query('SELECT COUNT(*) as rank FROM donors WHERE TotalDonations > :totalDonations');
    $this->db->bind(':totalDonations', $totalDonations);
    $result = $this->db->single();
    
    // Add 1 to get the actual rank (1-based index)
    return $result->rank + 1;
}

/**
 * Get top donors based on total donation amount
 * @param int $limit Number of top donors to return
 * @return array Array of top donors
 */
public function getTopDonors($limit = 10) {
    $this->db->query('SELECT d.DonorID, d.FirstName, d.LastName, d.TotalDonations, d.DonationCount, 
                            COUNT(DISTINCT ub.BadgeID) as BadgeCount
                     FROM donors d
                     LEFT JOIN user_badges ub ON d.DonorID = ub.DonorID
                     WHERE d.TotalDonations > 0
                     GROUP BY d.DonorID
                     ORDER BY d.TotalDonations DESC
                     LIMIT :limit');
    
    $this->db->bind(':limit', $limit, PDO::PARAM_INT);
    
    return $this->db->resultSet();
}

}