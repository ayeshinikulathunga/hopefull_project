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
                'pendingRecipients' => $pendingRecipients,
                'pendingRequests' => $pendingRequests,
                'recentlyVerified' => $recentlyVerified,
                'moderator' => $moderator
            ];
            
            $this->view('authModerators/dashboard', $data);
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
            $pendingRequests = $this->moderatorModel->getPendingRequests();
            
            $data = [
                'title' => 'Manage Donation Requests',
                'pendingRequests' => $pendingRequests
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
    
    public function approveRecipient($id) {
        if($this->moderatorModel->approveRecipient($id, $_SESSION['moderator_id'])) {
            flash('success_message', 'Recipient approved successfully');
        } else {
            flash('error_message', 'Failed to approve recipient', 'alert alert-danger');
        }
        
        redirect('authModerators/manageRecipients');
    }
    
    public function rejectRecipient($id) {
        if($this->moderatorModel->rejectRecipient($id, $_SESSION['moderator_id'])) {
            flash('success_message', 'Recipient rejected successfully');
        } else {
            flash('error_message', 'Failed to reject recipient', 'alert alert-danger');
        }
        
        redirect('authModerators/manageRecipients');
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
}