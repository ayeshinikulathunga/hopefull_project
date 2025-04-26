<?php
class Donations extends Controller {
    private $donationRequestModel;
    private $donationModel;
    private $userModel;
    private $requestModel;
    private $db; // Add the $db property
    
    public function __construct() {
        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        
        $this->donationRequestModel = $this->model('DonationRequest');
        $this->donationModel = $this->model('Donation'); // Add this line
        $this->userModel = $this->model('User');
        $this->requestModel = $this->model('DonationRequest');
        
        $this->db = new Database(); // Initialize the $db property with a Database instance
        $this->db = new Database(); // Ensure the Database class is properly included and initialized
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
    

    /*public function donate($requestId = null) {
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
    }*/

    /**
 * Make a donation to a request
 * @param string $requestId The request ID
 */
/*public function donate($requestId = null) {
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
            
            // If bank transfer payment method, validate payment slip
            $bankPaymentUploaded = false;
            $slipFileName = '';
            if ($data['payment_method'] == 'bank') {
                if (!isset($_FILES['bank_slip']) || $_FILES['bank_slip']['error'] != 0) {
                    $data['payment_method_err'] = 'Please upload your bank transfer slip';
                } else {
                    // File validation logic
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                    $fileType = $_FILES['bank_slip']['type'];
                    $fileSize = $_FILES['bank_slip']['size'];
                    $maxSize = 5 * 1024 * 1024; // 5MB max size
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        $data['payment_method_err'] = 'Only JPEG, PNG, GIF images and PDF files are allowed';
                    } elseif ($fileSize > $maxSize) {
                        $data['payment_method_err'] = 'File size must be less than 5MB';
                    } else {
                        $bankPaymentUploaded = true;
                    }
                }
            }
            
            // If no errors, process monetary donation
            if (empty($data['amount_err']) && empty($data['payment_method_err'])) {
                // Begin transaction
                $this->db->beginTransaction();
                
                try {
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
                    
                    // PayHere payments should be marked as Pending until confirmed
                    $initialStatus = ($data['payment_method'] == 'payhere') ? 'Pending' : 'Completed';
                    
                    // Generate DonationID (Format: DON + 5 random digits)
                    $donationId = 'DON' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                    
                    // Insert donation record with appropriate status
                    $this->db->query('INSERT INTO donations (DonationID, RequestID, DonorID, DonationType, Amount, QuantityDonated, IsAnonymous, Status) 
                                    VALUES (:donationId, :requestId, :donorId, :donationType, :amount, :quantity, :isAnonymous, :status)');
                    
                    $this->db->bind(':donationId', $donationId);
                    $this->db->bind(':requestId', $donationData['requestId']);
                    $this->db->bind(':donorId', $donationData['donorId']);
                    $this->db->bind(':donationType', $donationData['donationType']);
                    $this->db->bind(':amount', $donationData['amount']);
                    $this->db->bind(':quantity', $donationData['quantity']);
                    $this->db->bind(':isAnonymous', $donationData['isAnonymous']);
                    $this->db->bind(':status', $initialStatus);
                    
                    $donationResult = $this->db->execute();
                    
                    // Process based on payment method
                    if ($data['payment_method'] == 'payhere') {
                        // Create payment record for PayHere
                        $paymentData = [
                            'donation_id' => $donationId,
                            'payment_amount' => $data['amount'],
                            'payment_method' => 'payhere',
                            'status' => 'Pending',
                            'payment_details' => json_encode([
                                'user_id' => $_SESSION['user_id'],
                                'donor_id' => $_SESSION['donor_id'],
                                'request_id' => $data['requestId']
                            ])
                        ];
                        
                        $paymentId = $this->donationModel->createDonationPaymentRecord($paymentData);
                        
                        if (!$paymentId) {
                            throw new Exception("Failed to create payment record");
                        }
                        
                        // Store the donation ID in session for later use
                        $_SESSION['payhere_donation_id'] = $donationId;
                        $_SESSION['payhere_payment_id'] = $paymentId;
                        
                        // Commit transaction
                        $this->db->commit();
                        
                        // Redirect to PayHere processing
                        redirect('donations/processPayHere/' . $donationId);
                        return;
                        
                    } else if ($data['payment_method'] == 'bank') {
                        // Handle bank slip upload
                        if ($bankPaymentUploaded) {
                            // Get file extension
                            $fileExt = pathinfo($_FILES['bank_slip']['name'], PATHINFO_EXTENSION);
                            
                            // Create new filename based on donation ID
                            $newFileName = $donationId . '.' . $fileExt;
                            
                            // Set upload directory
                            $slipsDir = UPLOADS_PATH . '/donation_slips';
                            
                            // Create directory if it doesn't exist
                            if (!file_exists($slipsDir)) {
                                mkdir($slipsDir, 0755, true);
                            }
                            
                            // Set file destination
                            $destination = $slipsDir . '/' . $newFileName;
                            
                            // Upload file
                            if (move_uploaded_file($_FILES['bank_slip']['tmp_name'], $destination)) {
                                // Create bank payment record
                                $donationBankPaymentModel = $this->model('DonationBankPayment');
                                $bankPaymentData = [
                                    'donation_id' => $donationId,
                                    'slip_file' => $newFileName
                                ];
                                
                                if (!$donationBankPaymentModel->createBankPayment($bankPaymentData)) {
                                    throw new Exception("Failed to create bank payment record");
                                }
                                
                                // Set the donation status to Pending
                                $this->db->query('UPDATE donations SET Status = "Pending" WHERE DonationID = :donationId'); // Ensure $this->db is properly initialized
                                $this->db->bind(':donationId', $donationId);
                                $this->db->execute();
                                
                            } else {
                                throw new Exception("Failed to upload bank slip");
                            }
                        } else {
                            throw new Exception("Bank slip upload is required");
                        }
                        
                        // Update request status to InProgress
                        $this->db->query('UPDATE donation_requests 
                                         SET RequestStatus = "InProgress" 
                                         WHERE RequestID = :requestId AND RequestStatus = "Pending"');
                        $this->db->bind(':requestId', $requestId);
                        $this->db->execute();
                        
                        // Commit transaction
                        $this->db->commit();
                        
                    } else {
                        // Direct completion for other payment methods
                        
                        // Update monetary donation details
                        $this->db->query('UPDATE monetary_donation_details 
                                         SET CurrentAmount = CurrentAmount + :amount 
                                         WHERE RequestID = :requestId');
                        $this->db->bind(':amount', $data['amount']);
                        $this->db->bind(':requestId', $requestId);
                        $updateResult = $this->db->execute();
                        
                        // Update donor statistics
                        $this->db->query('UPDATE donors 
                                         SET TotalDonations = TotalDonations + :amount, DonationCount = DonationCount + 1 
                                         WHERE DonorID = :donorId');
                        $this->db->bind(':amount', $data['amount']);
                        $this->db->bind(':donorId', $_SESSION['donor_id']);
                        $donorUpdateResult = $this->db->execute();
                        
                        // Check monetary request completion
                        $this->donationModel->checkMonetaryRequestCompletion($requestId);
                        
                        // Commit transaction
                        $this->db->commit();
                    }
                    
                    // Get the created donation for confirmation page
                    $donation = $this->donationModel->getDonationById($donationId);
                    
                    // Prepare data for confirmation page
                    $confirmData = [
                        'donation' => $donation,
                        'request' => $request,
                        'payment_method' => $data['payment_method']
                    ];
                    
                    // Go to confirmation page
                    $this->view('donors/donation-confirmation', $confirmData);
                    return;
                    
                } catch (Exception $e) {
                    // Roll back transaction on error
                    $this->db->rollBack();
                    error_log("Donation Error: " . $e->getMessage());
                    flash('donation_error', 'Something went wrong. Please try again.', 'alert alert-danger');
                    
                    // Re-display the form with error
                    $this->view('donors/monetary-donation-form', $data);
                    return;
                }
            }
            
        } else {
            // Non-monetary donation processing (unchanged)
            // Your existing non-monetary donation logic here
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
}*/
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
            
            // If bank transfer payment method, validate payment slip
            $bankPaymentUploaded = false;
            $slipFileName = '';
            if ($data['payment_method'] == 'bank') {
                if (!isset($_FILES['bank_slip']) || $_FILES['bank_slip']['error'] != 0) {
                    $data['payment_method_err'] = 'Please upload your bank transfer slip';
                } else {
                    // File validation logic
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                    $fileType = $_FILES['bank_slip']['type'];
                    $fileSize = $_FILES['bank_slip']['size'];
                    $maxSize = 5 * 1024 * 1024; // 5MB max size
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        $data['payment_method_err'] = 'Only JPEG, PNG, GIF images and PDF files are allowed';
                    } elseif ($fileSize > $maxSize) {
                        $data['payment_method_err'] = 'File size must be less than 5MB';
                    } else {
                        $bankPaymentUploaded = true;
                    }
                }
            }
            
            // If no errors, process monetary donation
            if (empty($data['amount_err']) && empty($data['payment_method_err'])) {
                // Begin transaction
                $this->db->beginTransaction();
                
                try {
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
                    
                    // PayHere payments should be marked as Pending until confirmed
                    $initialStatus = ($data['payment_method'] == 'payhere') ? 'Pending' : 'Completed';
                    
                    // Generate DonationID (Format: DON + 5 random digits)
                    $donationId = 'DON' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                    
                    // Insert donation record with appropriate status
                    $this->db->query('INSERT INTO donations (DonationID, RequestID, DonorID, DonationType, Amount, QuantityDonated, IsAnonymous, Status) 
                                    VALUES (:donationId, :requestId, :donorId, :donationType, :amount, :quantity, :isAnonymous, :status)');
                    
                    $this->db->bind(':donationId', $donationId);
                    $this->db->bind(':requestId', $donationData['requestId']);
                    $this->db->bind(':donorId', $donationData['donorId']);
                    $this->db->bind(':donationType', $donationData['donationType']);
                    $this->db->bind(':amount', $donationData['amount']);
                    $this->db->bind(':quantity', $donationData['quantity']);
                    $this->db->bind(':isAnonymous', $donationData['isAnonymous']);
                    $this->db->bind(':status', $initialStatus);
                    
                    $donationResult = $this->db->execute();
                    
                    // Process based on payment method
                    if ($data['payment_method'] == 'payhere') {
                        // Create payment record for PayHere
                        $paymentData = [
                            'donation_id' => $donationId,
                            'payment_amount' => $data['amount'],
                            'payment_method' => 'payhere',
                            'status' => 'Pending',
                            'payment_details' => json_encode([
                                'user_id' => $_SESSION['user_id'],
                                'donor_id' => $_SESSION['donor_id'],
                                'request_id' => $data['requestId']
                            ])
                        ];
                        
                        $paymentId = $this->donationModel->createDonationPaymentRecord($paymentData);
                        
                        if (!$paymentId) {
                            throw new Exception("Failed to create payment record");
                        }
                        
                        // Store the donation ID in session for later use
                        $_SESSION['payhere_donation_id'] = $donationId;
                        $_SESSION['payhere_payment_id'] = $paymentId;
                        
                        // Commit transaction
                        $this->db->commit();
                        
                        // Redirect to PayHere processing
                        redirect('donations/processPayHere/' . $donationId);
                        return;
                        
                    } else if ($data['payment_method'] == 'bank') {
                        // Handle bank slip upload
                        if ($bankPaymentUploaded) {
                            // Get file extension
                            $fileExt = pathinfo($_FILES['bank_slip']['name'], PATHINFO_EXTENSION);
                            
                            // Create new filename based on donation ID
                            $newFileName = $donationId . '.' . $fileExt;
                            
                            // Set upload directory
                            $slipsDir = UPLOADS_PATH . '/donation_slips';
                            
                            // Create directory if it doesn't exist
                            if (!file_exists($slipsDir)) {
                                mkdir($slipsDir, 0755, true);
                            }
                            
                            // Set file destination
                            $destination = $slipsDir . '/' . $newFileName;
                            
                            // Upload file
                            if (move_uploaded_file($_FILES['bank_slip']['tmp_name'], $destination)) {
                                // Create bank payment record
                                $donationBankPaymentModel = $this->model('DonationBankPayment');
                                $bankPaymentData = [
                                    'donation_id' => $donationId,
                                    'slip_file' => $newFileName
                                ];
                                
                                if (!$donationBankPaymentModel->createBankPayment($bankPaymentData)) {
                                    throw new Exception("Failed to create bank payment record");
                                }
                                
                                // Set the donation status to Pending
                                $this->db->query('UPDATE donations SET Status = "Pending" WHERE DonationID = :donationId'); // Ensure $this->db is properly initialized
                                $this->db->bind(':donationId', $donationId);
                                $this->db->execute();
                                
                            } else {
                                throw new Exception("Failed to upload bank slip");
                            }
                        } else {
                            throw new Exception("Bank slip upload is required");
                        }
                        
                        // Update request status to InProgress
                        $this->db->query('UPDATE donation_requests 
                                         SET RequestStatus = "InProgress" 
                                         WHERE RequestID = :requestId AND RequestStatus = "Pending"');
                        $this->db->bind(':requestId', $requestId);
                        $this->db->execute();
                        
                        // Commit transaction
                        $this->db->commit();
                        
                    } else {
                        // Direct completion for other payment methods
                        
                        // Update monetary donation details
                        $this->db->query('UPDATE monetary_donation_details 
                                         SET CurrentAmount = CurrentAmount + :amount 
                                         WHERE RequestID = :requestId');
                        $this->db->bind(':amount', $data['amount']);
                        $this->db->bind(':requestId', $requestId);
                        $updateResult = $this->db->execute();
                        
                        // Update donor statistics
                        $this->db->query('UPDATE donors 
                                         SET TotalDonations = TotalDonations + :amount, DonationCount = DonationCount + 1 
                                         WHERE DonorID = :donorId');
                        $this->db->bind(':amount', $data['amount']);
                        $this->db->bind(':donorId', $_SESSION['donor_id']);
                        $donorUpdateResult = $this->db->execute();
                        
                        // Check monetary request completion
                        $this->donationModel->checkMonetaryRequestCompletion($requestId);
                        
                        // Commit transaction
                        $this->db->commit();
                    }
                    
                    // Get the created donation for confirmation page
                    $donation = $this->donationModel->getDonationById($donationId);
                    
                    // Prepare data for confirmation page
                    $confirmData = [
                        'donation' => $donation,
                        'request' => $request,
                        'payment_method' => $data['payment_method']
                    ];
                    
                    // Go to confirmation page
                    $this->view('donors/donation-confirmation', $confirmData);
                    return;
                    
                } catch (Exception $e) {
                    // Roll back transaction on error
                    $this->db->rollBack();
                    error_log("Donation Error: " . $e->getMessage());
                    flash('donation_error', 'Something went wrong. Please try again.', 'alert alert-danger');
                    
                    // Re-display the form with error
                    $this->view('donors/monetary-donation-form', $data);
                    return;
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
        redirect('donations/myDonations' . $redirectPage);
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


/**
 * Process PayHere payment for a monetary donation
 * @param string $donationId The donation ID
 */
public function processPayHere($donationId) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        redirect('users/login');
    }
    
    // Get donation details
    $donation = $this->donationModel->getDonationById($donationId);
    
    if (!$donation || $donation->DonorID != $_SESSION['donor_id']) {
        flash('donation_error', 'Invalid donation', 'alert alert-danger');
        redirect('donors/dashboard');
    }
    
    // Get user information
    $user = $this->userModel->getUserById($_SESSION['user_id']);
    
    // Get request details
    $request = $this->requestModel->getRequestById($donation->RequestID);
    
    // Prepare data for PayHere
    $data = [
        'title' => 'Processing Donation Payment',
        'donation' => $donation,
        'request' => $request,
        'user' => $user,
        'merchant_id' => DONATION_PAYHERE_MERCHANT_ID,
        'return_url' => DONATION_PAYHERE_RETURN_URL,
        'cancel_url' => DONATION_PAYHERE_CANCEL_URL,
        'notify_url' => DONATION_PAYHERE_NOTIFY_URL,
        'sandbox' => DONATION_PAYHERE_SANDBOX
    ];
    
    $this->view('donors/process_payhere', $data);
}

/**
 * Handle PayHere payment success callback
 * @param string $donationId The donation ID (optional)
 */
public function paymentSuccess($donationId = null) {
    // Log information for debugging
    error_log('Donation PayHere payment success callback triggered.');
    error_log('PayHere success $_SESSION: ' . json_encode($_SESSION));
    
    // Check if donation ID is provided via URL parameter
    if (!$donationId && isset($_SESSION['payhere_donation_id'])) {
        // Fall back to session variable if URL parameter is not provided
        $donationId = $_SESSION['payhere_donation_id'];
    }
    
    // Redirect to dashboard if no donation ID is available
    if (!$donationId) {
        flash('donation_error', 'Donation information is missing', 'alert alert-danger');
        redirect('donors/dashboard');
    }
    
    // Begin transaction
    $this->db->beginTransaction();
    
    try {
        // Get donation details first
        $this->db->query('SELECT * FROM donations WHERE DonationID = :donationId');
        $this->db->bind(':donationId', $donationId);
        $donation = $this->db->single();
        
        if (!$donation) {
            throw new Exception("Donation not found: " . $donationId);
        }
        
        // Only process if donation is not already completed
        if ($donation->Status != 'Completed') {
            // 1. Update donation status
            $this->db->query('UPDATE donations SET Status = "Completed" WHERE DonationID = :donationId');
            $this->db->bind(':donationId', $donationId);
            $donationUpdateResult = $this->db->execute();
            
            if (!$donationUpdateResult) {
                throw new Exception("Failed to update donation status");
            }
            
            // 2. Update monetary donation details (if it's a monetary donation)
            if ($donation->DonationType == 'Monetary' && $donation->Amount > 0) {
                $this->db->query('UPDATE monetary_donation_details 
                                SET CurrentAmount = CurrentAmount + :amount 
                                WHERE RequestID = :requestId');
                $this->db->bind(':amount', $donation->Amount);
                $this->db->bind(':requestId', $donation->RequestID);
                $monetaryUpdateResult = $this->db->execute();
                
                if (!$monetaryUpdateResult) {
                    throw new Exception("Failed to update monetary donation details");
                }
                
                // 3. Update donor statistics
                $this->db->query('UPDATE donors 
                                SET TotalDonations = TotalDonations + :amount, 
                                    DonationCount = DonationCount + 1 
                                WHERE DonorID = :donorId');
                $this->db->bind(':amount', $donation->Amount);
                $this->db->bind(':donorId', $donation->DonorID);
                $donorUpdateResult = $this->db->execute();
                
                if (!$donorUpdateResult) {
                    throw new Exception("Failed to update donor statistics");
                }
                
                // 4. Check if request is complete
                $this->donationModel->checkMonetaryRequestCompletion($donation->RequestID);
            }
        }
        
        // 5. Get the payment record for this donation
        $payment = $this->donationModel->getPaymentByDonationId($donationId);
        
        // 6. If payment record exists, update its status
        if ($payment && $payment->Status != 'Completed') {
            // Update payment status to Completed
            $this->donationModel->updateDonationPaymentStatus(
                $payment->ID,
                'Completed',
                null,
                'Updated via return URL'
            );
            error_log("Updated payment ID {$payment->ID} to Completed");
        } else {
            error_log("No payment record found for donation: {$donationId} or payment already completed.");
        }
        
        // Commit the transaction
        $this->db->commit();
        
        // Clear the PayHere session variables if they exist
        if (isset($_SESSION['payhere_donation_id'])) {
            unset($_SESSION['payhere_donation_id']);
        }
        if (isset($_SESSION['payhere_payment_id'])) {
            unset($_SESSION['payhere_payment_id']);
        }
        
        // Flash success message and redirect to donation confirmation
        flash('donation_message', 'Payment successful! Your donation has been processed.', 'alert alert-success');
        redirect('donors/donation-confirmation/' . $donationId);
    } catch (Exception $e) {
        // If any error occurs, roll back the transaction
        $this->db->rollBack();
        error_log("Payment Success Error: " . $e->getMessage());
        flash('donation_error', 'There was an error processing your payment.', 'alert alert-danger');
        redirect('donors/dashboard');
    }
}

/**
 * Handle PayHere payment cancellation
 * @param string $donationId The donation ID (optional)
 */
public function paymentCancelled($donationId = null) {
    // Log information for debugging
    error_log('Donation PayHere payment cancelled.');
    
    // Check if donation ID is provided via URL parameter
    if (!$donationId && isset($_SESSION['payhere_donation_id'])) {
        // Fall back to session variable if URL parameter is not provided
        $donationId = $_SESSION['payhere_donation_id'];
    }
    
    // Redirect to dashboard if no donation ID is available
    if (!$donationId) {
        flash('donation_error', 'Donation information is missing', 'alert alert-danger');
        redirect('donors/dashboard');
    }
    
    // Clear the PayHere session variables
    if (isset($_SESSION['payhere_donation_id'])) {
        unset($_SESSION['payhere_donation_id']);
    }
    if (isset($_SESSION['payhere_payment_id'])) {
        unset($_SESSION['payhere_payment_id']);
    }
    
    // Flash message and redirect to donation details
    flash('donation_message', 'Payment was cancelled. You can try again later.', 'alert alert-warning');
    redirect('donations/donationDetails/' . $donationId);
}

/**
 * Handle PayHere payment notification (server-to-server)
 */
public function paymentNotify() {
    // This endpoint will receive server-to-server notifications from PayHere
    error_log('Donation PayHere payment notification callback triggered. Data: ' . json_encode($_POST));
    
    // Get the POST data
    $data = $_POST;
    
    // Verify the payment
    if (isset($data['merchant_id']) && $data['merchant_id'] == DONATION_PAYHERE_MERCHANT_ID) {
        // Extract donation ID from the order_id field
        $donationId = $data['order_id'] ?? null;
        
        if ($donationId) {
            // Begin transaction
            $this->db->beginTransaction();
            
            try {
                // Get donation information
                $this->db->query('SELECT * FROM donations WHERE DonationID = :donationId');
                $this->db->bind(':donationId', $donationId);
                $donation = $this->db->single();
                
                if (!$donation) {
                    throw new Exception("Donation not found: " . $donationId);
                }
                
                // Verify the md5sig for security
                $merchant_id = $data['merchant_id'];
                $order_id = $data['order_id'];
                $payhere_amount = $data['payhere_amount'];
                $payhere_currency = $data['payhere_currency'];
                $status_code = $data['status_code'];
                $md5sig = $data['md5sig'];
                
                $local_md5sig = strtoupper(
                    md5(
                        $merchant_id .
                        $order_id .
                        $payhere_amount .
                        $payhere_currency .
                        $status_code .
                        strtoupper(md5(DONATION_PAYHERE_MERCHANT_SECRET))
                    )
                );
                
                // Only process if signatures match
                if ($local_md5sig === $md5sig) {
                    // Verify the payment status
                    if ($data['status_code'] == '2') { // 2 = Success
                        // Get the payment record
                        $payment = $this->donationModel->getPaymentByDonationId($donationId);
                        
                        if ($payment && $payment->Status != 'Completed') {
                            $this->donationModel->updateDonationPaymentStatus(
                                $payment->ID,
                                'Completed',
                                $data['payment_id'] ?? null,
                                json_encode($data)
                            );
                        }
                        
                        // Update donation status to Completed if it's not already
                        if ($donation->Status != 'Completed') {
                            // 1. Update donation status
                            $this->db->query('UPDATE donations SET Status = "Completed" WHERE DonationID = :donationId');
                            $this->db->bind(':donationId', $donationId);
                            $this->db->execute();
                            
                            // 2. If it's a monetary donation, update monetary donation details
                            if ($donation->DonationType == 'Monetary' && $donation->Amount > 0) {
                                // Update monetary donation details
                                $this->db->query('UPDATE monetary_donation_details 
                                                SET CurrentAmount = CurrentAmount + :amount 
                                                WHERE RequestID = :requestId');
                                $this->db->bind(':amount', $donation->Amount);
                                $this->db->bind(':requestId', $donation->RequestID);
                                $this->db->execute();
                                
                                // 3. Update donor statistics
                                $this->db->query('UPDATE donors 
                                                SET TotalDonations = TotalDonations + :amount, 
                                                    DonationCount = DonationCount + 1 
                                                WHERE DonorID = :donorId');
                                $this->db->bind(':amount', $donation->Amount);
                                $this->db->bind(':donorId', $donation->DonorID);
                                $this->db->execute();
                                
                                // 4. Check if the request is complete
                                $this->donationModel->checkMonetaryRequestCompletion($donation->RequestID);
                            }
                        }
                        
                        // Commit the transaction
                        $this->db->commit();
                        
                        // Return success response
                        http_response_code(200);
                        echo 'Payment verified';
                    } else {
                        // Payment failed
                        // Update the payment record
                        $payment = $this->donationModel->getPaymentByDonationId($donationId);
                        
                        if ($payment) {
                            $this->donationModel->updateDonationPaymentStatus(
                                $payment->ID,
                                'Failed',
                                $data['payment_id'] ?? null,
                                json_encode($data)
                            );
                        }
                        
                        // Commit the transaction (even for failed payments we want to record the status)
                        $this->db->commit();
                        
                        // Return error response
                        http_response_code(400);
                        echo 'Payment failed';
                    }
                } else {
                    // MD5 signature verification failed
                    $this->db->rollBack();
                    http_response_code(403);
                    error_log("MD5 signature mismatch. Local: {$local_md5sig}, Received: {$md5sig}");
                    echo 'Invalid signature';
                }
            } catch (Exception $e) {
                // If any error occurs, roll back the transaction
                $this->db->rollBack();
                error_log("Payment Notify Error: " . $e->getMessage());
                http_response_code(500);
                echo 'Server error: ' . $e->getMessage();
            }
        } else {
            // Invalid data
            http_response_code(400);
            echo 'Invalid data';
        }
    } else {
        // Invalid merchant
        http_response_code(403);
        echo 'Invalid merchant';
    }
    
    exit;
}


}