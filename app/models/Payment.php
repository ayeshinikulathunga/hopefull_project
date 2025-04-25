<?php
class Payment {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Record a payment attempt
    /*public function createPaymentRecord($data) {
        try {
            $this->db->query('INSERT INTO payments (OrderID, PaymentAmount, PaymentMethod, Status, PaymentReference, PaymentDetails) 
                             VALUES (:orderId, :paymentAmount, :paymentMethod, :status, :paymentReference, :paymentDetails)');
            
            $this->db->bind(':orderId', $data['order_id']);
            $this->db->bind(':paymentAmount', $data['payment_amount']);
            $this->db->bind(':paymentMethod', $data['payment_method']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':paymentReference', $data['payment_reference'] ?? null);
            $this->db->bind(':paymentDetails', $data['payment_details'] ?? null);
            
            if ($this->db->execute()) {
                return $this->db->getLastInsertId();
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Create payment record error: " . $e->getMessage());
            return false;
        }
    }*/

    // Record a payment attempt
public function createPaymentRecord($data) {
    try {
        $this->db->query('INSERT INTO payments (OrderID, PaymentAmount, PaymentMethod, Status, PaymentReference, PaymentDetails) 
                         VALUES (:orderId, :paymentAmount, :paymentMethod, :status, :paymentReference, :paymentDetails)');
        
        $this->db->bind(':orderId', $data['order_id']);
        $this->db->bind(':paymentAmount', $data['payment_amount']);
        $this->db->bind(':paymentMethod', $data['payment_method']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':paymentReference', $data['payment_reference'] ?? null);
        $this->db->bind(':paymentDetails', $data['payment_details'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->getLastInsertId(); // Fixed to use the public method
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log("Create payment record error: " . $e->getMessage());
        return false;
    }
}

    // Update payment status
    public function updatePaymentStatus($paymentId, $status, $reference = null, $details = null) {
        try {
            // Add logging to debug
            error_log("Attempting to update payment ID: {$paymentId} to status: {$status}");
            
            $this->db->query('UPDATE payments 
                           SET Status = :status, 
                               PaymentReference = :reference,
                               PaymentDetails = :details,
                               ProcessedDate = CURRENT_TIMESTAMP
                           WHERE ID = :paymentId');
            
            $this->db->bind(':status', $status);
            $this->db->bind(':reference', $reference);
            $this->db->bind(':details', $details);
            $this->db->bind(':paymentId', $paymentId);
            
            $result = $this->db->execute();
            
            error_log("Payment status update result: " . ($result ? "Success" : "Failed"));
            
            if (!$result) {
                error_log("SQL error: " . json_encode($this->db->getError()));
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Update payment status error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get payment by order ID
    public function getPaymentByOrderId($orderId) {
        try {
            $this->db->query('SELECT * FROM payments WHERE OrderID = :orderId ORDER BY CreatedDate DESC LIMIT 1');
            $this->db->bind(':orderId', $orderId);
            
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Get payment by order ID error: " . $e->getMessage());
            return false;
        }
    }
    
    // Verify PayHere payment signature
    public function verifyPayHereSignature($data, $signature) {
        // Sort the data array alphabetically by key
        ksort($data);
        
        // Create the hash input string
        $hash_input = '';
        foreach ($data as $key => $value) {
            if ($key != 'signature') {
                $hash_input .= $value;
            }
        }
        
        // Add the merchant secret
        $hash_input = PAYHERE_MERCHANT_SECRET . $hash_input;
        
        // Calculate the hash
        $calculated_signature = md5($hash_input);
        
        // Compare with the received signature
        return $calculated_signature === $signature;
    }
}