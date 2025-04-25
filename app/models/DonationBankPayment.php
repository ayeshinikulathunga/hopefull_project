<?php
class DonationBankPayment {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Create a bank payment record for a donation
     * @param array $data Bank payment data
     * @return bool True if successful
     */
    public function createBankPayment($data) {
        try {
            // Create donation_bank_payments table if it doesn't exist
            $this->db->query("CREATE TABLE IF NOT EXISTS `donation_bank_payments` (
                `ID` int(11) NOT NULL AUTO_INCREMENT,
                `DonationID` varchar(10) NOT NULL,
                `SlipFile` varchar(255) NOT NULL,
                `UploadDate` datetime DEFAULT current_timestamp(),
                `Status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
                `VerifiedBy` varchar(10) DEFAULT NULL,
                `VerificationDate` datetime DEFAULT NULL,
                `Notes` text DEFAULT NULL,
                PRIMARY KEY (`ID`),
                KEY `DonationID` (`DonationID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
            
            $this->db->execute();
            
            // Insert bank payment record
            $this->db->query('INSERT INTO donation_bank_payments (DonationID, SlipFile) 
                             VALUES (:donationId, :slipFile)');
            
            $this->db->bind(':donationId', $data['donation_id']);
            $this->db->bind(':slipFile', $data['slip_file']);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Create donation bank payment error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get bank payment by donation ID
     * @param string $donationId The donation ID
     * @return object|bool Bank payment record or false
     */
    public function getPaymentByDonationId($donationId) {
        try {
            $this->db->query('SELECT * FROM donation_bank_payments WHERE DonationID = :donationId');
            $this->db->bind(':donationId', $donationId);
            
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Get bank payment by donation ID error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update bank payment status
     * @param int $paymentId Payment ID
     * @param string $status New status
     * @param string|null $verifiedBy User ID who verified
     * @param string|null $notes Additional notes
     * @return bool True if successful
     */
    public function updatePaymentStatus($paymentId, $status, $verifiedBy = null, $notes = null) {
        try {
            $this->db->query('UPDATE donation_bank_payments 
                             SET Status = :status, 
                                 VerifiedBy = :verifiedBy,
                                 VerificationDate = CURRENT_TIMESTAMP,
                                 Notes = :notes
                             WHERE ID = :paymentId');
            
            $this->db->bind(':status', $status);
            $this->db->bind(':verifiedBy', $verifiedBy);
            $this->db->bind(':notes', $notes);
            $this->db->bind(':paymentId', $paymentId);
            
            if ($this->db->execute()) {
                // If payment is verified, update the donation status
                if ($status == 'Verified') {
                    // Get the donation ID from the payment record
                    $this->db->query('SELECT DonationID FROM donation_bank_payments WHERE ID = :paymentId');
                    $this->db->bind(':paymentId', $paymentId);
                    $payment = $this->db->single();
                    
                    if ($payment) {
                        // Update donation status via donationModel
                        $donationModel = new Donation();
                        
                        // Get the donation to get its details
                        $donation = $donationModel->getDonationById($payment->DonationID);
                        
                        if ($donation) {
                            // Mark the donation as completed
                            $this->db->query('UPDATE donations 
                                             SET Status = "Completed" 
                                             WHERE DonationID = :donationId');
                            $this->db->bind(':donationId', $payment->DonationID);
                            $this->db->execute();
                            
                            if ($donation->DonationType == 'Monetary') {
                                // Update monetary donation details
                                $this->db->query('UPDATE monetary_donation_details 
                                                 SET CurrentAmount = CurrentAmount + :amount 
                                                 WHERE RequestID = :requestId');
                                $this->db->bind(':amount', $donation->Amount);
                                $this->db->bind(':requestId', $donation->RequestID);
                                $this->db->execute();
                                
                                // Update donor statistics
                                $this->db->query('UPDATE donors 
                                                 SET TotalDonations = TotalDonations + :amount, 
                                                     DonationCount = DonationCount + 1 
                                                 WHERE DonorID = :donorId');
                                $this->db->bind(':amount', $donation->Amount);
                                $this->db->bind(':donorId', $donation->DonorID);
                                $this->db->execute();
                                
                                // Check if request is complete
                                $donationModel->checkMonetaryRequestCompletion($donation->RequestID);
                            }
                        }
                    }
                }
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Update donation bank payment status error: " . $e->getMessage());
            return false;
        }
    }
}