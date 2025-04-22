<?php
class AuthModerators extends Controller {
    private $moderatorModel;
    private $userModel;
    
    public function __construct() {
        // Check if user is logged in and is an auth moderator
        if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'AuthModerator') {
            redirect('users/login');
        }
        
        // Load models
        try {
            $this->moderatorModel = $this->model('AuthModerator');
            $this->userModel = $this->model('User');
        } catch (Exception $e) {
            die('Error loading models: ' . $e->getMessage());
        }
    }
    
    public function index() {
        redirect('authModerators/dashboard');
    }
    
    public function dashboard() {
        try {
            // Get verification statistics
            $stats = $this->moderatorModel->getVerificationStats();
    
            // Get request statistics (includes pending, inProgress, completed, expired)
            $requestStats = $this->moderatorModel->getRequestStats();
    
            // Get pending recipients and requests
            $pendingRecipients = $this->moderatorModel->getPendingRecipients();
            $pendingRequests = $this->moderatorModel->getPendingRequests();
    
            // Get recently verified recipients
            $recentlyVerified = $this->moderatorModel->getRecentlyVerifiedRecipients();
    
            // Get moderator info
            $moderator = $this->moderatorModel->getModeratorByUserId($_SESSION['user_id']);
    
            $data = [
                'title' => 'Authentication Moderator Dashboard',
                'stats' => $stats,
                'requestStats' => $requestStats, // ✅ New data added
                'pendingRecipients' => $pendingRecipients,
                'pendingRequests' => $pendingRequests,
                'recentlyVerified' => $recentlyVerified,
                'moderator' => $moderator
            ];
    
            $this->view('authModerators/dashboard', $data);
            $this->view('authModerators/manageRecipients', $data);
            $this->view('authModerators/manageRequests', $data);
    
        } catch (Exception $e) {
            // Log error
            error_log("Error in auth moderator dashboard: " . $e->getMessage());
            
            flash('dashboard_error', 'An error occurred while loading the dashboard', 'alert alert-danger');
            
            // Load dashboard with empty data
            $data = [
                'title' => 'Authentication Moderator Dashboard',
                'stats' => (object)[
                    'pendingRecipients' => 0,
                    'approvedRecipients' => 0,
                    'rejectedRecipients' => 0,
                    'pendingRequests' => 0,
                    'approvedRequests' => 0,
                    'rejectedRequests' => 0
                ],
                'pendingRecipients' => [],
                'pendingRequests' => [],
                'recentlyVerified' => [],
                'moderator' => (object)['VerificationCount' => 0]
            ];
            
            $this->view('authModerators/dashboard', $data);
        }
    }
    
    public function manageRecipients() {
        try {
            $pendingRecipients = $this->moderatorModel->getPendingRecipients();
            
            $data = [
                'title' => 'Manage Recipients',
                'pendingRecipients' => $pendingRecipients
            ];
            
            $this->view('authModerators/manageRecipients', $data);
        } catch (Exception $e) {
            flash('error', 'An error occurred while loading recipients', 'alert alert-danger');
            redirect('authModerators/dashboard');
        }
    }
    
    public function manageRequests() {
        try {
            $approvedRequests = $this->moderatorModel->getApprovedRequests(); // new addition
            
            $data = [
                'title' => 'Manage Donation Requests',
                'approvedRequests' => $approvedRequests // pass to view
            ];
            
            $this->view('authModerators/manageRequests', $data);
        } catch (Exception $e) {
            flash('error', 'An error occurred while loading requests', 'alert alert-danger');
            redirect('authModerators/dashboard');
        }
    }
    
    
    public function viewRecipient($id) {
        // Code to view a specific recipient's details
    }

    public function verify_recipient() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recipientId = $_POST['recipient_id'];
            $status = $_POST['status'];
            $moderatorId = $_SESSION['user_id'];
    
            if ($this->moderatorModel->verifyRecipient($recipientId, $status, $moderatorId)) {
                flash('success_message', 'Recipient verification updated successfully');
            } else {
                flash('error_message', 'Failed to update recipient verification', 'alert alert-danger');
            }
    
            // ✅ Redirect to manageRecipients, not dashboard
            redirect('authModerators/manageRecipients');
        }
    }
    
    
    public function approve() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recipientId = $_POST['recipient_id']; // Get the recipient ID from the POST data
            $status = 'Approved'; // Set the status to Approved
            
            // Call the model method to update the verification status
            if ($this->moderatorModel->verifyRecipient($recipientId, $status)) {
                $_SESSION['moderator_success'] = 'Recipient approved successfully';
                header('Location: ' . URLROOT . '/authModerators/manageRecipients');
                exit;
            } else {
                $_SESSION['moderator_error'] = 'Failed to approve recipient';
                header('Location: ' . URLROOT . '/authModerators/manageRecipients');
                exit;
            }
        } else {
            // Redirect to verifications page if not POST
            header('Location: ' . URLROOT . '/authModerators/manageRecipients');
            exit;
        }
    }

    public function reject() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recipientId = $_POST['recipient_id']; // Get the recipient ID from the POST data
            $status = 'Rejected'; // Set the status to Rejected
            
            // Call the model method to update the verification status
            if ($this->moderatorModel->verifyRecipient($recipientId, $status)) {
                $_SESSION['moderator_success'] = 'Recipient rejected successfully';
                header('Location: ' . URLROOT . '/authModerators/manageRecipients');
                exit;
            } else {
                $_SESSION['moderator_error'] = 'Failed to reject recipient';
                header('Location: ' . URLROOT . '/authModerators/manageRecipients');
                exit;
            }
        }    else {
                // Redirect to verifications page if not POST
                header('Location: ' . URLROOT . '/authModerators/manageRecipients');
                exit;
            }
    }
    
    
    public function viewRequest($id) {
        // Code to view a specific request's details
    }
    
    public function approveRequest($id) {
        if($this->moderatorModel->approveRequest($id, $_SESSION['moderator_id'])) {
            flash('success_message', 'Request approved successfully');
        } else {
            flash('error_message', 'Failed to approve request', 'alert alert-danger');
        }
        
        redirect('authModerators/manageRequests');
    }
    
    public function rejectRequest($id) {
        if($this->moderatorModel->rejectRequest($id, $_SESSION['moderator_id'])) {
            flash('success_message', 'Request rejected successfully');
        } else {
            flash('error_message', 'Failed to reject request', 'alert alert-danger');
        }
        
        redirect('authModerators/manageRequests');
    }

    public function updateRequestStatus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $requestID = $_POST['request_id'];
            $newStatus = $_POST['new_status'];
    
            if ($this->moderatorModel->updateRequestStatus($requestID, $newStatus)) {
                $_SESSION['moderator_success'] = 'Request status updated successfully.';
            } else {
                $_SESSION['moderator_error'] = 'Error updating request status.';
            }
    
            redirect('authModerators/manageRequests');
        } else {
            redirect('authModerators/manageRequests');
        }
    }
    
}