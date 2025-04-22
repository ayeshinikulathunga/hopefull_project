<?php
class Recipient {
    private $db;
    private $recipientModel;
    
    public function __construct() {
        
        $this->db = new Database();
    }
    
    // Get recipient by ID
    public function getRecipientById($id) {
        $this->db->query('SELECT * FROM recipients WHERE RecipientID = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    // Get all donation requests by recipient
    public function getRequestsByRecipient($recipientId) {
        $this->db->query('SELECT * FROM donation_requests 
                         WHERE RecipientID = :recipientId 
                         ORDER BY CreatedDate DESC');
        $this->db->bind(':recipientId', $recipientId);
        
        $requests = $this->db->resultSet();
        
        // Get additional details for each request
        foreach ($requests as $request) {
            if ($request->RequestType == 'Monetary') {
                $this->db->query('SELECT * FROM monetary_donation_details WHERE RequestID = :requestId');
                $this->db->bind(':requestId', $request->RequestID);
                $details = $this->db->single();
                
                if ($details) {
                    $request->TargetAmount = $details->TargetAmount;
                    $request->CurrentAmount = $details->CurrentAmount;
                    $request->Progress = ($details->CurrentAmount / $details->TargetAmount) * 100;
                } else {
                    $request->Progress = 0;
                }
            } else {
                $this->db->query('SELECT * FROM nonmonetary_donation_details WHERE RequestID = :requestId');
                $this->db->bind(':requestId', $request->RequestID);
                $details = $this->db->single();
                
                if ($details) {
                    $request->ItemName = $details->ItemName;
                    $request->QuantityNeeded = $details->QuantityNeeded;
                    $request->QuantityReceived = $details->QuantityReceived;
                    $request->Progress = ($details->QuantityReceived / $details->QuantityNeeded) * 100;
                } else {
                    $request->Progress = 0;
                }
            }
            
            // Format progress for display
            $request->Progress = min(100, max(0, round($request->Progress)));
            
            // Check if image exists
            $imagePath = APPROOT . '/../public/uploads/requests/' . $request->RequestID . '.jpg';
            $request->HasImage = file_exists($imagePath);
        }
        
        return $requests;
    }
    
    // Get request details including monetary or non-monetary specific details
    public function getRequestDetails($requestId) {
        $this->db->query('SELECT * FROM donation_requests WHERE RequestID = :requestId');
        $this->db->bind(':requestId', $requestId);
        
        $request = $this->db->single();
        
        if($request) {
            if($request->RequestType == 'Monetary') {
                $this->db->query('SELECT * FROM monetary_donation_details WHERE RequestID = :requestId');
                $this->db->bind(':requestId', $requestId);
                $monetaryDetails = $this->db->single();
                if ($monetaryDetails) {
                    $request->details = $monetaryDetails;
                    $request->TargetAmount = $monetaryDetails->TargetAmount;
                    $request->CurrentAmount = $monetaryDetails->CurrentAmount;
                    $request->Progress = ($monetaryDetails->CurrentAmount / $monetaryDetails->TargetAmount) * 100;
                } else {
                    $request->Progress = 0;
                }
            } else {
                $this->db->query('SELECT * FROM nonmonetary_donation_details WHERE RequestID = :requestId');
                $this->db->bind(':requestId', $requestId);
                $nonMonetaryDetails = $this->db->single();
                if ($nonMonetaryDetails) {
                    $request->details = $nonMonetaryDetails;
                    $request->ItemName = $nonMonetaryDetails->ItemName;
                    $request->QuantityNeeded = $nonMonetaryDetails->QuantityNeeded;
                    $request->QuantityReceived = $nonMonetaryDetails->QuantityReceived;
                    $request->Province = $nonMonetaryDetails->Province;
                    $request->DropOffLocation = $nonMonetaryDetails->DropOffLocation;
                    $request->DropOffTime = $nonMonetaryDetails->DropOffTime;
                    $request->Progress = ($nonMonetaryDetails->QuantityReceived / $nonMonetaryDetails->QuantityNeeded) * 100;
                } else {
                    $request->Progress = 0;
                }
            }
            
            // Format progress for display
            $request->Progress = min(100, max(0, round($request->Progress)));
            
            // Check if image exists
            $imagePath = APPROOT . '/../public/uploads/requests/' . $request->RequestID . '.jpg';
            $request->HasImage = file_exists($imagePath);
            
            // Check if proof document exists
            $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->RequestID . '.pdf';
            $request->HasProofDocument = file_exists($pdfPath);
        }
        
        return $request;
    }
    
    /**
     * Create a new donation request
     * @param array $data The request data
     * @return string|bool The request ID if successful, false otherwise
     */
   /* public function createRequest($data) {
        $this->db->beginTransaction();
        
        try {
            // Generate RequestID (Format: REQ + 5 digits)
            $requestId = 'REQ' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            
            // Insert request record
            $this->db->query('INSERT INTO donation_requests (
                RequestID, RecipientID, RequestType, Category, Title, Description, 
                ProofDocument, Deadline, VerificationStatus, RequestStatus
            ) VALUES (
                :requestId, :recipientId, :requestType, :category, :title, :description, 
                :proofDocument, :deadline, "Pending", "Pending"
            )');
            
            $this->db->bind(':requestId', $requestId);
            $this->db->bind(':recipientId', $data['recipientId']);
            $this->db->bind(':requestType', $data['requestType']);
            $this->db->bind(':category', $data['category']);
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':proofDocument', $data['proofDocument'] ?? null);
            $this->db->bind(':deadline', $data['deadline']);
            
            $requestResult = $this->db->execute();
            
            // Insert type-specific details
            if ($data['requestType'] == 'Monetary') {
                $detailId = 'MD' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                
                $this->db->query('INSERT INTO monetary_donation_details (
                    DetailID, RequestID, TargetAmount, CurrentAmount
                ) VALUES (
                    :detailId, :requestId, :targetAmount, 0
                )');
                
                $this->db->bind(':detailId', $detailId);
                $this->db->bind(':requestId', $requestId);
                $this->db->bind(':targetAmount', $data['targetAmount']);
                
                $detailResult = $this->db->execute();
            } else {
                $detailId = 'NMD' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                
                $this->db->query('INSERT INTO nonmonetary_donation_details (
                    DetailID, RequestID, ItemName, QuantityNeeded, QuantityReceived, 
                    Province, DropOffLocation, DropOffTime
                ) VALUES (
                    :detailId, :requestId, :itemName, :quantityNeeded, 0, 
                    :province, :dropOffLocation, :dropOffTime
                )');
                
                $this->db->bind(':detailId', $detailId);
                $this->db->bind(':requestId', $requestId);
                $this->db->bind(':itemName', $data['itemName']);
                $this->db->bind(':quantityNeeded', $data['quantityNeeded']);
                $this->db->bind(':province', $data['province']);
                $this->db->bind(':dropOffLocation', $data['dropOffLocation']);
                $this->db->bind(':dropOffTime', $data['dropOffTime']);
                
                $detailResult = $this->db->execute();
            }
            
            // If all operations successful, commit transaction
            if ($requestResult && $detailResult) {
                $this->db->commit();
                return $requestId;
            } else {
                $this->db->rollBack();
                return false;
            }
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Request Creation Error: " . $e->getMessage());
            return false;
        }
    }*/


    public function createRequest($data) {
        $this->db->beginTransaction();
        
        try {
            // Generate RequestID (Format: REQ + 5 digits)
            $requestId = 'REQ' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            
            // Determine title and use a default if not set
            $title = !empty($data['requestTitle']) ? $data['requestTitle'] : $data['title'];
            
            // Prepare ProofDocument value
            $proofDocument = $requestId . '.pdf';
            
            // Insert request record
            $this->db->query('INSERT INTO donation_requests (
                RequestID, RecipientID, RequestType, Category, Title, Description, 
                ProofDocument, Deadline, VerificationStatus, RequestStatus
            ) VALUES (
                :requestId, :recipientId, :requestType, :category, :title, :description, 
                :proofDocument, :deadline, "Pending", "Pending"
            )');
            
            // Bind parameters with null coalescing and type checking
            $this->db->bind(':requestId', $requestId);
            $this->db->bind(':recipientId', $data['recipientId']);
            $this->db->bind(':requestType', $data['requestType']);
            $this->db->bind(':category', $data['category']);
            $this->db->bind(':title', $title);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':proofDocument', $proofDocument);
            $this->db->bind(':deadline', $data['deadline']);
            
            $requestResult = $this->db->execute();
            
            // Insert type-specific details
            if ($data['requestType'] == 'Monetary') {
                // Validate monetary request details
                if (!isset($data['targetAmount']) || $data['targetAmount'] <= 0) {
                    throw new Exception("Invalid target amount for monetary request");
                }
                
                $detailId = 'MD' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                
                $this->db->query('INSERT INTO monetary_donation_details (
                    DetailID, RequestID, TargetAmount, CurrentAmount
                ) VALUES (
                    :detailId, :requestId, :targetAmount, 0
                )');
                
                $this->db->bind(':detailId', $detailId);
                $this->db->bind(':requestId', $requestId);
                $this->db->bind(':targetAmount', $data['targetAmount']);
                
                $detailResult = $this->db->execute();
            } else {
                // Validate non-monetary request details
                if (
                    empty($data['itemName']) || 
                    !isset($data['quantityNeeded']) || 
                    $data['quantityNeeded'] <= 0 || 
                    empty($data['province']) || 
                    empty($data['dropOffLocation']) || 
                    empty($data['dropOffTime'])
                ) {
                    throw new Exception("Invalid details for non-monetary request");
                }
                
                $detailId = 'NMD' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                
                $this->db->query('INSERT INTO nonmonetary_donation_details (
                    DetailID, RequestID, ItemName, QuantityNeeded, QuantityReceived, 
                    Province, DropOffLocation, DropOffTime
                ) VALUES (
                    :detailId, :requestId, :itemName, :quantityNeeded, 0, 
                    :province, :dropOffLocation, :dropOffTime
                )');
                
                $this->db->bind(':detailId', $detailId);
                $this->db->bind(':requestId', $requestId);
                $this->db->bind(':itemName', $data['itemName']);
                $this->db->bind(':quantityNeeded', $data['quantityNeeded']);
                $this->db->bind(':province', $data['province']);
                $this->db->bind(':dropOffLocation', $data['dropOffLocation']);
                $this->db->bind(':dropOffTime', $data['dropOffTime']);
                
                $detailResult = $this->db->execute();
            }
            
            // If all operations successful, commit transaction
            if ($requestResult && $detailResult) {
                $this->db->commit();
                
                // Log successful request creation
                error_log("Request created successfully: $requestId");
                
                return $requestId;
            } else {
                // Rollback transaction and log error
                $this->db->rollBack();
                error_log("Request Creation Failed: Database insertion error for RequestID $requestId");
                return false;
            }
            
        } catch (Exception $e) {
            // Rollback transaction and log detailed error
            $this->db->rollBack();
            error_log("Request Creation Error: " . $e->getMessage());
            
            // Optionally, you can log additional context
            error_log("Request Data: " . json_encode($data));
            
            return false;
        }
    }

    /**
     * Update an existing donation request
     * @param array $data The request data
     * @return bool True if successful, false otherwise
     */
    public function updateRequest($data) {
        $this->db->beginTransaction();
        
        try {
            // Update request record
            $this->db->query('UPDATE donation_requests SET 
                Category = :category, 
                Title = :title, 
                Description = :description,
                Deadline = :deadline
                ' . (!empty($data['proofDocument']) ? ', ProofDocument = :proofDocument' : '') . '
                WHERE RequestID = :requestId AND RecipientID = :recipientId');
            
            $this->db->bind(':requestId', $data['requestId']);
            $this->db->bind(':recipientId', $data['recipientId']);
            $this->db->bind(':category', $data['category']);
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':deadline', $data['deadline']);
            
            if (!empty($data['proofDocument'])) {
                $this->db->bind(':proofDocument', $data['proofDocument']);
            }
            
            $requestResult = $this->db->execute();
            
            // Update type-specific details
            if ($data['requestType'] == 'Monetary') {
                $this->db->query('UPDATE monetary_donation_details SET 
                    TargetAmount = :targetAmount
                    WHERE RequestID = :requestId');
                
                $this->db->bind(':requestId', $data['requestId']);
                $this->db->bind(':targetAmount', $data['targetAmount']);
                
                $detailResult = $this->db->execute();
            } else {
                $this->db->query('UPDATE nonmonetary_donation_details SET 
                    ItemName = :itemName, 
                    QuantityNeeded = :quantityNeeded, 
                    Province = :province, 
                    DropOffLocation = :dropOffLocation, 
                    DropOffTime = :dropOffTime
                    WHERE RequestID = :requestId');
                
                $this->db->bind(':requestId', $data['requestId']);
                $this->db->bind(':itemName', $data['itemName']);
                $this->db->bind(':quantityNeeded', $data['quantityNeeded']);
                $this->db->bind(':province', $data['province']);
                $this->db->bind(':dropOffLocation', $data['dropOffLocation']);
                $this->db->bind(':dropOffTime', $data['dropOffTime']);
                
                $detailResult = $this->db->execute();
            }
            
            // If all operations successful, commit transaction
            if ($requestResult && $detailResult) {
                $this->db->commit();
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Request Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a donation request
     * @param string $requestId The request ID
     * @param string $recipientId The recipient ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function deleteRequest($requestId, $recipientId) {
        $this->db->beginTransaction();
        
        try {
            // First check if request belongs to recipient and is in Pending status
            $this->db->query('SELECT * FROM donation_requests 
                             WHERE RequestID = :requestId 
                             AND RecipientID = :recipientId 
                             AND (RequestStatus = "Pending" OR VerificationStatus = "Rejected")');
            
            $this->db->bind(':requestId', $requestId);
            $this->db->bind(':recipientId', $recipientId);
            
            $request = $this->db->single();
            
            if (!$request) {
                // Not eligible for deletion
                $this->db->rollBack();
                return false;
            }
            
            // Delete type-specific details
            if ($request->RequestType == 'Monetary') {
                $this->db->query('DELETE FROM monetary_donation_details WHERE RequestID = :requestId');
                $this->db->bind(':requestId', $requestId);
                $this->db->execute();
            } else {
                $this->db->query('DELETE FROM nonmonetary_donation_details WHERE RequestID = :requestId');
                $this->db->bind(':requestId', $requestId);
                $this->db->execute();
            }
            
            // Delete request record
            $this->db->query('DELETE FROM donation_requests WHERE RequestID = :requestId AND RecipientID = :recipientId');
            $this->db->bind(':requestId', $requestId);
            $this->db->bind(':recipientId', $recipientId);
            
            $result = $this->db->execute();
            
            // Commit if successful
            if ($result) {
                $this->db->commit();
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Request Deletion Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get donations for a request
     * @param string $requestId The request ID
     * @return array Donations for this request
     */
    public function getDonationsByRequest($requestId) {
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

    // Send personalized impact update to specific donor

}