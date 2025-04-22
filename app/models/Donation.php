<?php
class Donation {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function createDonation($data) {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Generate DonationID (Format: DON + 5 random digits)
            $donationId = 'DON' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            
            // Insert donation record
            $this->db->query('INSERT INTO donations (DonationID, RequestID, DonorID, DonationType, Amount, QuantityDonated, IsAnonymous, Status) 
                             VALUES (:donationId, :requestId, :donorId, :donationType, :amount, :quantity, :isAnonymous, "Completed")');
            
            $this->db->bind(':donationId', $donationId);
            $this->db->bind(':requestId', $data['requestId']);
            $this->db->bind(':donorId', $data['donorId']);
            $this->db->bind(':donationType', $data['donationType']);
            $this->db->bind(':amount', $data['amount'] ?? null);
            $this->db->bind(':quantity', $data['quantity'] ?? null);
            $this->db->bind(':isAnonymous', $data['isAnonymous'] ? 1 : 0);
            
            $donationResult = $this->db->execute();
            
            // Update request details based on donation type
            if($data['donationType'] == 'Monetary') {
                $this->db->query('UPDATE monetary_donation_details 
                                 SET CurrentAmount = CurrentAmount + :amount 
                                 WHERE RequestID = :requestId');
                
                $this->db->bind(':amount', $data['amount']);
                $this->db->bind(':requestId', $data['requestId']);
                
                $updateResult = $this->db->execute();
            } else {
                $this->db->query('UPDATE nonmonetary_donation_details 
                                 SET QuantityReceived = QuantityReceived + :quantity 
                                 WHERE RequestID = :requestId');
                
                $this->db->bind(':quantity', $data['quantity']);
                $this->db->bind(':requestId', $data['requestId']);
                
                $updateResult = $this->db->execute();
            }
            
            // Update donor statistics
            $this->db->query('UPDATE donors 
                             SET TotalDonations = TotalDonations + :amount, DonationCount = DonationCount + 1 
                             WHERE DonorID = :donorId');
            
            $this->db->bind(':amount', $data['amount'] ?? 0);
            $this->db->bind(':donorId', $data['donorId']);
            
            $donorUpdateResult = $this->db->execute();
            
            // Check request completion status and update if needed
            if($data['donationType'] == 'Monetary') {
                $this->checkMonetaryRequestCompletion($data['requestId']);
            } else {
                $this->checkNonMonetaryRequestCompletion($data['requestId']);
            }
            
            // If all operations successful, commit transaction
            if($donationResult && $updateResult && $donorUpdateResult) {
                $this->db->commit();
                return $donationId;
            } else {
                $this->db->rollBack();
                return false;
            }
            
        } catch(PDOException $e) {
            $this->db->rollBack();
            error_log("Donation Creation Error: " . $e->getMessage());
            return false;
        }
    }

    private function checkMonetaryRequestCompletion($requestId) {
        // Get monetary donation details
        $this->db->query('SELECT md.TargetAmount, md.CurrentAmount 
                         FROM monetary_donation_details md 
                         WHERE md.RequestID = :requestId');
        
        $this->db->bind(':requestId', $requestId);
        $details = $this->db->single();
        
        // If target amount reached, update request status to Completed
        if($details && $details->CurrentAmount >= $details->TargetAmount) {
            $this->db->query('UPDATE donation_requests 
                             SET RequestStatus = "Completed" 
                             WHERE RequestID = :requestId');
            
            $this->db->bind(':requestId', $requestId);
            $this->db->execute();
        } else {
            // Otherwise ensure it's marked as InProgress
            $this->db->query('UPDATE donation_requests 
                             SET RequestStatus = "InProgress" 
                             WHERE RequestID = :requestId AND RequestStatus = "Pending"');
            
            $this->db->bind(':requestId', $requestId);
            $this->db->execute();
        }
    }

    private function checkNonMonetaryRequestCompletion($requestId) {
        // Get non-monetary donation details
        $this->db->query('SELECT nmd.QuantityNeeded, nmd.QuantityReceived 
                         FROM nonmonetary_donation_details nmd 
                         WHERE nmd.RequestID = :requestId');
        
        $this->db->bind(':requestId', $requestId);
        $details = $this->db->single();
        
        // If quantity needed reached, update request status to Completed
        if($details && $details->QuantityReceived >= $details->QuantityNeeded) {
            $this->db->query('UPDATE donation_requests 
                             SET RequestStatus = "Completed" 
                             WHERE RequestID = :requestId');
            
            $this->db->bind(':requestId', $requestId);
            $this->db->execute();
        } else {
            // Otherwise ensure it's marked as InProgress
            $this->db->query('UPDATE donation_requests 
                             SET RequestStatus = "InProgress" 
                             WHERE RequestID = :requestId AND RequestStatus = "Pending"');
            
            $this->db->bind(':requestId', $requestId);
            $this->db->execute();
        }
    }

    public function getDonationById($donationId) {
        $this->db->query('SELECT d.*, dr.Title, dr.Category, dr.RequestType 
                         FROM donations d 
                         JOIN donation_requests dr ON d.RequestID = dr.RequestID 
                         WHERE d.DonationID = :donationId');
        
        $this->db->bind(':donationId', $donationId);
        
        return $this->db->single();
    }

    public function getDonationsByRequestId($requestId) {
        $this->db->query('SELECT d.*, 
                         CASE WHEN d.IsAnonymous = 1 THEN "Anonymous Donor" 
                            ELSE CONCAT(dn.FirstName, " ", dn.LastName) END AS DonorName 
                         FROM donations d 
                         JOIN donors dn ON d.DonorID = dn.DonorID 
                         WHERE d.RequestID = :requestId 
                         ORDER BY d.DonationDate DESC');
        
        $this->db->bind(':requestId', $requestId);
        
        return $this->db->resultSet();
    }

    public function getDonationsByDonorId($donorId) {
        $this->db->query('SELECT d.*, dr.Title, dr.Category, r.FirstName as RecipientFirstName, r.LastName as RecipientLastName 
                         FROM donations d 
                         JOIN donation_requests dr ON d.RequestID = dr.RequestID 
                         JOIN recipients r ON dr.RecipientID = r.RecipientID 
                         WHERE d.DonorID = :donorId 
                         ORDER BY d.DonationDate DESC');
        
        $this->db->bind(':donorId', $donorId);
        
        return $this->db->resultSet();
    }

    /*public function createNonMonetaryDonation($data) {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Generate DonationID (Format: DON + 5 random digits)
            $donationId = 'DON' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            
            // Insert donation record
            $this->db->query('INSERT INTO donations (DonationID, RequestID, DonorID, DonationType, QuantityDonated, IsAnonymous, Status) 
                             VALUES (:donationId, :requestId, :donorId, :donationType, :quantity, :isAnonymous, "Pending")');
            
            $this->db->bind(':donationId', $donationId);
            $this->db->bind(':requestId', $data['requestId']);
            $this->db->bind(':donorId', $data['donorId']);
            $this->db->bind(':donationType', $data['donationType']);
            $this->db->bind(':quantity', $data['quantity']);
            $this->db->bind(':isAnonymous', $data['isAnonymous'] ? 1 : 0);
            
            $donationResult = $this->db->execute();
            
            // Create non_monetary_donation_scheduling record
            $schedulingId = 'NMDS' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            
            $this->db->query('INSERT INTO non_monetary_donation_scheduling (SchedulingID, DonationID, DropOffDate, DropOffTime, Notes) 
                             VALUES (:schedulingId, :donationId, :dropOffDate, :dropOffTime, :notes)');
            
            $this->db->bind(':schedulingId', $schedulingId);
            $this->db->bind(':donationId', $donationId);
            $this->db->bind(':dropOffDate', $data['dropOffDate']);
            $this->db->bind(':dropOffTime', $data['dropOffTime']);
            $this->db->bind(':notes', $data['notes'] ?? null);
            
            $schedulingResult = $this->db->execute();
            
            // Update nonmonetary_donation_details (increment QuantityReceived)
            $this->db->query('UPDATE nonmonetary_donation_details 
                             SET QuantityReceived = QuantityReceived + :quantity 
                             WHERE RequestID = :requestId');
            
            $this->db->bind(':quantity', $data['quantity']);
            $this->db->bind(':requestId', $data['requestId']);
            
            $updateResult = $this->db->execute();
            
            // Update donor statistics (increment DonationCount)
            $this->db->query('UPDATE donors 
                             SET DonationCount = DonationCount + 1 
                             WHERE DonorID = :donorId');
            
            $this->db->bind(':donorId', $data['donorId']);
            
            $donorUpdateResult = $this->db->execute();
            
            // Check request completion status and update if needed
            $this->checkNonMonetaryRequestCompletion($data['requestId']);
            
            // If all operations successful, commit transaction
            if($donationResult && $schedulingResult && $updateResult && $donorUpdateResult) {
                $this->db->commit();
                return $donationId;
            } else {
                $this->db->rollBack();
                return false;
            }
            
        } catch(PDOException $e) {
            $this->db->rollBack();
            error_log("Non-Monetary Donation Creation Error: " . $e->getMessage());
            return false;
        }
    }*/
    



    /**
 * Get all donations for a specific donor with pagination
 * @param string $donorId The donor ID
 * @param int $limit The maximum number of donations to return
 * @param int $offset The offset for pagination
 * @return array Array of donations
 */
public function getDonationsByDonor($donorId, $limit = null, $offset = 0) {
    $sql = 'SELECT d.*, dr.Title, dr.Category, dr.RequestType 
            FROM donations d 
            JOIN donation_requests dr ON d.RequestID = dr.RequestID 
            WHERE d.DonorID = :donorId 
            ORDER BY d.DonationDate DESC';
    
    // Add pagination if limit is provided
    if ($limit !== null) {
        $sql .= ' LIMIT :limit OFFSET :offset';
    }
    
    $this->db->query($sql);
    $this->db->bind(':donorId', $donorId);
    
    if ($limit !== null) {
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
    }
    
    return $this->db->resultSet();
}

/**
 * Count the total number of donations for a donor
 * @param string $donorId The donor ID
 * @return int The total number of donations
 */
public function countDonationsByDonor($donorId) {
    $this->db->query('SELECT COUNT(*) as total FROM donations WHERE DonorID = :donorId');
    $this->db->bind(':donorId', $donorId);
    
    $result = $this->db->single();
    return $result->total;
}

/**
 * Get non-monetary item details for a request
 * @param string $requestId The request ID
 * @return object|bool The item details or false if not found
 */
public function getNonMonetaryItemDetails($requestId) {
    $this->db->query('SELECT * FROM nonmonetary_donation_details WHERE RequestID = :requestId');
    $this->db->bind(':requestId', $requestId);
    
    return $this->db->single();
}

/**
 * Cancel a donation
 * @param string $donationId The donation ID
 * @param string $reason The cancellation reason
 * @return bool True if successful, false otherwise
 */

 /*public function cancelDonation($donationId, $reason) {
    $this->db->beginTransaction();
    
    try {
        // Check if the CancellationReason column exists
        $this->db->query("SHOW COLUMNS FROM donations LIKE 'CancellationReason'");
        $reasonColumnExists = !empty($this->db->resultSet());
        
        // Check if the CancellationDate column exists
        $this->db->query("SHOW COLUMNS FROM donations LIKE 'CancellationDate'");
        $dateColumnExists = !empty($this->db->resultSet());
        
        // Prepare the SQL query based on existing columns
        if ($reasonColumnExists && $dateColumnExists) {
            // Both columns exist, use the original query
            $this->db->query('UPDATE donations 
                             SET Status = "Cancelled", CancellationReason = :reason, CancellationDate = NOW() 
                             WHERE DonationID = :donationId AND Status = "Pending"');
            
            $this->db->bind(':reason', $reason);
        } else {
            // Just update the status
            $this->db->query('UPDATE donations 
                             SET Status = "Cancelled" 
                             WHERE DonationID = :donationId AND Status = "Pending"');
        }
        
        $this->db->bind(':donationId', $donationId);
        $result = $this->db->execute();
        
        // Store the reason in a separate log table if those columns don't exist
        if ($result && (!$reasonColumnExists || !$dateColumnExists)) {
            // Create a cancellation log table if it doesn't exist
            $this->db->query("CREATE TABLE IF NOT EXISTS `donation_cancellations` (
                `ID` int(11) NOT NULL AUTO_INCREMENT,
                `DonationID` varchar(10) NOT NULL,
                `CancellationReason` text NOT NULL,
                `CancellationDate` datetime DEFAULT current_timestamp(),
                PRIMARY KEY (`ID`),
                KEY `DonationID` (`DonationID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
            $this->db->execute();
            
            // Insert into the log table
            $this->db->query('INSERT INTO donation_cancellations (DonationID, CancellationReason) 
                             VALUES (:donationId, :reason)');
            $this->db->bind(':donationId', $donationId);
            $this->db->bind(':reason', $reason);
            $this->db->execute();
        }
        
        if ($result) {
            $this->db->commit();
            return true;
        } else {
            $this->db->rollBack();
            return false;
        }
        
    } catch(PDOException $e) {
        $this->db->rollBack();
        error_log("Donation Cancellation Error: " . $e->getMessage());
        return false;
    }
}*/

public function cancelDonation($donationId, $reason) {
    $this->db->beginTransaction();
    
    try {
        // Get donation details before updating status
        $this->db->query('SELECT * FROM donations WHERE DonationID = :donationId');
        $this->db->bind(':donationId', $donationId);
        $donation = $this->db->single();
        
        if (!$donation) {
            $this->db->rollBack();
            return false;
        }
        
        // Update donation status
        $this->db->query('UPDATE donations 
                         SET Status = "Cancelled" 
                         WHERE DonationID = :donationId AND Status = "Pending"');
        
        $this->db->bind(':donationId', $donationId);
        $result = $this->db->execute();
        
        // For non-monetary donations, update the quantity received in the request details
        if ($donation && $donation->DonationType == 'NonMonetary') {
            // Update the quantity received in nonmonetary_donation_details
            $this->db->query('UPDATE nonmonetary_donation_details 
                             SET QuantityReceived = QuantityReceived - :quantity 
                             WHERE RequestID = :requestId');
            
            $this->db->bind(':quantity', $donation->QuantityDonated);
            $this->db->bind(':requestId', $donation->RequestID);
            
            $this->db->execute();
        }
        
        // Store cancellation information (using a separate table since the main table might not have these columns)
        $this->db->query("CREATE TABLE IF NOT EXISTS `donation_cancellations` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `DonationID` varchar(10) NOT NULL,
            `CancellationReason` text NOT NULL,
            `CancellationDate` datetime DEFAULT current_timestamp(),
            PRIMARY KEY (`ID`),
            KEY `DonationID` (`DonationID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        $this->db->execute();
        
        // Insert into the cancellation log
        $this->db->query('INSERT INTO donation_cancellations (DonationID, CancellationReason) 
                         VALUES (:donationId, :reason)');
        $this->db->bind(':donationId', $donationId);
        $this->db->bind(':reason', $reason);
        $this->db->execute();
        
        // Commit transaction if successful
        if ($result) {
            $this->db->commit();
            return true;
        } else {
            $this->db->rollBack();
            return false;
        }
        
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("Donation Cancellation Error: " . $e->getMessage());
        return false;
    }
}

/*public function cancelDonation($donationId, $reason) {
    $this->db->beginTransaction();
    
    try {
        // Update donation status
        $this->db->query('UPDATE donations 
                         SET Status = "Cancelled", CancellationReason = :reason, CancellationDate = NOW() 
                         WHERE DonationID = :donationId AND Status = "Pending"');
        
        $this->db->bind(':donationId', $donationId);
        $this->db->bind(':reason', $reason);
        
        $result = $this->db->execute();
        
        // Get donation details to update request accordingly
        $this->db->query('SELECT * FROM donations WHERE DonationID = :donationId');
        $this->db->bind(':donationId', $donationId);
        $donation = $this->db->single();
        
        if ($donation && $donation->DonationType == 'NonMonetary') {
            // Update the quantity received in nonmonetary_donation_details
            $this->db->query('UPDATE nonmonetary_donation_details 
                             SET QuantityReceived = QuantityReceived - :quantity 
                             WHERE RequestID = :requestId');
            
            $this->db->bind(':quantity', $donation->QuantityDonated);
            $this->db->bind(':requestId', $donation->RequestID);
            
            $this->db->execute();
        }
        
        // Commit transaction if successful
        if ($result) {
            $this->db->commit();
            return true;
        } else {
            $this->db->rollBack();
            return false;
        }
        
    } catch(PDOException $e) {
        $this->db->rollBack();
        error_log("Donation Cancellation Error: " . $e->getMessage());
        return false;
    }
}*/

public function createNonMonetaryDonation($data) {
    // Begin transaction
    $this->db->beginTransaction();
    
    try {
        // Generate DonationID (Format: DON + 5 random digits)
        $donationId = 'DON' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        // Insert donation record
        $this->db->query('INSERT INTO donations (DonationID, RequestID, DonorID, DonationType, QuantityDonated, IsAnonymous, Status) 
                         VALUES (:donationId, :requestId, :donorId, :donationType, :quantity, :isAnonymous, "Pending")');
        
        $this->db->bind(':donationId', $donationId);
        $this->db->bind(':requestId', $data['requestId']);
        $this->db->bind(':donorId', $data['donorId']);
        $this->db->bind(':donationType', $data['donationType']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':isAnonymous', $data['isAnonymous'] ? 1 : 0);
        
        $donationResult = $this->db->execute();
        
        // Create non_monetary_donation_scheduling record
        $schedulingId = 'NMDS' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        $this->db->query('INSERT INTO non_monetary_donation_scheduling (SchedulingID, DonationID, DropOffDate, DropOffTime, Notes) 
                         VALUES (:schedulingId, :donationId, :dropOffDate, :dropOffTime, :notes)');
        
        $this->db->bind(':schedulingId', $schedulingId);
        $this->db->bind(':donationId', $donationId);
        $this->db->bind(':dropOffDate', $data['dropOffDate']);
        $this->db->bind(':dropOffTime', $data['dropOffTime']);
        $this->db->bind(':notes', $data['notes'] ?? null);
        
        $schedulingResult = $this->db->execute();
        
        // IMPORTANT CHANGE: Update the nonmonetary_donation_details to include the new quantity
        $this->db->query('UPDATE nonmonetary_donation_details 
                         SET QuantityReceived = QuantityReceived + :quantity 
                         WHERE RequestID = :requestId');
        
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':requestId', $data['requestId']);
        
        $updateQuantityResult = $this->db->execute();
        
        // Update donor statistics (increment DonationCount)
        $this->db->query('UPDATE donors 
                         SET DonationCount = DonationCount + 1 
                         WHERE DonorID = :donorId');
        
        $this->db->bind(':donorId', $data['donorId']);
        
        $donorUpdateResult = $this->db->execute();
        
        // If donation is for a significant amount, also update the request status
        if ($data['quantity'] > 0) {
            $this->db->query('UPDATE donation_requests 
                           SET RequestStatus = "InProgress" 
                           WHERE RequestID = :requestId AND RequestStatus = "Pending"');
            $this->db->bind(':requestId', $data['requestId']);
            $this->db->execute();
            
            // Check if the request should be marked as completed
            $this->checkNonMonetaryRequestCompletion($data['requestId']);
        }
        
        // If all operations successful, commit transaction
        if ($donationResult && $schedulingResult && $updateQuantityResult && $donorUpdateResult) {
            $this->db->commit();
            return $donationId;
        } else {
            $this->db->rollBack();
            return false;
        }
        
    } catch(PDOException $e) {
        $this->db->rollBack();
        error_log("Non-Monetary Donation Creation Error: " . $e->getMessage());
        return false;
    }
}

public function getNonMonetaryDonationDetails($donationId) {
    $this->db->query('SELECT * FROM non_monetary_donation_scheduling WHERE DonationID = :donationId');
    
    $this->db->bind(':donationId', $donationId);
    
    return $this->db->single();
}




/**
 * Get pending non-monetary donations for a donor with pagination
 * 
 * @param string $donorId The donor ID
 * @param int $limit Number of items per page
 * @param int $offset Pagination offset
 * @return array Array of pending non-monetary donations
 */
public function getPendingNonMonetaryDonations($donorId, $limit = null, $offset = 0) {
    $sql = 'SELECT d.*, dr.Title, dr.Category 
            FROM donations d 
            JOIN donation_requests dr ON d.RequestID = dr.RequestID 
            WHERE d.DonorID = :donorId 
            AND d.DonationType = "NonMonetary" 
            AND d.Status = "Pending" 
            ORDER BY d.DonationDate DESC';
    
    // Add pagination if limit is provided
    if ($limit !== null) {
        $sql .= ' LIMIT :limit OFFSET :offset';
    }
    
    $this->db->query($sql);
    $this->db->bind(':donorId', $donorId);
    
    if ($limit !== null) {
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
    }
    
    return $this->db->resultSet();
}

/**
 * Count pending non-monetary donations for a donor
 * 
 * @param string $donorId The donor ID
 * @return int Number of pending non-monetary donations
 */
public function countPendingNonMonetaryDonations($donorId) {
    $this->db->query('SELECT COUNT(*) as total 
                     FROM donations 
                     WHERE DonorID = :donorId 
                     AND DonationType = "NonMonetary" 
                     AND Status = "Pending"');
                     
    $this->db->bind(':donorId', $donorId);
    
    $result = $this->db->single();
    return $result->total;
}

/**
 * Update pending donation status to completed when items are received
 * 
 * @param string $donationId The donation ID
 * @param string $notes Optional notes about the delivery
 * @return bool True if successful, false otherwise
 */
public function markDonationAsCompleted($donationId, $notes = null) {
    $this->db->beginTransaction();
    
    try {
        // Update donation status
        $this->db->query('UPDATE donations 
                         SET Status = "Completed" 
                         WHERE DonationID = :donationId AND Status = "Pending"');
                         
        $this->db->bind(':donationId', $donationId);
        $result = $this->db->execute();
        
        // Get the donation to determine request and quantity
        $this->db->query('SELECT * FROM donations WHERE DonationID = :donationId');
        $this->db->bind(':donationId', $donationId);
        $donation = $this->db->single();
        
        if ($donation) {
            // Check if the request should be marked as completed
            $this->checkNonMonetaryRequestCompletion($donation->RequestID);
            
            // Add completion note if provided
            if ($notes) {
                // Check if notes table exists, create if not
                $this->db->query("SHOW TABLES LIKE 'donation_completion_notes'");
                $tableExists = $this->db->resultSet();
                
                if (empty($tableExists)) {
                    $this->db->query("CREATE TABLE IF NOT EXISTS `donation_completion_notes` (
                        `ID` int(11) NOT NULL AUTO_INCREMENT,
                        `DonationID` varchar(10) NOT NULL,
                        `Notes` text NOT NULL,
                        `CompletionDate` datetime DEFAULT current_timestamp(),
                        PRIMARY KEY (`ID`),
                        KEY `DonationID` (`DonationID`),
                        CONSTRAINT `donation_completion_notes_ibfk_1` FOREIGN KEY (`DonationID`) REFERENCES `donations` (`DonationID`) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
                    
                    $this->db->execute();
                }
                
                // Insert completion note
                $this->db->query('INSERT INTO donation_completion_notes (DonationID, Notes) 
                                 VALUES (:donationId, :notes)');
                                 
                $this->db->bind(':donationId', $donationId);
                $this->db->bind(':notes', $notes);
                $this->db->execute();
            }
        }
        
        // Commit transaction if successful
        if ($result) {
            $this->db->commit();
            return true;
        } else {
            $this->db->rollBack();
            return false;
        }
        
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("Mark Donation Completed Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get upcoming donations that are due soon
 * 
 * @param string $donorId The donor ID
 * @param int $daysThreshold Number of days to consider as "upcoming" (default: 3)
 * @return array Array of upcoming donations
 */
public function getUpcomingDonations($donorId, $daysThreshold = 3) {
    // First check if the scheduling table exists
    $this->db->query("SHOW TABLES LIKE 'non_monetary_donation_scheduling'");
    $tableExists = $this->db->resultSet();
    
    if (empty($tableExists)) {
        return [];
    }
    
    // Calculate the date threshold
    $thresholdDate = date('Y-m-d', strtotime('+' . $daysThreshold . ' days'));
    
    $this->db->query('SELECT d.*, dr.Title, dr.Category, nms.DropOffDate, nms.DropOffTime
                     FROM donations d
                     JOIN donation_requests dr ON d.RequestID = dr.RequestID
                     JOIN non_monetary_donation_scheduling nms ON d.DonationID = nms.DonationID
                     WHERE d.DonorID = :donorId
                     AND d.Status = "Pending"
                     AND d.DonationType = "NonMonetary"
                     AND nms.DropOffDate <= :thresholdDate
                     AND nms.DropOffDate >= CURDATE()
                     ORDER BY nms.DropOffDate, nms.DropOffTime');
                     
    $this->db->bind(':donorId', $donorId);
    $this->db->bind(':thresholdDate', $thresholdDate);
    
    $upcomingDonations = $this->db->resultSet();
    
    // Enhance each donation with additional details
    foreach ($upcomingDonations as &$donation) {
        // Get item details
        $itemDetails = $this->getNonMonetaryItemDetails($donation->RequestID);
        if ($itemDetails) {
            $donation->ItemName = $itemDetails->ItemName;
            $donation->DropOffLocation = $itemDetails->DropOffLocation;
            $donation->Province = $itemDetails->Province;
        }
        
        // Calculate days remaining
        $today = new DateTime();
        $dropOffDate = new DateTime($donation->DropOffDate);
        $daysRemaining = $today->diff($dropOffDate)->days;
        $donation->DaysRemaining = $daysRemaining;
    }
    
    return $upcomingDonations;
}

/**
 * Get overdue donations (past the scheduled drop-off date)
 * 
 * @param string $donorId The donor ID
 * @return array Array of overdue donations
 */
public function getOverdueDonations($donorId) {
    // First check if the scheduling table exists
    $this->db->query("SHOW TABLES LIKE 'non_monetary_donation_scheduling'");
    $tableExists = $this->db->resultSet();
    
    if (empty($tableExists)) {
        return [];
    }
    
    $this->db->query('SELECT d.*, dr.Title, dr.Category, nms.DropOffDate, nms.DropOffTime
                     FROM donations d
                     JOIN donation_requests dr ON d.RequestID = dr.RequestID
                     JOIN non_monetary_donation_scheduling nms ON d.DonationID = nms.DonationID
                     WHERE d.DonorID = :donorId
                     AND d.Status = "Pending"
                     AND d.DonationType = "NonMonetary"
                     AND nms.DropOffDate < CURDATE()
                     ORDER BY nms.DropOffDate');
                     
    $this->db->bind(':donorId', $donorId);
    
    $overdueDonations = $this->db->resultSet();
    
    // Enhance each donation with additional details
    foreach ($overdueDonations as &$donation) {
        // Get item details
        $itemDetails = $this->getNonMonetaryItemDetails($donation->RequestID);
        if ($itemDetails) {
            $donation->ItemName = $itemDetails->ItemName;
            $donation->DropOffLocation = $itemDetails->DropOffLocation;
            $donation->Province = $itemDetails->Province;
        }
        
        // Calculate days overdue
        $today = new DateTime();
        $dropOffDate = new DateTime($donation->DropOffDate);
        $daysOverdue = $today->diff($dropOffDate)->days;
        $donation->DaysOverdue = $daysOverdue;
    }
    
    return $overdueDonations;
}

}