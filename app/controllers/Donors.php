<?php 
class Donors extends Controller {
    private $db;
    private $donorModel;
    private $requestModel;
    private $donationModel;
    private $badgeModel;

    public function __construct() {
        $this->db = new Database;
        
        // Check if user is logged in and is a donor
        if(!isLoggedIn()) {
            redirect('users/login');
        } else if($_SESSION['user_type'] !== 'Donor') {
            redirect('users/login');
        }
        
        // Load models
        $this->donorModel = $this->model('Donor');
        $this->requestModel = $this->model('DonationRequest');
        $this->donationModel = $this->model('Donation');
        $this->badgeModel = $this->model('Badge');
    }
    
    // Add the index method
    public function index() {
        redirect('donors/dashboard');
    }
    
    // Add the dashboard method
    public function dashboard() {
        // Get donor ID from session
        $donorId = $_SESSION['donor_id'];

        // Get recent donation requests (limited to 3)
        $recentRequests = $this->requestModel->getRecentApprovedRequests(3);

        // Process requests to include additional data
        foreach($recentRequests as $request) {
            if($request->RequestType == 'Monetary') {
                $monetaryDetails = $this->requestModel->getMonetaryRequestDetails($request->RequestID);
                if($monetaryDetails) {
                    $request->TargetAmount = $monetaryDetails->TargetAmount;
                    $request->CurrentAmount = $monetaryDetails->CurrentAmount;
                }
            } else {
                $nonMonetaryDetails = $this->requestModel->getNonMonetaryRequestDetails($request->RequestID);
                if($nonMonetaryDetails) {
                    $request->QuantityNeeded = $nonMonetaryDetails->QuantityNeeded;
                    $request->QuantityReceived = $nonMonetaryDetails->QuantityReceived;
                }
            }
        }

        // Get donor statistics
        $donorStats = $this->donorModel->getDonorStats($donorId);

        // Get badge count - you might need to implement this
        $badgeCount = $this->badgeModel->getDonorBadgeCount($donorId);

        // Calculate impact count (simplified - assuming each donation impacts 5 lives)
        $impactCount = ($donorStats->DonationCount ?? 0) * 5;

        $data = [
            'title' => 'Donor Dashboard',
            'recentRequests' => $recentRequests,
            'donorStats' => $donorStats,
            'badgeCount' => $badgeCount,
            'impactCount' => $impactCount
        ];

        $this->view('donors/dashboard', $data);
    }

    public function getDonorStats($donorId) {
        $this->db->query('SELECT * FROM donors WHERE DonorID = :donorId');
        $this->db->bind(':donorId', $donorId);
        
        return $this->db->single();
    }
    
    public function getDonorDonationHistory($donorId) {
        $this->db->query('SELECT d.*, dr.Title, dr.Category, dr.RequestType 
                         FROM donations d 
                         JOIN donation_requests dr ON d.RequestID = dr.RequestID 
                         WHERE d.DonorID = :donorId 
                         ORDER BY d.DonationDate DESC');
        
        $this->db->bind(':donorId', $donorId);
        
        return $this->db->resultSet();
    }
    
    public function updateDonorStatistics($donorId, $amount = 0) {
        // Get current stats
        $this->db->query('SELECT TotalDonations, DonationCount FROM donors WHERE DonorID = :donorId');
        $this->db->bind(':donorId', $donorId);
        $currentStats = $this->db->single();
        
        // Calculate new values
        $newTotal = $currentStats->TotalDonations + $amount;
        $newCount = $currentStats->DonationCount + 1;
        
        // Update donor statistics
        $this->db->query('UPDATE donors SET TotalDonations = :totalDonations, DonationCount = :donationCount WHERE DonorID = :donorId');
        $this->db->bind(':totalDonations', $newTotal);
        $this->db->bind(':donationCount', $newCount);
        $this->db->bind(':donorId', $donorId);
        
        return $this->db->execute();
    }


    /**
 * Show details for a specific donation request
 * @param string $requestId The ID of the donation request to display
 */
public function details($requestId = null) {
    // Check if valid request ID is provided
    if ($requestId === null) {
        flash('donation_error', 'Invalid request', 'alert alert-danger');
        redirect('donors/dashboard');
        return;
    }
    
    // Get request details with all related information
    $request = $this->requestModel->getRequestWithFullDetails($requestId);
    
    if (!$request) {
        flash('donation_error', 'Request not found', 'alert alert-danger');
        redirect('donors/dashboard');
        return;
    }
    
    // Get item details for non-monetary donations
    $itemDetails = null;
    if ($request->RequestType == 'NonMonetary') {
        $itemDetails = $this->requestModel->getNonMonetaryRequestDetails($requestId);
    }
    
    // Get similar requests (same category, excluding current request)
    $similarRequests = $this->requestModel->getSimilarRequests($requestId, $request->Category, 3);
    
    // Check for updates (this would need to be implemented in your model)
    $updates = []; // Placeholder - you would fetch real updates from a database table

    // Set up the data for the view
    $data = [
        'title' => $request->Title . ' - Donation Details',
        'request' => $request,
        'itemDetails' => $itemDetails,
        'similarRequests' => $similarRequests,
        'updates' => $updates
    ];
    
    $this->view('donors/details', $data);
}


/**
 * Show all donation requests with filtering and pagination
 */
public function allRequests() {
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
    $requests = $this->requestModel->getFilteredRequests($category, $type, $search, $perPage, $offset);
    
    // Count total matching requests for pagination
    $totalRequests = $this->requestModel->countFilteredRequests($category, $type, $search);
    $totalPages = ceil($totalRequests / $perPage);
    
    // Build query string for pagination links
    $queryParams = [];
    if (!empty($category)) $queryParams[] = 'category=' . urlencode($category);
    if (!empty($type)) $queryParams[] = 'type=' . urlencode($type);
    if (!empty($search)) $queryParams[] = 'search=' . urlencode($search);
    
    $queryString = !empty($queryParams) ? '&' . implode('&', $queryParams) : '';
    
    $data = [
        'title' => 'All Donation Requests',
        'requests' => $requests,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalRequests' => $totalRequests,
        'queryString' => $queryString
    ];
    
    $this->view('donors/allRequests', $data);
}


/**
 * View all feedback reports for this donor
 * @return void
 */
public function feedback() {
    // Initialize Feedback model
    $feedbackModel = $this->model('Feedback');
    
    // Get page number for pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $perPage = 10; // Number of feedback items per page
    $offset = ($page - 1) * $perPage;
    
    // Get feedback with pagination
    $feedback = $this->donationModel->getFeedbackForDonor($_SESSION['donor_id']);
    
    // Count total feedback for pagination
    $feedbackStats = $feedbackModel->getDonorFeedbackStats($_SESSION['donor_id']);
    $totalFeedback = $feedbackStats->TotalFeedback;
    $totalPages = ceil($totalFeedback / $perPage);
    
    // Get donor's feedback statistics for summary display
    $donorStats = $this->donorModel->getDonorStats($_SESSION['donor_id']);
    
    $data = [
        'title' => 'Recipient Feedback',
        'feedback' => $feedback,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalFeedback' => $totalFeedback,
        'feedbackStats' => $feedbackStats,
        'donorStats' => $donorStats
    ];
    
    $this->view('donors/feedback', $data);
}

/**
 * View specific feedback details
 * @param string $feedbackId The feedback ID
 * @return void
 */
public function viewFeedback($feedbackId = null) {
    // Check if feedback ID is provided
    if (!$feedbackId) {
        flash('feedback_error', 'Invalid feedback report', 'alert alert-danger');
        redirect('donors/feedback');
        return;
    }
    
    // Initialize Feedback model
    $feedbackModel = $this->model('Feedback');
    
    // Get feedback details
    $feedback = $this->donationModel->getFeedbackDetails($feedbackId, $_SESSION['donor_id']);
    
    // Check if feedback exists and belongs to this donor
    if (!$feedback) {
        flash('feedback_error', 'Feedback report not found or access denied', 'alert alert-danger');
        redirect('donors/feedback');
        return;
    }
    
    // Mark feedback as read
    $this->donationModel->markFeedbackAsRead($feedbackId, $_SESSION['donor_id']);
    
    // Prepare data for view
    $data = [
        'title' => 'Feedback Details',
        'feedback' => $feedback
    ];
    
    $this->view('donors/view_feedback', $data);
}

/**
 * Mark all feedback as read
 * @return void
 */
public function markAllFeedbackAsRead() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get all unread feedback for this donor
        $this->db->query('UPDATE feedback_reports fr
                         JOIN donations d ON fr.DonationID = d.DonationID
                         SET fr.ViewedByDonor = 1
                         WHERE d.DonorID = :donorId AND fr.ViewedByDonor = 0');
        
        $this->db->bind(':donorId', $_SESSION['donor_id']);
        
        if ($this->db->execute()) {
            flash('feedback_message', 'All feedback marked as read');
        } else {
            flash('feedback_error', 'Failed to update feedback status', 'alert alert-danger');
        }
    }
    
    redirect('donors/feedback');
}




}