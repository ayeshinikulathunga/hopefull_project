<?php
class DonationRequest {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getRecentApprovedRequests($limit = 3) {
        $this->db->query('SELECT dr.*, 
                         r.FirstName as RecipientFirstName, r.LastName as RecipientLastName
                         FROM donation_requests dr
                         JOIN recipients r ON dr.RecipientID = r.RecipientID 
                         WHERE dr.VerificationStatus = "Approved" 
                         AND dr.RequestStatus IN ("Pending", "InProgress") 
                         AND dr.Deadline >= CURDATE() 
                         ORDER BY dr.CreatedDate DESC 
                         LIMIT :limit');
        
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        
        $requests = $this->db->resultSet();
        
        // Check for proof documents for each request
        foreach ($requests as $request) {
            // Set recipient name
            if (isset($request->RecipientFirstName) && isset($request->RecipientLastName)) {
                $request->RecipientName = $request->RecipientFirstName . ' ' . $request->RecipientLastName;
            }
            
            // Check if proof document exists physically
            $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->RequestID . '.pdf';
            $request->ProofDocumentExists = file_exists($pdfPath);
            
            // If physical file not found, check if we have a ProofDocument field with a value
            if (!$request->ProofDocumentExists && !empty($request->ProofDocument)) {
                $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->ProofDocument;
                $request->ProofDocumentExists = file_exists($pdfPath);
            }
            
            // Get donation details based on type
            if ($request->RequestType == 'Monetary') {
                $details = $this->getMonetaryRequestDetails($request->RequestID);
                if ($details) {
                    $request->TargetAmount = $details->TargetAmount;
                    $request->CurrentAmount = $details->CurrentAmount;
                }
            } else {
                $details = $this->getNonMonetaryRequestDetails($request->RequestID);
                if ($details) {
                    $request->QuantityNeeded = $details->QuantityNeeded;
                    $request->QuantityReceived = $details->QuantityReceived;
                }
            }
        }
        
        return $requests;
    }

    public function getRequestById($requestId) {
        $this->db->query('SELECT dr.*, 
                         r.FirstName as RecipientFirstName, r.LastName as RecipientLastName
                         FROM donation_requests dr
                         JOIN recipients r ON dr.RecipientID = r.RecipientID 
                         WHERE dr.RequestID = :requestId');
        $this->db->bind(':requestId', $requestId);
        
        $result = $this->db->single();
        $request = $result ? $result : null;
        
        if ($request) {
            // Set recipient name
            if (isset($request->RecipientFirstName) && isset($request->RecipientLastName)) {
                $request->RecipientName = $request->RecipientFirstName . ' ' . $request->RecipientLastName;
            }
            
            // Check if proof document exists physically
            $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->RequestID . '.pdf';
            $request->ProofDocumentExists = file_exists($pdfPath);
            
            // If physical file not found, check if we have a ProofDocument field with a value
            if (!$request->ProofDocumentExists && !empty($request->ProofDocument)) {
                $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->ProofDocument;
                $request->ProofDocumentExists = file_exists($pdfPath);
            }
            
            // Get donation details based on type
            if ($request->RequestType == 'Monetary') {
                $details = $this->getMonetaryRequestDetails($request->RequestID);
                if ($details) {
                    $request->TargetAmount = $details->TargetAmount;
                    $request->CurrentAmount = $details->CurrentAmount;
                }
            } else {
                $details = $this->getNonMonetaryRequestDetails($request->RequestID);
                if ($details) {
                    $request->QuantityNeeded = $details->QuantityNeeded;
                    $request->QuantityReceived = $details->QuantityReceived;
                }
            }
        }
        
        return $request;
    }

    public function getMonetaryRequestDetails($requestId) {
        $this->db->query('SELECT * FROM monetary_donation_details WHERE RequestID = :requestId');
        $this->db->bind(':requestId', $requestId);
        
        return $this->db->single();
    }

    public function getNonMonetaryRequestDetails($requestId) {
        $this->db->query('SELECT * FROM nonmonetary_donation_details WHERE RequestID = :requestId');
        $this->db->bind(':requestId', $requestId);
        
        return $this->db->single();
    }

    public function getFilteredRequests($category, $type, $search, $limit, $offset) {
        $sql = 'SELECT dr.*, 
                r.FirstName as RecipientFirstName, r.LastName as RecipientLastName
                FROM donation_requests dr
                JOIN recipients r ON dr.RecipientID = r.RecipientID 
                WHERE dr.VerificationStatus = "Approved" 
                AND dr.RequestStatus IN ("Pending", "InProgress") 
                AND dr.Deadline >= CURDATE()';
        
        // Add category filter if provided
        if(!empty($category)) {
            $sql .= ' AND dr.Category = :category';
        }
        
        // Add type filter if provided
        if(!empty($type)) {
            $sql .= ' AND dr.RequestType = :type';
        }
        
        // Add search filter if provided
        if(!empty($search)) {
            $sql .= ' AND (dr.Title LIKE :search OR dr.Description LIKE :search)';
        }
        
        // Add ordering and pagination
        $sql .= ' ORDER BY dr.CreatedDate DESC LIMIT :limit OFFSET :offset';
        
        $this->db->query($sql);
        
        // Bind parameters if they exist
        if(!empty($category)) {
            $this->db->bind(':category', $category);
        }
        
        if(!empty($type)) {
            $this->db->bind(':type', $type);
        }
        
        if(!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }
        
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        
        $requests = $this->db->resultSet();
        
        // Enhance each request with additional details
        foreach ($requests as $request) {
            // Set recipient name
            if (isset($request->RecipientFirstName) && isset($request->RecipientLastName)) {
                $request->RecipientName = $request->RecipientFirstName . ' ' . $request->RecipientLastName;
            }
            
            // Check if proof document exists physically
            $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->RequestID . '.pdf';
            $request->ProofDocumentExists = file_exists($pdfPath);
            
            // If physical file not found, check if we have a ProofDocument field with a value
            if (!$request->ProofDocumentExists && !empty($request->ProofDocument)) {
                $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->ProofDocument;
                $request->ProofDocumentExists = file_exists($pdfPath);
            }
            
            // Get donation details based on type
            if ($request->RequestType == 'Monetary') {
                $details = $this->getMonetaryRequestDetails($request->RequestID);
                if ($details) {
                    $request->TargetAmount = $details->TargetAmount;
                    $request->CurrentAmount = $details->CurrentAmount;
                }
            } else {
                $details = $this->getNonMonetaryRequestDetails($request->RequestID);
                if ($details) {
                    $request->QuantityNeeded = $details->QuantityNeeded;
                    $request->QuantityReceived = $details->QuantityReceived;
                    $request->ItemName = $details->ItemName;
                }
            }
        }
        
        return $requests;
    }
    
    public function countFilteredRequests($category, $type, $search) {
        $sql = 'SELECT COUNT(*) as total FROM donation_requests dr 
                WHERE dr.VerificationStatus = "Approved" 
                AND dr.RequestStatus IN ("Pending", "InProgress") 
                AND dr.Deadline >= CURDATE()';
        
        // Add category filter if provided
        if(!empty($category)) {
            $sql .= ' AND dr.Category = :category';
        }
        
        // Add type filter if provided
        if(!empty($type)) {
            $sql .= ' AND dr.RequestType = :type';
        }
        
        // Add search filter if provided
        if(!empty($search)) {
            $sql .= ' AND (dr.Title LIKE :search OR dr.Description LIKE :search)';
        }
        
        $this->db->query($sql);
        
        // Bind parameters if they exist
        if(!empty($category)) {
            $this->db->bind(':category', $category);
        }
        
        if(!empty($type)) {
            $this->db->bind(':type', $type);
        }
        
        if(!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }
        
        $result = $this->db->single();
        return $result->total;
    }
    
    /**
     * Check if a proof document exists for a request
     * @param int $requestId The request ID to check
     * @return bool True if document exists, false otherwise
     */
    public function proofDocumentExists($requestId) {
        // Get the request to check for ProofDocument field
        $this->db->query('SELECT ProofDocument FROM donation_requests WHERE RequestID = :requestId');
        $this->db->bind(':requestId', $requestId);
        $request = $this->db->single();
        
        if (!$request) {
            return false;
        }
        
        // First check if a file exists with requestId.pdf
        $pdfPath = APPROOT . '/../public/uploads/documents/' . $requestId . '.pdf';
        if (file_exists($pdfPath)) {
            return true;
        }
        
        // If not found, and we have a ProofDocument value, check that path
        if (!empty($request->ProofDocument)) {
            $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->ProofDocument;
            return file_exists($pdfPath);
        }
        
        return false;
    }
    
    /**
     * Get the URL of a proof document
     * @param int $requestId The request ID
     * @return string|null The URL to the proof document, or null if not found
     */
    public function getProofDocumentUrl($requestId) {
        // Get the request to check for ProofDocument field
        $this->db->query('SELECT ProofDocument FROM donation_requests WHERE RequestID = :requestId');
        $this->db->bind(':requestId', $requestId);
        $request = $this->db->single();
        
        if (!$request) {
            return null;
        }
        
        // First check if a file exists with requestId.pdf
        $pdfPath = APPROOT . '/../public/uploads/documents/' . $requestId . '.pdf';
        if (file_exists($pdfPath)) {
            return URLROOT . '/uploads/documents/' . $requestId . '.pdf';
        }
        
        // If not found, and we have a ProofDocument value, use that path
        if (!empty($request->ProofDocument)) {
            $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->ProofDocument;
            if (file_exists($pdfPath)) {
                return URLROOT . '/uploads/documents/' . $request->ProofDocument;
            }
        }
        
        return null;
    }


    /**
 * Get similar donation requests based on category
 * @param string $currentRequestId The ID of the current request to exclude
 * @param string $category The category to match
 * @param int $limit The maximum number of requests to return
 * @return array Array of similar donation requests
 */
public function getSimilarRequests($currentRequestId, $category, $limit = 3) {
    $this->db->query('SELECT dr.*, 
                     r.FirstName as RecipientFirstName, r.LastName as RecipientLastName
                     FROM donation_requests dr
                     JOIN recipients r ON dr.RecipientID = r.RecipientID 
                     WHERE dr.RequestID != :currentRequestId
                     AND dr.Category = :category
                     AND dr.VerificationStatus = "Approved" 
                     AND dr.RequestStatus IN ("Pending", "InProgress") 
                     AND dr.Deadline >= CURDATE() 
                     ORDER BY dr.CreatedDate DESC 
                     LIMIT :limit');
    
    $this->db->bind(':currentRequestId', $currentRequestId);
    $this->db->bind(':category', $category);
    $this->db->bind(':limit', $limit, PDO::PARAM_INT);
    
    $requests = $this->db->resultSet();
    
    // Process each request to add additional details
    foreach ($requests as $request) {
        // Set recipient name
        if (isset($request->RecipientFirstName) && isset($request->RecipientLastName)) {
            $request->RecipientName = $request->RecipientFirstName . ' ' . $request->RecipientLastName;
        }
        
        // Check if proof document exists
        $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->RequestID . '.pdf';
        $request->ProofDocumentExists = file_exists($pdfPath);
        
        // If physical file not found, check if we have a ProofDocument field with a value
        if (!$request->ProofDocumentExists && !empty($request->ProofDocument)) {
            $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->ProofDocument;
            $request->ProofDocumentExists = file_exists($pdfPath);
        }
        
        // Get donation details based on type
        if ($request->RequestType == 'Monetary') {
            $details = $this->getMonetaryRequestDetails($request->RequestID);
            if ($details) {
                $request->TargetAmount = $details->TargetAmount;
                $request->CurrentAmount = $details->CurrentAmount;
            }
        } else {
            $details = $this->getNonMonetaryRequestDetails($request->RequestID);
            if ($details) {
                $request->QuantityNeeded = $details->QuantityNeeded;
                $request->QuantityReceived = $details->QuantityReceived;
                $request->ItemName = $details->ItemName;
            }
        }
    }
    
    return $requests;
}

/**
 * Get a donation request with all its details
 * @param string $requestId The ID of the request to retrieve
 * @return object|bool The request with all details or false if not found
 */
public function getRequestWithFullDetails($requestId) {
    $request = $this->getRequestById($requestId);
    
    if (!$request) {
        return false;
    }
    
    // Additional processing can be done here if needed
    
    return $request;
}
}