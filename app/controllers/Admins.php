<?php
class Admins extends Controller {
    private $adminModel;
    private $userModel;
    
    public function __construct() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        
        // Check if user is a system admin
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'SystemAdmin') {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        
        $this->adminModel = $this->model('Admin');
        $this->userModel = $this->model('User');
    }
    
    public function dashboard() {
        // Fetch dashboard data
        $data = [
            'title' => 'Admin Dashboard',
            'user_counts' => $this->adminModel->getUserCounts(),
            'recent_users' => $this->adminModel->getRecentUsers(),
            'pending_verifications' => $this->adminModel->getPendingVerifications()
        ];
        
        $this->view('admins/dashboard', $data);
    }
    
    public function users() {
        // Manage users
        $data = [
            'title' => 'Manage Users',
            'users' => $this->adminModel->getRecentUsers(50)
        ];
        
        $this->view('admins/users', $data);
    }
    
    public function verifications() {
        $data = [
            'title' => 'Verification Requests',
            'pending_verifications' => $this->adminModel->getPendingVerifications()
        ];
        
        $this->view('admins/verifications', $data);
    }
    
    public function update_user_status() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Ensure admin is logged in
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'SystemAdmin') {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_POST['user_id'];
            $status = $_POST['status'];
            
            // Set a session message
            if($this->adminModel->updateUserStatus($userId, $status)) {
                $_SESSION['admin_success'] = 'User status updated successfully';
                header('Location: ' . URLROOT . '/admins/users');
                exit;
            } else {
                $_SESSION['admin_error'] = 'Failed to update user status';
                header('Location: ' . URLROOT . '/admins/users');
                exit;
            }
        }
    }
    
    public function verify_recipient() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Ensure admin is logged in
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'SystemAdmin') {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recipientId = $_POST['recipient_id'];
            $status = $_POST['status'];
            $moderatorId = $_SESSION['user_id']; // Current admin's user ID
            
            if($this->adminModel->verifyRecipient($recipientId, $status, $moderatorId)) {
                $_SESSION['admin_success'] = 'Recipient verification updated successfully';
                header('Location: ' . URLROOT . '/admins/verifications');
                exit;
            } else {
                $_SESSION['admin_error'] = 'Failed to update recipient verification';
                header('Location: ' . URLROOT . '/admins/verifications');
                exit;
            }
        }
    }
}