<?php
class Recipient {
    private $db;
    private $recipientModel;
    
    public function __construct() {
        
        $this->db = new Database();
    }
    
    // Get recipient by ID
    public function getRecipientById($id) {
        // First get the basic recipient info
        $this->db->query('SELECT * FROM recipients WHERE RecipientID = :id');
        $this->db->bind(':id', $id);
        $recipient = $this->db->single();
        
        if (!$recipient) {
            return false;
        }
        
        // Calculate total monetary donations
        $this->db->query('SELECT COALESCE(SUM(d.Amount), 0) as total 
                         FROM donations d
                         JOIN donation_requests dr ON d.RequestID = dr.RequestID
                         WHERE dr.RecipientID = :recipientId 
                         AND d.DonationType = "Monetary"
                         AND d.Status = "Completed"');
        $this->db->bind(':recipientId', $id);
        $result = $this->db->single();
        $recipient->TotalMonetaryDonations = $result->total;
        
        // Calculate total donations count
        $this->db->query('SELECT COUNT(*) as total 
                         FROM donations d
                         JOIN donation_requests dr ON d.RequestID = dr.RequestID
                         WHERE dr.RecipientID = :recipientId
                         AND d.Status = "Completed"');
        $this->db->bind(':recipientId', $id);
        $result = $this->db->single();
        $recipient->TotalDonationsReceived = $result->total;
        
        return $recipient;
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

    

    public function updateProfile($data) {
        $this->db->query('UPDATE recipients SET 
                        FirstName = :firstName, 
                        LastName = :lastName, 
                        ContactNumber = :contactNumber, 
                        OrganizationName = :organization, 
                        Address = :address 
                        WHERE UserID = :userId');
        
        // Bind values
        $this->db->bind(':firstName', $data['firstName']);
        $this->db->bind(':lastName', $data['lastName']);
        $this->db->bind(':contactNumber', $data['contactNumber']);
        $this->db->bind(':organization', $data['organization']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':userId', $data['userId']);
        
        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($userId, $password) {
        $this->db->query('SELECT Password FROM users WHERE UserID = :userId');
        $this->db->bind(':userId', $userId);
        
        $row = $this->db->single();
        
        if (password_verify($password, $row->Password)) {
            return true;
        } else {
            return false;
        }
    }

    public function changePassword($userId, $newPassword) {
        $this->db->query('UPDATE users SET Password = :password WHERE UserID = :userId');
        
        // Hash Password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Bind values
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':userId', $userId);
        
        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteAccount($userId) {
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Delete related records first (respecting foreign key constraints)
            // Note: You would need to adjust these queries based on your actual database schema
            
            // Delete donation records
            $this->db->query('DELETE FROM donations WHERE RequestID IN (SELECT RequestID FROM donation_requests WHERE RecipientID = (SELECT RecipientID FROM recipients WHERE UserID = :userId))');
            $this->db->bind(':userId', $userId);
            $this->db->execute();
            
            // Delete requests
            $this->db->query('DELETE FROM donation_requests WHERE RecipientID = (SELECT RecipientID FROM recipients WHERE UserID = :userId)');
            $this->db->bind(':userId', $userId);
            $this->db->execute();
            
            // Delete recipient profile
            $this->db->query('DELETE FROM recipients WHERE UserID = :userId');
            $this->db->bind(':userId', $userId);
            $this->db->execute();
            
            // Finally, delete the user account
            $this->db->query('DELETE FROM users WHERE UserID = :userId');
            $this->db->bind(':userId', $userId);
            $this->db->execute();
            
            // Commit transaction
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Rollback if any error occurs
            $this->db->rollback();
            return false;
        }
    }

    public function getRequestStats($recipientId) {
        $this->db->query('SELECT 
                        COUNT(*) as TotalRequests,
                        SUM(CASE WHEN Status = "completed" THEN 1 ELSE 0 END) as CompletedRequests,
                        SUM(CASE WHEN Status = "in progress" THEN 1 ELSE 0 END) as InProgressRequests,
                        COUNT(DISTINCT DonorID) as TotalContributors
                        FROM donation_requests r
                        LEFT JOIN donations d ON r.RequestID = d.RequestID
                        WHERE r.RecipientID = :recipientId');
        
        $this->db->bind(':recipientId', $recipientId);
        
        return $this->db->single();
    }

    // Get recent donations
    public function getRecentDonations($recipientId, $limit = 5) {
        $this->db->query('SELECT d.*, 
                        r.Title as RequestTitle, 
                        CONCAT(donors.FirstName, " ", donors.LastName) as DonorName,
                        d.DonationType,
                        CASE 
                            WHEN d.DonationType = "monetary" THEN d.Amount
                            ELSE NULL
                        END as Amount,
                        CASE 
                            WHEN d.DonationType = "item" THEN d.ItemName
                            ELSE NULL
                        END as ItemName
                        FROM donations d
                        JOIN donation_requests r ON d.RequestID = r.RequestID
                        LEFT JOIN donors ON d.DonorID = donors.DonorID
                        WHERE r.RecipientID = :recipientId
                        ORDER BY d.DonationDate DESC
                        LIMIT :limit');
        
        $this->db->bind(':recipientId', $recipientId);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    // Get active requests
    public function getActiveRequests($recipientId, $limit = 3) {
        $this->db->query('SELECT r.*,
                        IFNULL(
                            CASE 
                                WHEN r.RequestType = "monetary" THEN
                                    (IFNULL(SUM(d.Amount), 0) / r.TargetAmount) * 100
                                WHEN r.RequestType = "item" THEN
                                    (IFNULL(COUNT(d.DonationID), 0) / r.Quantity) * 100
                                ELSE 0
                            END, 0
                        ) as PercentComplete
                        FROM donation_requests r
                        LEFT JOIN donation_requests d ON r.RequestID = d.RequestID
                        WHERE r.RecipientID = :recipientId 
                        AND r.RequestStatus IN ("pending", "in progress","completed","expired")
                        AND r.VerificationStatus IN ("pending", "approved","rejected")
                        GROUP BY r.RequestID
                        ORDER BY r.Deadline ASC
                        LIMIT :limit');
        
        $this->db->bind(':recipientId', $recipientId);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    // Get request history
    public function getRequestHistory($recipientId, $limit = 5) {
        $this->db->query('SELECT r.*,
                        COUNT(DISTINCT d.DonorID) as DonorCount
                        FROM donation_requests r
                        LEFT JOIN donations d ON r.RequestID = d.RequestID
                        WHERE r.RecipientID = :recipientId
                        GROUP BY r.RequestID
                        ORDER BY r.CreatedDate DESC
                        LIMIT :limit');
        
        $this->db->bind(':recipientId', $recipientId);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    // Get calendar data
    public function getCalendarData($recipientId) {
        $this->db->query('SELECT 
                        RequestID,
                        Title,
                        CreatedDate,
                        Deadline,
                        RequestStatus,
                        RequestType
                        FROM donation_requests
                        WHERE RecipientID = :recipientId
                        ORDER BY CreatedDate DESC');
        
        $this->db->bind(':recipientId', $recipientId);
        
        return $this->db->resultSet();
    }

    public function getTotalDonations($recipientId) {
        $this->db->query('SELECT 
                            SUM(CASE 
                                WHEN d.DonationType = "monetary" THEN d.Amount 
                                ELSE 0 
                            END) as TotalMonetaryDonations,
                            COUNT(CASE 
                                WHEN d.DonationType = "item" THEN d.DonationID 
                                ELSE NULL 
                            END) as TotalItemDonations
                          FROM donations d
                          JOIN donation_requests r ON d.RequestID = r.RequestID
                          WHERE r.RecipientID = :recipientId');
        
        $this->db->bind(':recipientId', $recipientId);
        
        return $this->db->single();
    }


}
