<?php
class BankPayment {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Create a new bank payment record
    public function createBankPayment($data) {
        try {
            $this->db->query('INSERT INTO bank_payments (OrderID, SlipFile) 
                             VALUES (:orderId, :slipFile)');
            
            $this->db->bind(':orderId', $data['order_id']);
            $this->db->bind(':slipFile', $data['slip_file']);
            
            $this->db->execute();
            
            return $this->db->getLastInsertId();
        } catch (Exception $e) {
            error_log("Create bank payment error: " . $e->getMessage());
            return false;
        }
    }

    // Get bank payment by order ID
    public function getPaymentByOrderId($orderId) {
        try {
            $this->db->query('SELECT * FROM bank_payments WHERE OrderID = :orderId ORDER BY UploadDate DESC LIMIT 1');
            $this->db->bind(':orderId', $orderId);
            
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Get bank payment error: " . $e->getMessage());
            return false;
        }
    }

    // Update bank payment status (for seller verification)
    /*public function updatePaymentStatus($id, $status, $verifiedBy, $notes = null) {
        try {
            $this->db->query('UPDATE bank_payments SET 
                             Status = :status, 
                             VerifiedBy = :verifiedBy,
                             VerificationDate = CURRENT_TIMESTAMP,
                             Notes = :notes
                             WHERE ID = :id');
            
            $this->db->bind(':status', $status);
            $this->db->bind(':verifiedBy', $verifiedBy);
            $this->db->bind(':notes', $notes);
            $this->db->bind(':id', $id);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Update bank payment status error: " . $e->getMessage());
            return false;
        }
    }*/

    public function updatePaymentStatus($id, $status, $verifiedBy, $notes = null) {
        // Begin transaction to ensure all updates are atomic
        $this->db->beginTransaction();
        
        try {
            // 1. Update the bank payment status
            $this->db->query('UPDATE bank_payments SET 
                             Status = :status, 
                             VerifiedBy = :verifiedBy, 
                             VerificationDate = CURRENT_TIMESTAMP, 
                             Notes = :notes 
                             WHERE ID = :id');
            
            $this->db->bind(':status', $status);
            $this->db->bind(':verifiedBy', $verifiedBy);
            $this->db->bind(':notes', $notes);
            $this->db->bind(':id', $id);
            
            $updateResult = $this->db->execute();
            
            // If the payment is being verified, update related tables
            if ($status == 'Verified' && $updateResult) {
                // 2. Get the bank payment details to find the order ID
                $this->db->query('SELECT OrderID FROM bank_payments WHERE ID = :id');
                $this->db->bind(':id', $id);
                $bankPayment = $this->db->single();
                
                if ($bankPayment && !empty($bankPayment->OrderID)) {
                    $donationId = $bankPayment->OrderID;
                    
                    // 3. Get the donation details
                    $this->db->query('SELECT * FROM donations WHERE DonationID = :donationId');
                    $this->db->bind(':donationId', $donationId);
                    $donation = $this->db->single();
                    
                    if ($donation) {
                        // 4. Update the donation status to Completed
                        $this->db->query('UPDATE donations SET Status = "Completed" WHERE DonationID = :donationId');
                        $this->db->bind(':donationId', $donationId);
                        $this->db->execute();
                        
                        // 5. If it's a monetary donation, update monetary_donation_details
                        if ($donation->DonationType == 'Monetary' && $donation->Amount > 0) {
                            // Update the current amount in monetary_donation_details
                            $this->db->query('UPDATE monetary_donation_details 
                                             SET CurrentAmount = CurrentAmount + :amount 
                                             WHERE RequestID = :requestId');
                            $this->db->bind(':amount', $donation->Amount);
                            $this->db->bind(':requestId', $donation->RequestID);
                            $this->db->execute();
                            
                            // 6. Update donor statistics
                            $this->db->query('UPDATE donors 
                                             SET TotalDonations = TotalDonations + :amount, 
                                                 DonationCount = DonationCount + 1 
                                             WHERE DonorID = :donorId');
                            $this->db->bind(':amount', $donation->Amount);
                            $this->db->bind(':donorId', $donation->DonorID);
                            $this->db->execute();
                            
                            // 7. Check if the request is complete and update status if needed
                            $this->db->query('SELECT md.TargetAmount, md.CurrentAmount 
                                             FROM monetary_donation_details md 
                                             WHERE md.RequestID = :requestId');
                            $this->db->bind(':requestId', $donation->RequestID);
                            $monetaryDetails = $this->db->single();
                            
                            if ($monetaryDetails && $monetaryDetails->CurrentAmount >= $monetaryDetails->TargetAmount) {
                                $this->db->query('UPDATE donation_requests 
                                                 SET RequestStatus = "Completed" 
                                                 WHERE RequestID = :requestId');
                                $this->db->bind(':requestId', $donation->RequestID);
                                $this->db->execute();
                            } else {
                                // Ensure request is marked as InProgress if not complete
                                $this->db->query('UPDATE donation_requests 
                                                 SET RequestStatus = "InProgress" 
                                                 WHERE RequestID = :requestId AND RequestStatus = "Pending"');
                                $this->db->bind(':requestId', $donation->RequestID);
                                $this->db->execute();
                            }
                        }
                    }
                }
            }
            
            // Commit all changes if we got here without exceptions
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            // Roll back all changes if any error occurred
            $this->db->rollBack();
            error_log("Update bank payment status error: " . $e->getMessage());
            return false;
        }
    }

    // Get all pending bank payments
    public function getPendingPayments() {
        try {
            $this->db->query('SELECT bp.*, o.OrderID, o.TotalAmount, o.UserID, 
                             o.Status AS OrderStatus, u.Username,
                             sd.ShippingAddress, sd.ContactPhone
                             FROM bank_payments bp
                             JOIN orders o ON bp.OrderID = o.OrderID
                             JOIN users u ON o.UserID = u.UserID
                             LEFT JOIN shipping_details sd ON o.OrderID = sd.OrderID
                             WHERE bp.Status = "Pending"
                             ORDER BY bp.UploadDate DESC');
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Get pending payments error: " . $e->getMessage());
            return [];
        }
    }

    // Get payment by ID
    public function getPaymentById($id) {
        try {
            $this->db->query('SELECT bp.*, o.OrderID, o.TotalAmount, o.UserID, 
                             o.Status AS OrderStatus, u.Username,
                             sd.ShippingAddress, sd.ContactPhone
                             FROM bank_payments bp
                             JOIN orders o ON bp.OrderID = o.OrderID
                             JOIN users u ON o.UserID = u.UserID
                             LEFT JOIN shipping_details sd ON o.OrderID = sd.OrderID
                             WHERE bp.ID = :id');
            
            $this->db->bind(':id', $id);
            
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Get payment by ID error: " . $e->getMessage());
            return false;
        }
    }

    // Get payment history (verified/rejected payments)
    public function getPaymentHistory() {
        try {
            $this->db->query('SELECT bp.*, o.OrderID, o.TotalAmount, o.UserID, 
                             o.Status AS OrderStatus, u.Username, sv.Username AS VerifierName,
                             sd.ShippingAddress, sd.ContactPhone
                             FROM bank_payments bp
                             JOIN orders o ON bp.OrderID = o.OrderID
                             JOIN users u ON o.UserID = u.UserID
                             LEFT JOIN users sv ON bp.VerifiedBy = sv.UserID
                             LEFT JOIN shipping_details sd ON o.OrderID = sd.OrderID
                             WHERE bp.Status != "Pending"
                             ORDER BY bp.VerificationDate DESC
                             LIMIT 20');
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Get payment history error: " . $e->getMessage());
            return [];
        }
    }
}