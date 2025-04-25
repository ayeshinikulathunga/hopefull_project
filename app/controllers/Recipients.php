<?php
class Recipients extends Controller {
    private $userModel;
    private $recipientModel;
    private $feedbackModel;
    
    public function __construct() {
        // Check if user is logged in and is a recipient
        if (!isLoggedIn()) {
            redirect('users/login');
        }

        if($_SESSION['user_type'] !== 'Recipient') {
            redirect('users/login');
        }
        
        $this->userModel = $this->model('User');
        $this->recipientModel = $this->model('Recipient');
        $this->feedbackModel = $this->model('Feedback');
    }
    
    // Default method - redirects to dashboard
    public function index() {
        redirect('recipients/dashboard');
    }
    
    // Dashboard that shows recipient info and their requests
    public function dashboard() {
        // Get recipient data
        $recipient = $this->recipientModel->getRecipientById($_SESSION['recipient_id']);
        
        // Get donation requests made by this recipient
        $requests = $this->recipientModel->getRequestsByRecipient($_SESSION['recipient_id']);
        
        $data = [
            'title' => 'Recipient Dashboard',
            'recipient' => $recipient,
            'requests' => $requests
        ];
        
        $this->view('recipients/dashboard', $data);
    }
    
    // Display all requests by this recipient
    public function requests() {
        // Get recipient data
        $recipient = $this->recipientModel->getRecipientById($_SESSION['recipient_id']);
        
        // Get all donation requests made by this recipient
        $requests = $this->recipientModel->getRequestsByRecipient($_SESSION['recipient_id']);
        
        $data = [
            'title' => 'My Donation Requests',
            'recipient' => $recipient,
            'requests' => $requests
        ];
        
        $this->view('recipients/requests', $data);
    }
    
    // Create a new donation request

    public function createRequest() {
        // If form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Initialize data with all potential fields
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'recipientId' => $_SESSION['recipient_id'],
                'requestType' => trim($_POST['requestType'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'requestTitle' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'deadline' => trim($_POST['deadline'] ?? ''),
                
                // Add these missing fields
                'itemName' => trim($_POST['itemName'] ?? ''),
                'quantityNeeded' => isset($_POST['quantityNeeded']) ? (int)$_POST['quantityNeeded'] : '',
                'province' => trim($_POST['province'] ?? ''),
                'dropOffLocation' => trim($_POST['dropOffLocation'] ?? ''),
                'dropOffTime' => trim($_POST['dropOffTime'] ?? ''),
                'targetAmount' => isset($_POST['targetAmount']) ? filter_var($_POST['targetAmount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '',
                
                // Error fields
                'title_err' => '',
                'description_err' => '',
                'deadline_err' => '',
                'category_err' => '',
                'requestType_err' => '',
                'itemName_err' => '',
                'quantityNeeded_err' => '',
                'province_err' => '',
                'dropOffLocation_err' => '',
                'dropOffTime_err' => '',
                'targetAmount_err' => '',
                'requestImage_err' => '',
                'proofDocument_err' => ''
            ];
            
            // Validate request fields
            $validationResult = $this->validateRequestFields($data);
            
            // File upload variables
            $requestImage = '';
            $proofDocument = '';
            $uploadErrors = false;
            
            // Handle file uploads
            try {
                // Request image upload
                if(isset($_FILES['requestImage']) && $_FILES['requestImage']['error'] === 0) {
                    $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    $uploadDir = APPROOT . '/../public/uploads/requests/';
                    
                    // Create directory if it doesn't exist
                    if(!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    // Check file type
                    $fileType = $_FILES['requestImage']['type'];
                    
                    if(!in_array($fileType, $allowedImageTypes)) {
                        $data['requestImage_err'] = 'Only JPG and PNG images are allowed';
                        $uploadErrors = true;
                    } else {
                        $requestImage = 'temp_' . time() . '_' . $_FILES['requestImage']['name'];
                    }
                } else {
                    $data['requestImage_err'] = 'Please upload a request image';
                    $uploadErrors = true;
                }
                
                // Proof document upload
                if(isset($_FILES['proofDocument']) && $_FILES['proofDocument']['error'] === 0) {
                    $allowedDocTypes = ['application/pdf'];
                    $uploadDir = APPROOT . '/../public/uploads/documents/';
                    
                    // Create directory if it doesn't exist
                    if(!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    // Check file type
                    $fileType = $_FILES['proofDocument']['type'];
                    
                    if(!in_array($fileType, $allowedDocTypes)) {
                        $data['proofDocument_err'] = 'Only PDF documents are allowed';
                        $uploadErrors = true;
                    } else {
                        $proofDocument = 'temp_' . time() . '_' . $_FILES['proofDocument']['name'];
                    }
                } else {
                    $data['proofDocument_err'] = 'Please upload a proof document';
                    $uploadErrors = true;
                }
            } catch (Exception $e) {
                error_log('File upload error: ' . $e->getMessage());
                $uploadErrors = true;
            }
            
            // Combine validation checks
            $isValid = 
                empty($validationResult['title_err']) && 
                empty($validationResult['description_err']) && 
                empty($validationResult['deadline_err']) && 
                empty($validationResult['category_err']) && 
                empty($validationResult['requestType_err']) && 
                !$uploadErrors;
            
            // Additional type-specific validation
            if ($data['requestType'] == 'Monetary') {
                $isValid = $isValid && empty($validationResult['targetAmount_err']);
            } else {
                $isValid = $isValid && 
                    empty($validationResult['itemName_err']) && 
                    empty($validationResult['quantityNeeded_err']) && 
                    empty($validationResult['province_err']) && 
                    empty($validationResult['dropOffLocation_err']) && 
                    empty($validationResult['dropOffTime_err']);
            }
            
            // Merge validation results
            $data = array_merge($data, $validationResult);
            
            // If all validations pass, create request
            if($isValid) {
                try {
                    // First create the request record
                    $requestId = $this->recipientModel->createRequest($data);
                    
                    if($requestId) {
                        // Handle file uploads with proper names
                        $imageSuccess = false;
                        $docSuccess = false;
                        
                        // Upload request image
                        $imageTarget = APPROOT . '/../public/uploads/requests/' . $requestId . '.jpg';
                        if(move_uploaded_file($_FILES['requestImage']['tmp_name'], $imageTarget)) {
                            $imageSuccess = true;
                        }
                        
                        // Upload proof document
                        $docTarget = APPROOT . '/../public/uploads/documents/' . $requestId . '.pdf';
                        if(move_uploaded_file($_FILES['proofDocument']['tmp_name'], $docTarget)) {
                            $docSuccess = true;
                        }
                        
                        // Display appropriate message
                        if($imageSuccess && $docSuccess) {
                            flash('request_message', 'Donation request created successfully');
                        } else {
                            flash('request_message', 'Request created, but there were issues with file uploads', 'alert alert-warning');
                        }
                        
                        redirect('recipients/requests');
                    } else {
                        flash('request_message', 'Something went wrong when creating your request', 'alert alert-danger');
                        $this->view('recipients/create_request', $data);
                    }
                } catch (Exception $e) {
                    error_log('Request creation error: ' . $e->getMessage());
                    flash('request_message', 'An unexpected error occurred', 'alert alert-danger');
                    $this->view('recipients/create_request', $data);
                }
            } else {
                // Load view with errors
                $this->view('recipients/create_request', $data);
            }
        } else {
            // First time loading the form
            $data = [
                'title' => 'Create Donation Request',
                'requestType' => '',
                'category' => '',
                'requestTitle' => '',
                'description' => '',
                'deadline' => '',
                'itemName' => '',
                'quantityNeeded' => '',
                'province' => '',
                'dropOffLocation' => '',
                'dropOffTime' => '',
                'targetAmount' => '',
                
                // Error fields
                'title_err' => '',
                'description_err' => '',
                'deadline_err' => '',
                'category_err' => '',
                'requestType_err' => '',
                'itemName_err' => '',
                'quantityNeeded_err' => '',
                'province_err' => '',
                'dropOffLocation_err' => '',
                'dropOffTime_err' => '',
                'targetAmount_err' => '',
                'requestImage_err' => '',
                'proofDocument_err' => ''
            ];
            
            $this->view('recipients/create_request', $data);
        }
    }
    
    // Supporting validation method
    private function validateRequestFields($data) {
        $errors = [
            'title_err' => '',
            'description_err' => '',
            'deadline_err' => '',
            'category_err' => '',
            'requestType_err' => '',
            'itemName_err' => '',
            'quantityNeeded_err' => '',
            'province_err' => '',
            'dropOffLocation_err' => '',
            'dropOffTime_err' => '',
            'targetAmount_err' => ''
        ];
    
        // Validate request type
        if(empty($data['requestType']) || !in_array($data['requestType'], ['Monetary', 'NonMonetary'])) {
            $errors['requestType_err'] = 'Please select a valid request type';
        }
    
        // Validate title
        if(empty($data['requestTitle'])) {
            $errors['title_err'] = 'Please enter a title';
        } elseif(strlen($data['requestTitle']) > 100) {
            $errors['title_err'] = 'Title cannot exceed 100 characters';
        }
    
        // Validate description
        if(empty($data['description'])) {
            $errors['description_err'] = 'Please enter a description';
        }
    
        // Validate category
        if(empty($data['category']) || !in_array($data['category'], ['Healthcare', 'Education', 'Community', 'Sports', 'MakeAWish'])) {
            $errors['category_err'] = 'Please select a valid category';
        }
    
        // Validate deadline
        if(empty($data['deadline'])) {
            $errors['deadline_err'] = 'Please select a deadline';
        } elseif(strtotime($data['deadline']) < strtotime('today')) {
            $errors['deadline_err'] = 'Deadline cannot be in the past';
        }
    
        // Validate type-specific fields
        if($data['requestType'] == 'Monetary') {
            // Validate target amount
            if(empty($data['targetAmount'])) {
                $errors['targetAmount_err'] = 'Please enter a target amount';
            } elseif($data['targetAmount'] <= 0) {
                $errors['targetAmount_err'] = 'Target amount must be greater than zero';
            }
        } else {
            // Validate non-monetary fields
            if(empty($data['itemName'])) {
                $errors['itemName_err'] = 'Please enter an item name';
            }
    
            if(empty($data['quantityNeeded']) || $data['quantityNeeded'] <= 0) {
                $errors['quantityNeeded_err'] = 'Please enter a valid quantity needed';
            }
    
            if(empty($data['province'])) {
                $errors['province_err'] = 'Please select a province';
            }
    
            if(empty($data['dropOffLocation'])) {
                $errors['dropOffLocation_err'] = 'Please enter a drop-off location';
            }
    
            if(empty($data['dropOffTime'])) {
                $errors['dropOffTime_err'] = 'Please enter a drop-off time';
            }
        }
    
        return $errors;
    }
    
    // View a single request with donations
    public function viewRequest($id = null) {
        // Check if ID is provided
        if(!$id) {
            flash('request_message', 'Invalid request', 'alert alert-danger');
            redirect('recipients/requests');
        }
        
        // Get request details
        $request = $this->recipientModel->getRequestDetails($id);
        
        // Check if request exists and belongs to this recipient
        if(!$request || $request->RecipientID != $_SESSION['recipient_id']) {
            flash('request_message', 'Request not found or access denied', 'alert alert-danger');
            redirect('recipients/requests');
        }
        
        // Get donations for this request
        $donations = $this->recipientModel->getDonationsByRequest($id);
        
        // Prepare data for view
        $data = [
            'requestTitle' => $request->Title,
            'request' => $request,
            'donations' => $donations
        ];
        
        $this->view('recipients/view_request', $data);
    }

   
    public function editRequest($id = null) {
        // Add detailed logging
        error_log("Edit Request Method Called with ID: " . print_r($id, true));
        
        // Ensure ID is not null
        if ($id === null) {
            error_log("Edit Request: No ID provided");
            flash('request_message', 'Invalid request ID', 'alert alert-danger');
            redirect('recipients/requests');
            return;
        }
        
        // Get request details
        $request = $this->recipientModel->getRequestDetails($id);
        
        // Check if request exists and belongs to this recipient
        if (!$request || $request->RecipientID != $_SESSION['recipient_id']) {
            error_log("Edit Request: Request not found or access denied");
            flash('request_message', 'Request not found or access denied', 'alert alert-danger');
            redirect('recipients/requests');
            return;
        }
        
        // Check if request can be edited (only Pending or Rejected requests)
        if ($request->RequestStatus != 'Pending' && $request->VerificationStatus != 'Rejected') {
            error_log("Edit Request: Request cannot be edited");
            flash('request_message', 'This request cannot be edited', 'alert alert-danger');
            redirect('recipients/viewRequest/' . $id);
            return;
        }
        
        // If form is submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Initialize data with form fields
            $data = [
                'title' => 'Edit Donation Request',
                'request' => $request,
                'requestId' => $id,
                'recipientId' => $_SESSION['recipient_id'],
                'requestType' => $request->RequestType, // Can't change request type during edit
                'category' => trim($_POST['category']),
                'requestTitle' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'deadline' => trim($_POST['deadline']),
                
                // Error fields
                'title_err' => '',
                'description_err' => '',
                'deadline_err' => '',
                'category_err' => ''
            ];
            
            // Add type-specific fields based on request type
            if ($request->RequestType == 'Monetary') {
                $data['targetAmount'] = filter_var($_POST['targetAmount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $data['targetAmount_err'] = '';
            } else {
                $data['itemName'] = trim($_POST['itemName']);
                $data['quantityNeeded'] = (int)$_POST['quantityNeeded'];
                $data['province'] = trim($_POST['province']);
                $data['dropOffLocation'] = trim($_POST['dropOffLocation']);
                $data['dropOffTime'] = trim($_POST['dropOffTime']);
                
                $data['itemName_err'] = '';
                $data['quantityNeeded_err'] = '';
                $data['province_err'] = '';
                $data['dropOffLocation_err'] = '';
                $data['dropOffTime_err'] = '';
            }
            
            // Validate inputs
            $isValid = true;
            
            // Validate title
            if (empty($data['requestTitle'])) {
                $data['title_err'] = 'Please enter a title';
                $isValid = false;
            } elseif (strlen($data['requestTitle']) > 100) {
                $data['title_err'] = 'Title cannot exceed 100 characters';
                $isValid = false;
            }
            
            // Validate description
            if (empty($data['description'])) {
                $data['description_err'] = 'Please enter a description';
                $isValid = false;
            }
            
            // Validate category
            if (empty($data['category']) || !in_array($data['category'], ['Healthcare', 'Education', 'Community', 'Sports', 'MakeAWish'])) {
                $data['category_err'] = 'Please select a valid category';
                $isValid = false;
            }
            
            // Validate deadline
            if (empty($data['deadline'])) {
                $data['deadline_err'] = 'Please select a deadline';
                $isValid = false;
            } elseif (strtotime($data['deadline']) < strtotime('today')) {
                $data['deadline_err'] = 'Deadline cannot be in the past';
                $isValid = false;
            }
            
            // Validate type-specific fields
            if ($request->RequestType == 'Monetary') {
                if (empty($data['targetAmount']) || $data['targetAmount'] <= 0) {
                    $data['targetAmount_err'] = 'Please enter a valid target amount';
                    $isValid = false;
                }
            } else {
                if (empty($data['itemName'])) {
                    $data['itemName_err'] = 'Please enter an item name';
                    $isValid = false;
                }
                
                if (empty($data['quantityNeeded']) || $data['quantityNeeded'] <= 0) {
                    $data['quantityNeeded_err'] = 'Please enter a valid quantity';
                    $isValid = false;
                }
                
                if (empty($data['province'])) {
                    $data['province_err'] = 'Please select a province';
                    $isValid = false;
                }
                
                if (empty($data['dropOffLocation'])) {
                    $data['dropOffLocation_err'] = 'Please enter a drop-off location';
                    $isValid = false;
                }
                
                if (empty($data['dropOffTime'])) {
                    $data['dropOffTime_err'] = 'Please enter a drop-off time';
                    $isValid = false;
                }
            }
            
            // Handle file uploads (optional)
            $uploadErrors = false;
            $requestImage = '';
            $proofDocument = '';
            
            // Check and upload request image if provided
            if (isset($_FILES['requestImage']) && $_FILES['requestImage']['error'] === 0) {
                $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                $fileType = $_FILES['requestImage']['type'];
                
                if (!in_array($fileType, $allowedImageTypes)) {
                    $data['requestImage_err'] = 'Only JPG and PNG images are allowed';
                    $uploadErrors = true;
                } else {
                    $requestImage = $id . '.jpg';
                    $imageTarget = APPROOT . '/../public/uploads/requests/' . $requestImage;
                    if (!move_uploaded_file($_FILES['requestImage']['tmp_name'], $imageTarget)) {
                        $uploadErrors = true;
                    }
                }
            }
            
            // Check and upload proof document if provided
            if (isset($_FILES['proofDocument']) && $_FILES['proofDocument']['error'] === 0) {
                $allowedDocTypes = ['application/pdf'];
                $fileType = $_FILES['proofDocument']['type'];
                
                if (!in_array($fileType, $allowedDocTypes)) {
                    $data['proofDocument_err'] = 'Only PDF documents are allowed';
                    $uploadErrors = true;
                } else {
                    $proofDocument = $id . '.pdf';
                    $docTarget = APPROOT . '/../public/uploads/documents/' . $proofDocument;
                    if (!move_uploaded_file($_FILES['proofDocument']['tmp_name'], $docTarget)) {
                        $uploadErrors = true;
                    }
                }
            }
            
            // If validation passes and no upload errors
            if ($isValid && !$uploadErrors) {
                // Prepare update data - Use requestTitle as the title field for the update
                $updateData = [
                    'requestId' => $data['requestId'],
                    'recipientId' => $data['recipientId'],
                    'requestType' => $data['requestType'],
                    'category' => $data['category'],
                    'title' => $data['requestTitle'], // Use requestTitle here, not title
                    'description' => $data['description'],
                    'deadline' => $data['deadline']
                ];
                
                // Add type-specific fields
                if ($data['requestType'] == 'Monetary') {
                    $updateData['targetAmount'] = $data['targetAmount'];
                } else {
                    $updateData['itemName'] = $data['itemName'];
                    $updateData['quantityNeeded'] = $data['quantityNeeded'];
                    $updateData['province'] = $data['province'];
                    $updateData['dropOffLocation'] = $data['dropOffLocation'];
                    $updateData['dropOffTime'] = $data['dropOffTime'];
                }
                
                // Add optional files
                $updateData['proofDocument'] = $proofDocument ? $proofDocument : null;
                
                // Attempt to update request
                $updateResult = $this->recipientModel->updateRequest($updateData);
                
                if ($updateResult) {
                    flash('request_message', 'Request updated successfully');
                    redirect('recipients/viewRequest/' . $id);
                } else {
                    flash('request_message', 'Failed to update request', 'alert alert-danger');
                    $this->view('recipients/edit_request', $data);
                }
            } else {
                // Reload view with errors
                $this->view('recipients/edit_request', $data);
            }
        } else {
            // First time loading the edit form
            $data = [
                'title' => 'Edit Donation Request',
                'request' => $request,
                'requestId' => $id,
                'requestType' => $request->RequestType,
                'category' => $request->Category,
                'requestTitle' => $request->Title, // This is the actual request title
                'description' => $request->Description,
                'deadline' => date('Y-m-d', strtotime($request->Deadline)),
                
                // Add type-specific details
                'targetAmount' => $request->RequestType == 'Monetary' ? $request->TargetAmount : '',
                'itemName' => $request->RequestType == 'NonMonetary' ? $request->ItemName : '',
                'quantityNeeded' => $request->RequestType == 'NonMonetary' ? $request->QuantityNeeded : '',
                'province' => $request->RequestType == 'NonMonetary' ? $request->Province : '',
                'dropOffLocation' => $request->RequestType == 'NonMonetary' ? $request->DropOffLocation : '',
                'dropOffTime' => $request->RequestType == 'NonMonetary' ? $request->DropOffTime : '',
                
                // Error fields
                'title_err' => '',
                'description_err' => '',
                'deadline_err' => '',
                'category_err' => '',
                'targetAmount_err' => '',
                'itemName_err' => '',
                'quantityNeeded_err' => '',
                'province_err' => '',
                'dropOffLocation_err' => '',
                'dropOffTime_err' => ''
            ];
            
            $this->view('recipients/edit_request', $data);
        }
    }

    
    // Delete a request
    public function deleteRequest($id = null) {
        // Check if ID is provided
        if(!$id) {
            flash('request_message', 'Invalid request', 'alert alert-danger');
            redirect('recipients/requests');
        }
        
        // Only process POST requests for security
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            redirect('recipients/viewRequest/' . $id);
        }
        
        // Attempt to delete the request
        if($this->recipientModel->deleteRequest($id, $_SESSION['recipient_id'])) {
            // Delete associated image and document files if they exist
            $imagePath = APPROOT . '/../public/uploads/requests/' . $id . '.jpg';
            if(file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            $docPath = APPROOT . '/../public/uploads/documents/' . $id . '.pdf';
            if(file_exists($docPath)) {
                unlink($docPath);
            }
            
            flash('request_message', 'Request deleted successfully');
        } else {
            flash('request_message', 'Unable to delete this request. It may have already received donations or been approved.', 'alert alert-danger');
        }
        
        redirect('recipients/requests');
    }
    
   
  
    
    // View donation statistics
    public function statistics() {
        // Get recipient data
        $recipient = $this->recipientModel->getRecipientById($_SESSION['recipient_id']);
        
        // Get donation statistics
        $stats = $this->recipientModel->getRecipientStatistics($_SESSION['recipient_id']);
        
        // Get monthly donation trends
        $monthlyTrends = $this->recipientModel->getMonthlyDonationTrends($_SESSION['recipient_id']);
        
        // Get category breakdown
        $categoryBreakdown = $this->recipientModel->getCategoryBreakdown($_SESSION['recipient_id']);
        
        $data = [
            'title' => 'Donation Statistics',
            'recipient' => $recipient,
            'stats' => $stats,
            'monthlyTrends' => $monthlyTrends,
            'categoryBreakdown' => $categoryBreakdown
        ];
        
        $this->view('recipients/statistics', $data);
    }
    
    // Send thank you messages
    public function thankDonors($requestId = null) {
        // Check if request ID is provided
        if(!$requestId) {
            flash('request_message', 'Invalid request', 'alert alert-danger');
            redirect('recipients/requests');
        }
        
        // Get request details
        $request = $this->recipientModel->getRequestDetails($requestId);
        
        // Check if request exists and belongs to this recipient
        if(!$request || $request->RecipientID != $_SESSION['recipient_id']) {
            flash('request_message', 'Request not found or access denied', 'alert alert-danger');
            redirect('recipients/requests');
        }
        
        // If form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            
            $message = trim($_POST['message']);
            $donors = isset($_POST['donors']) ? $_POST['donors'] : [];
            
            if(empty($message)) {
                flash('thank_message', 'Please enter a thank you message', 'alert alert-danger');
                redirect('recipients/thankDonors/' . $requestId);
            }
            
            if(empty($donors)) {
                flash('thank_message', 'Please select at least one donor to thank', 'alert alert-danger');
                redirect('recipients/thankDonors/' . $requestId);
            }
            
            // Send thank you messages
            $result = $this->recipientModel->sendThankYouMessages($requestId, $donors, $message);
            
            if($result) {
                flash('thank_message', 'Thank you messages sent successfully');
                redirect('recipients/viewRequest/' . $requestId);
            } else {
                flash('thank_message', 'Failed to send thank you messages', 'alert alert-danger');
                redirect('recipients/thankDonors/' . $requestId);
            }
        } else {
            // Get donations for this request
            $donations = $this->recipientModel->getDonationsByRequest($requestId);
            
            // Filter out anonymous donations
            $nonAnonymousDonations = [];
            foreach($donations as $donation) {
                if($donation->IsAnonymous == 0 && $donation->Status == 'Completed') {
                    $nonAnonymousDonations[] = $donation;
                }
            }
            
            $data = [
                'title' => 'Send Thank You Messages',
                'request' => $request,
                'donations' => $nonAnonymousDonations
            ];
            
            $this->view('recipients/thank_donors', $data);
        }
    }
    
        /**
 * Create a feedback report for a specific donation
 * @param string $requestId The request ID
 * @return void
 */
public function createDonorFeedback($requestId = null) {
    // Check if request ID is provided
    if(!$requestId) {
        flash('request_message', 'Invalid request', 'alert alert-danger');
        redirect('recipients/requests');
    }
    
    // Get request details
    $request = $this->recipientModel->getRequestDetails($requestId);
    
    // Check if request exists and belongs to this recipient
    if(!$request || $request->RecipientID != $_SESSION['recipient_id']) {
        flash('request_message', 'Request not found or access denied', 'alert alert-danger');
        redirect('recipients/requests');
    }
    
    // Get donations for this request
    $donations = $this->recipientModel->getDonationsByRequest($requestId);
    
    // Filter out anonymous donations, show only completed ones
    $nonAnonymousDonations = [];
    foreach($donations as $donation) {
        if($donation->IsAnonymous == 0 && $donation->Status == 'Completed') {
            $nonAnonymousDonations[] = $donation;
        }
    }
    
    // If form is submitted
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Validate form submission
        $selectedDonations = isset($_POST['donations']) ? $_POST['donations'] : [];
        
        if(empty($selectedDonations)) {
            flash('feedback_message', 'Please select at least one donation to send feedback', 'alert alert-danger');
            redirect('recipients/createDonorFeedback/' . $requestId);
        }
        
        // Process each selected donation
        $successCount = 0;
        $failureCount = 0;
        
        // Initialize Feedback model
        $feedbackModel = $this->model('Feedback');
        
        foreach($selectedDonations as $donationId) {
            // Prepare feedback data
            $feedbackData = [
                'donationId' => $donationId,
                'recipientId' => $_SESSION['recipient_id'],
                'feedbackType' => 'ImpactReport',
                'content' => trim($_POST['impact_message'][$donationId] ?? ''),
                'rating' => null // For impact reports, rating might not be applicable
            ];
            
            // Validate content
            if(empty($feedbackData['content'])) {
                $failureCount++;
                continue;
            }
            
            // Create feedback report
            $result = $feedbackModel->createFeedbackReport($feedbackData);
            
            if($result) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }
        
        // Set appropriate flash message
        if($successCount > 0) {
            flash('request_message', "Successfully sent impact feedback for {$successCount} donations" . 
                  ($failureCount > 0 ? " (Failed for {$failureCount} donations)" : ''));
        } else {
            flash('request_message', 'Failed to send impact feedback', 'alert alert-danger');
        }
        
        redirect('recipients/viewRequest/' . $requestId);
    } else {
        // Display form to send impact updates
        $data = [
            'title' => 'Send Donor Impact Feedback',
            'request' => $request,
            'donations' => $nonAnonymousDonations
        ];
        
        $this->view('recipients/create_donor_feedback', $data);
    }
}
  
/**
 * View feedback history
 * @return void
 */
public function feedbackHistory() {
    // Initialize Feedback model
    $feedbackModel = $this->model('Feedback');
    
    // Get all feedback created by this recipient
    $feedback = $feedbackModel->getFeedbackByRecipient($_SESSION['recipient_id']);
    
    $data = [
        'title' => 'Feedback History',
        'feedback' => $feedback
    ];
    
    $this->view('recipients/feedback_history', $data);
}

/**
 * Delete a feedback report
 * @param string $feedbackId The feedback ID
 * @return void
 */
public function deleteFeedback($feedbackId = null) {
    // Check if feedback ID is provided
    if(!$feedbackId) {
        flash('feedback_message', 'Invalid feedback', 'alert alert-danger');
        redirect('recipients/feedbackHistory');
    }
    
    // Only process POST requests for security
    if($_SERVER['REQUEST_METHOD'] != 'POST') {
        redirect('recipients/feedbackHistory');
    }
    
    // Attempt to delete the feedback report
    if($this->feedbackModel->deleteFeedback($feedbackId, $_SESSION['recipient_id'])) {
        flash('feedback_message', 'Feedback deleted successfully');
    } else {
        flash('feedback_message', 'Unable to delete this feedback', 'alert alert-danger');
    }
    
    redirect('recipients/feedbackHistory');
}

//get total donations for a recipient


}