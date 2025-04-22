<?php
class Donations extends Controller {
    private $donationRequestModel;
    private $donationModel;
    private $userModel;
    private $requestModel;
    
    public function __construct() {
        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        
        $this->donationRequestModel = $this->model('DonationRequest');
        $this->donationModel = $this->model('Donation'); // Add this line
        $this->userModel = $this->model('User');
        $this->requestModel = $this->model('DonationRequest');
    }
    
    public function index() {
        // Get recent donation requests
        $requests = $this->donationRequestModel->getRecentApprovedRequests(6);
        
        // Process each request to add recipient name and other details
        foreach ($requests as $request) {
            if (isset($request->RecipientFirstName) && isset($request->RecipientLastName)) {
                $request->RecipientName = $request->RecipientFirstName . ' ' . $request->RecipientLastName;
            }
            
            // Get appropriate details based on request type
            if ($request->RequestType == 'Monetary') {
                $details = $this->donationRequestModel->getMonetaryRequestDetails($request->RequestID);
                if ($details) {
                    $request->TargetAmount = $details->TargetAmount;
                    $request->CurrentAmount = $details->CurrentAmount;
                }
            } else {
                $details = $this->donationRequestModel->getNonMonetaryRequestDetails($request->RequestID);
                if ($details) {
                    $request->QuantityNeeded = $details->QuantityNeeded;
                    $request->QuantityReceived = $details->QuantityReceived;
                }
            }
            
            // Check if proof document exists
            $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->RequestID . '.pdf';
            $request->ProofDocumentExists = file_exists($pdfPath);
        }
        
        $data = [
            'title' => 'Donation Dashboard',
            'requests' => $requests
        ];
        
        $this->view('donations/index', $data);
    }
    
    public function requests() {
        // Get page number for pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        // Items per page
        $perPage = 9;
        
        // Calculate offset for pagination
        $offset = ($page - 1) * $perPage;
        
        // Get filter values
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        // Get filtered requests with pagination
        $requests = $this->donationRequestModel->getFilteredRequests($category, $type, $search, $perPage, $offset);
        
        // Count total matching requests for pagination
        $totalRequests = $this->donationRequestModel->countFilteredRequests($category, $type, $search);
        $totalPages = ceil($totalRequests / $perPage);
        
        // Build query string for pagination links
        $queryParams = [];
        if (!empty($category)) $queryParams[] = 'category=' . urlencode($category);
        if (!empty($type)) $queryParams[] = 'type=' . urlencode($type);
        if (!empty($search)) $queryParams[] = 'search=' . urlencode($search);
        
        $queryString = !empty($queryParams) ? '&' . implode('&', $queryParams) : '';
        
        $data = [
            'title' => 'Donation Requests',
            'requests' => $requests,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalRequests' => $totalRequests,
            'queryString' => $queryString
        ];
        
        $this->view('donations/requests', $data);
    }
    

    public function donate($requestId = null) {
        // Check if valid request ID is provided
        if ($requestId === null) {
            flash('donation_error', 'Invalid request', 'alert alert-danger');
            redirect('donors/dashboard');
            return;
        }
        
        // Get request details
        $request = $this->donationRequestModel->getRequestWithFullDetails($requestId);
        
        if (!$request) {
            flash('donation_error', 'Request not found', 'alert alert-danger');
            redirect('donors/dashboard');
            return;
        }
        
        // Check if request is still active
        if ($request->RequestStatus !== 'Pending' && $request->RequestStatus !== 'InProgress') {
            flash('donation_error', 'This request is no longer accepting donations', 'alert alert-danger');
            redirect('donors/dashboard');
            return;
        }
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Initialize data array
            $data = [
                'title' => 'Donate to ' . $request->Title,
                'request' => $request,
                'amount' => trim($_POST['amount'] ?? ''),
                'quantity' => trim($_POST['quantity'] ?? ''),
                'payment_method' => trim($_POST['payment_method'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
                'amount_err' => '',
                'quantity_err' => '',
                'payment_method_err' => ''
            ];
            
            // Get additional data for the forms
            if ($request->RequestType == 'NonMonetary') {
                $data['itemDetails'] = $this->donationRequestModel->getNonMonetaryRequestDetails($requestId);
            }
            
            // Validate based on request type
            if ($request->RequestType === 'Monetary') {
                // Validate amount
                if (empty($data['amount'])) {
                    $data['amount_err'] = 'Please enter donation amount';
                } else if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
                    $data['amount_err'] = 'Amount must be a positive number';
                }
                
                // Validate payment method
                if (empty($data['payment_method'])) {
                    $data['payment_method_err'] = 'Please select payment method';
                }
                
                // If no errors, process monetary donation
                if (empty($data['amount_err']) && empty($data['payment_method_err'])) {
                    // Create donation record
                    $donationData = [
                        'donorId' => $_SESSION['donor_id'],
                        'requestId' => $requestId,
                        'donationType' => 'Monetary',
                        'amount' => $data['amount'],
                        'quantity' => null,
                        'isAnonymous' => isset($_POST['anonymous']) ? 1 : 0,
                        'notes' => $data['notes']
                    ];
                    
                    $donationId = $this->donationModel->createDonation($donationData);
                    if ($donationId) {
                        // Get the created donation for confirmation page
                        $donation = $this->donationModel->getDonationById($donationId);
                        
                        // Prepare data for confirmation page
                        $confirmData = [
                            'donation' => $donation,
                            'request' => $request
                        ];
                        
                        // Go to confirmation page
                        $this->view('donors/donation-confirmation', $confirmData);
                        return;
                    } else {
                        flash('donation_error', 'Something went wrong. Please try again.', 'alert alert-danger');
                    }
                }
            } else {
                // Non-monetary donation validation
                if (empty($data['quantity'])) {
                    $data['quantity_err'] = 'Please enter donation quantity';
                } else if (!is_numeric($data['quantity']) || $data['quantity'] <= 0 || floor($data['quantity']) != $data['quantity']) {
                    $data['quantity_err'] = 'Quantity must be a positive whole number';
                }
                
                // If no errors, process non-monetary donation
                if (empty($data['quantity_err'])) {
                    // Create donation record
                    $donationData = [
                        'donorId' => $_SESSION['donor_id'],
                        'requestId' => $requestId,
                        'donationType' => 'NonMonetary',
                        'amount' => null,
                        'quantity' => $data['quantity'],
                        'isAnonymous' => isset($_POST['anonymous']) ? 1 : 0,
                        'dropOffDate' => $_POST['dropoff_date'] ?? null,
                        'dropOffTime' => $_POST['dropoff_time'] ?? null,
                        'notes' => $data['notes']
                    ];
                    
                    // Use createNonMonetaryDonation method instead of createDonation
                    $donationId = $this->donationModel->createNonMonetaryDonation($donationData);
                    if ($donationId) {
                        // Get the created donation for confirmation page
                        $donation = $this->donationModel->getDonationById($donationId);
                        $scheduleDetails = $this->donationModel->getNonMonetaryDonationDetails($donationId);
                        
                        // Prepare data for confirmation page
                        $confirmData = [
                            'donation' => $donation,
                            'request' => $request,
                            'itemDetails' => $data['itemDetails'],
                            'scheduleDetails' => $scheduleDetails
                        ];
                        
                        // Go to confirmation page
                        $this->view('donors/donation-confirmation', $confirmData);
                        return;
                    } else {
                        flash('donation_error', 'Something went wrong. Please try again.', 'alert alert-danger');
                    }
                }
            }
            
            // If we got here, there were errors, display the form again with errors
            if ($request->RequestType === 'Monetary') {
                $this->view('donors/monetary-donation-form', $data);
            } else {
                $this->view('donors/non-monetary-donation-form', $data);
            }
        } else {
            // Display the donation form (GET request)
            $data = [
                'title' => 'Donate to ' . $request->Title,
                'request' => $request,
                'amount' => '',
                'quantity' => '',
                'payment_method' => '',
                'notes' => '',
                'amount_err' => '',
                'quantity_err' => '',
                'payment_method_err' => ''
            ];
            
            // Get additional data for the forms
            if ($request->RequestType == 'NonMonetary') {
                $data['itemDetails'] = $this->donationRequestModel->getNonMonetaryRequestDetails($requestId);
            }
            
            // Load the appropriate form based on request type
            if ($request->RequestType === 'Monetary') {
                $this->view('donors/monetary-donation-form', $data);
            } else {
                $this->view('donors/non-monetary-donation-form', $data);
            }
        }
    }

    
    /**
     * Downloads a proof document for a request
     * @param int $requestId The request ID
     */
    public function downloadProof($requestId) {
        // Verify that the request exists
        $request = $this->donationRequestModel->getRequestById($requestId);
        
        if (!$request) {
            flash('donation_error', 'Request not found', 'alert alert-danger');
            redirect('donations');
            return;
        }
        
        // Check if proof document exists
        $pdfPath = APPROOT . '/../public/uploads/documents/' . $requestId . '.pdf';
        
        if (!file_exists($pdfPath)) {
            flash('donation_error', 'Document not found', 'alert alert-danger');
            redirect('donations/requests');
            return;
        }
        
        // Set headers for file download
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="proof_document_' . $requestId . '.pdf"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Output file
        readfile($pdfPath);
        exit;
    }
    
    public function viewProof($requestId) {
        // Set header to return JSON
        header('Content-Type: application/json');
        
        // Verify that the request exists
        $request = $this->donationRequestModel->getRequestById($requestId);
        
        if (!$request) {
            echo json_encode(['success' => false, 'message' => 'Request not found']);
            return;
        }
        
        // First check if a file exists with requestId.pdf
        $pdfPath = APPROOT . '/../public/uploads/documents/' . $requestId . '.pdf';
        if (file_exists($pdfPath)) {
            echo json_encode([
                'success' => true, 
                'url' => URLROOT . '/uploads/documents/' . $requestId . '.pdf'
            ]);
            return;
        }
        
        // If not found, and we have a ProofDocument value, check that path
        if (!empty($request->ProofDocument)) {
            $pdfPath = APPROOT . '/../public/uploads/documents/' . $request->ProofDocument;
            if (file_exists($pdfPath)) {
                echo json_encode([
                    'success' => true, 
                    'url' => URLROOT . '/uploads/documents/' . $request->ProofDocument
                ]);
                return;
            }
        }
        
        // If we get here, no document was found
        echo json_encode([
            'success' => false, 
            'message' => 'Proof document not found. Please contact the administrator.'
        ]);
    }



    public function single() {
        if (isset($this->stmt)) {
            $this->stmt->execute();
            return $this->stmt->fetch(PDO::FETCH_OBJ); // Ensure it returns a result
        } else {
            throw new Exception('No prepared statement found to execute.');
        }
    }


    /**
 * Display all donations made by the current donor
 */
public function myDonations() {
    // Get the donor ID from the session
    $donorId = $_SESSION['donor_id'];
    
    // Get page number for pagination (optional)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $perPage = 10; // Number of donations per page
    $offset = ($page - 1) * $perPage;
    
    // Get donations for this donor with pagination
    $donations = $this->donationModel->getDonationsByDonor($donorId, $perPage, $offset);
    
    // Count total donations for pagination
    $totalDonations = $this->donationModel->countDonationsByDonor($donorId);
    $totalPages = ceil($totalDonations / $perPage);
    
    // Prepare data for the view
    $data = [
        'donations' => $donations,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalDonations' => $totalDonations
    ];
    
    // Load the myDonations view
    $this->view('donors/myDonations', $data);
}

/**
 * Display details of a specific donation
 * @param int $donationId The donation ID
 */
public function donationDetails($donationId = null) {
    // Check if donation ID is provided
    if ($donationId === null) {
        flash('donation_error', 'Invalid donation', 'alert alert-danger');
        redirect('donations/myDonations');
        return;
    }
    
    // Get donation details
    $donation = $this->donationModel->getDonationById($donationId);
    
    // Check if donation exists and belongs to the current donor
    if (!$donation || $donation->DonorID != $_SESSION['donor_id']) {
        flash('donation_error', 'Donation not found or access denied', 'alert alert-danger');
        redirect('donations/myDonations');
        return;
    }
    
    // Get additional details based on donation type
    $data = [
        'donation' => $donation,
        'request' => $this->requestModel->getRequestById($donation->RequestID)
    ];
    
    // Add type-specific details
    if ($donation->DonationType == 'NonMonetary') {
        $data['itemDetails'] = $this->donationModel->getNonMonetaryItemDetails($donation->RequestID);
        $data['scheduleDetails'] = $this->donationModel->getNonMonetaryDonationDetails($donationId);
    }
    
    // Load the donationDetails view
    $this->view('donors/donationDetails', $data);
}

/**
 * Cancel a pending donation
 */
public function cancelDonation() {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('donations/myDonations');
        return;
    }
    
    // Sanitize POST data
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Get donation ID and reason
    $donationId = $_POST['donation_id'] ?? null;
    $cancelReason = $_POST['cancel_reason'] ?? '';
    $otherReason = $_POST['other_reason'] ?? '';
    $redirectPage = $_POST['redirect_page'] ?? 'myDonations';
    
    // Validate inputs
    if (!$donationId || empty($cancelReason)) {
        flash('donation_error', 'Invalid request data', 'alert alert-danger');
        redirect('donations/' . $redirectPage);
        return;
    }
    
    // If "Other" is selected, use the other reason text
    if ($cancelReason === 'Other' && !empty($otherReason)) {
        $cancelReason = $otherReason;
    }
    
    // Get donation
    $donation = $this->donationModel->getDonationById($donationId);
    
    // Check if donation exists, belongs to the current donor, and is pending
    if (!$donation || $donation->DonorID != $_SESSION['donor_id'] || $donation->Status !== 'Pending') {
        flash('donation_error', 'Cannot cancel this donation', 'alert alert-danger');
        redirect('donations/' . $redirectPage);
        return;
    }
    
    // Process cancellation
    if ($this->donationModel->cancelDonation($donationId, $cancelReason)) {
        flash('donation_message', 'Your donation has been cancelled successfully', 'alert alert-success');
    } else {
        flash('donation_error', 'Failed to cancel donation. Please try again.', 'alert alert-danger');
    }
    
    // Handle redirection based on the page
    if ($redirectPage === 'donationDetails') {
        // For donation details page, redirect to myDonations
        redirect('donations/myDonations');
    } else {
        // For other pages, redirect back to the same page
        redirect('donations/' . $redirectPage);
    }
}

/**
 * Display pending non-monetary donations with scheduling details
 * 
 * @param int $page Optional page number for pagination
 * @return void
 */
public function pendingDonations($page = 1) {
    // Validate page number
    $page = (int)$page;
    if ($page < 1) $page = 1;
    
    // Items per page for pagination
    $perPage = 6;
    $offset = ($page - 1) * $perPage;
    
    // Get donor ID from session
    $donorId = $_SESSION['donor_id'];
    
    // Get pending non-monetary donations for this donor
    $pendingDonations = $this->donationModel->getPendingNonMonetaryDonations($donorId, $perPage, $offset);
    
    // Enhance donations with drop-off details
    foreach ($pendingDonations as &$donation) {
        // Get scheduling details
        $scheduleDetails = $this->donationModel->getNonMonetaryDonationDetails($donation->DonationID);
        
        if ($scheduleDetails) {
            // Add scheduling info to donation object
            $donation->DropOffDate = $scheduleDetails->DropOffDate;
            $donation->DropOffTime = $scheduleDetails->DropOffTime;
        }
        
        // Get item details from request
        $itemDetails = $this->donationModel->getNonMonetaryItemDetails($donation->RequestID);
        if ($itemDetails) {
            $donation->ItemName = $itemDetails->ItemName;
            $donation->DropOffLocation = $itemDetails->DropOffLocation;
            $donation->Province = $itemDetails->Province;
        }
    }
    
    // Count total pending donations for pagination
    $totalDonations = $this->donationModel->countPendingNonMonetaryDonations($donorId);
    $totalPages = ceil($totalDonations / $perPage);
    
    // Prepare data for view
    $data = [
        'pendingDonations' => $pendingDonations,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalDonations' => $totalDonations
    ];
    
    // Load the view
    $this->view('donors/pendingDonations', $data);
}



}