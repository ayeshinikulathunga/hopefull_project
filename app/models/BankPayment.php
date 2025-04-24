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

    // Update bank payment status (for admin verification)
    public function updatePaymentStatus($id, $status, $verifiedBy, $notes = null) {
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
    }
}