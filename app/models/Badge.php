<?php
class Badge {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllBadges() {
        $this->db->query('SELECT * FROM badges ORDER BY BadgeName');
        return $this->db->resultSet();
    }

    public function getBadgeById($badgeId) {
        $this->db->query('SELECT * FROM badges WHERE BadgeID = :badgeId');
        $this->db->bind(':badgeId', $badgeId);
        
        return $this->db->single();
    }

    public function getDonorBadges($donorId) {
        $this->db->query('SELECT b.*, ub.AwardedDate FROM badges b 
                         JOIN user_badges ub ON b.BadgeID = ub.BadgeID 
                         WHERE ub.DonorID = :donorId 
                         ORDER BY ub.AwardedDate DESC');
        
        $this->db->bind(':donorId', $donorId);
        
        return $this->db->resultSet();
    }

    public function getDonorBadgeCount($donorId) {
        $this->db->query('SELECT COUNT(*) as badgeCount FROM user_badges WHERE DonorID = :donorId');
        $this->db->bind(':donorId', $donorId);
        
        $result = $this->db->single();
        return $result ? $result->badgeCount : 0;
    }

    public function checkAndAwardBadges($donorId) {
        // Get donor statistics
        $this->db->query('SELECT TotalDonations, DonationCount FROM donors WHERE DonorID = :donorId');
        $this->db->bind(':donorId', $donorId);
        $donorStats = $this->db->single();
        
        if(!$donorStats) {
            return false;
        }
        
        // Get all badges the donor doesn't have yet
        $this->db->query('SELECT b.* FROM badges b 
                         WHERE b.BadgeID NOT IN (
                             SELECT ub.BadgeID FROM user_badges ub WHERE ub.DonorID = :donorId
                         )');
        
        $this->db->bind(':donorId', $donorId);
        $availableBadges = $this->db->resultSet();
        
        $awardedBadges = [];
        
        foreach($availableBadges as $badge) {
            $awarded = false;
            
            // Check criteria for each badge type
            switch($badge->BadgeName) {
                case 'First Donation':
                    if($donorStats->DonationCount >= 1) {
                        $awarded = $this->awardBadge($donorId, $badge->BadgeID);
                    }
                    break;
                    
                case 'Generous Donor':
                    if($donorStats->TotalDonations >= 5000) {
                        $awarded = $this->awardBadge($donorId, $badge->BadgeID);
                    }
                    break;
                    
                case 'Serial Donor':
                    if($donorStats->DonationCount >= 5) {
                        $awarded = $this->awardBadge($donorId, $badge->BadgeID);
                    }
                    break;
                    
                case 'Silver Supporter':
                    if($donorStats->TotalDonations >= 10000) {
                        $awarded = $this->awardBadge($donorId, $badge->BadgeID);
                    }
                    break;
                    
                case 'Gold Supporter':
                    if($donorStats->TotalDonations >= 25000) {
                        $awarded = $this->awardBadge($donorId, $badge->BadgeID);
                    }
                    break;
                    
                case 'Platinum Hero':
                    if($donorStats->TotalDonations >= 50000) {
                        $awarded = $this->awardBadge($donorId, $badge->BadgeID);
                    }
                    break;
                    
                // Additional badge criteria can be added here
            }
            
            if($awarded) {
                $awardedBadges[] = $badge;
            }
        }
        
        return $awardedBadges;
    }

    private function awardBadge($donorId, $badgeId) {
        // Generate UserBadgeID (Format: UB + 5 random digits)
        $userBadgeId = 'UB' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        $this->db->query('INSERT INTO user_badges (UserBadgeID, DonorID, BadgeID) 
                         VALUES (:userBadgeId, :donorId, :badgeId)');
        
        $this->db->bind(':userBadgeId', $userBadgeId);
        $this->db->bind(':donorId', $donorId);
        $this->db->bind(':badgeId', $badgeId);
        
        return $this->db->execute();
    }
}