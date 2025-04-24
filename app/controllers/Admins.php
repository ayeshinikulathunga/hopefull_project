<?php
class Admins extends Controller {
    private $adminModel;
    private $userModel;
    private $adminInquiryModel;
    
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
        $this->adminInquiryModel = $this->model('AdminInquiry');
    }
    
    public function index() {
        // Redirect to the addUser  method
        header("Location: " . URLROOT . "/admins/addUser ");
        exit();
    }

    public function dashboard() {
        // Fetch dashboard data
        $data = [
            'title' => 'Admin Dashboard',
            'user_counts' => $this->adminModel->getUserCounts(),
            'recent_users' => $this->adminModel->getRecentUsers(),
            'pending_verifications' => $this->adminModel->getPendingVerifications(),
            // Add inquiry data for dashboard
            'inquiries' => $this->adminInquiryModel->getRecentInquiries(5),
            'new_inquiries' => $this->adminInquiryModel->countInquiriesByStatus('New'),
            'in_progress_inquiries' => $this->adminInquiryModel->countInquiriesByStatus('In Progress'),
            'total_inquiries' => $this->adminInquiryModel->countInquiriesByStatus()
        ];
        
        $this->view('admins/dashboard', $data);
    }
    
    public function users() {
        // Manage users - get all users by default
        $data = [
            'title' => 'Manage Users',
            'users' => $this->adminModel->getAllUsers()
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
                $_SESSION['admin_success'] = 'User status updated successfully!';
                header('Location: ' . URLROOT . '/admins/users');
                exit;
            } else {
                $_SESSION['admin_error'] = 'Failed to update user status';
                header('Location: ' . URLROOT . '/admins/users');
                exit;
            }
        }
    }
    
    public function addUser () {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Validate form data
            $data = [
                'email' => trim($_POST['Email']),
                'username' => trim($_POST['Username']),
                'password' => password_hash(trim($_POST['Password']), PASSWORD_DEFAULT), // Hash the password
                'user_type' => trim($_POST['user_type']),
                'user_status' => trim($_POST['user_status'])
            ];
    
            // Validate email format
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['admin_error'] = "Invalid email format.";
                header("Location: " . URLROOT . "/admins/add_user");
                exit();
            }
    
    
            // Call the model to add the user
            if ($this->adminModel->addUser ($data)) {
                $_SESSION['admin_success'] = "User  added successfully!";
                // Redirect to the users list or dashboard instead of the add user page
                header("Location: " . URLROOT . "/admins/users");
                exit();
            } else {
                $_SESSION['admin_error'] = "Error adding user.";
            }
    
            // Redirect to the add user page in case of error
            header("Location: " . URLROOT . "/admins/add_user");
            exit();
        }
    
        // Load the view
        $data['title'] = 'Add User';
        $this->view('admins/add_user', $data); // Call the view method
    }
    
    public function editUser($id) {
        // Fetch user by ID
        $user = $this->adminModel->getUserById($id);
    
        if (!$user) {
            $_SESSION['admin_error'] = "User not found.";
            header("Location: " . URLROOT . "/admins/users");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'userID' => $id,
                'email' => trim($_POST['email']),
                'username' => trim($_POST['username']),
                'user_type' => trim($_POST['user_type']),
                'user_status' => trim($_POST['user_status'])
            ];
    
            if ($this->adminModel->editUser($data)) {
                $_SESSION['admin_success'] = "User updated successfully!";
                header("Location: " . URLROOT . "/admins/users");
                exit();
            } else {
                $_SESSION['admin_error'] = "Failed to update user.";
            }
        }
    
        $data = [
            'user' => $user,
            'title' => 'Edit User'
        ];
        $this->view('admins/edit_user', $data);
    }
    
    
    
    public function delete_user() {
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
            
            // Try to delete user
            if($this->adminModel->deleteUser($userId)) {
                $_SESSION['admin_success'] = 'User deleted successfully';
                header('Location: ' . URLROOT . '/admins/users');
                exit;
            } else {
                $_SESSION['admin_error'] = 'Failed to delete user. The user may have related records.';
                header('Location: ' . URLROOT . '/admins/users');
                exit;
            }
        } else {
            // Redirect to users page if not POST
            header('Location: ' . URLROOT . '/admins/users');
            exit;
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

    public function approve() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recipientId = $_POST['recipient_id']; // Get the recipient ID from the POST data
            $status = 'Approved'; // Set the status to Approved
            
            // Call the model method to update the verification status
            if ($this->adminModel->verifyRecipient($recipientId, $status)) {
                $_SESSION['admin_success'] = 'Recipient approved successfully';
                header('Location: ' . URLROOT . '/admins/verifications');
                exit;
            } else {
                $_SESSION['admin_error'] = 'Failed to approve recipient';
                header('Location: ' . URLROOT . '/admins/verifications');
                exit;
            }
        } else {
            // Redirect to verifications page if not POST
            header('Location: ' . URLROOT . '/admins/verifications');
            exit;
        }
    }

    public function reject() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recipientId = $_POST['recipient_id']; // Get the recipient ID from the POST data
            $status = 'Rejected'; // Set the status to Rejected
            
            // Call the model method to update the verification status
            if ($this->adminModel->verifyRecipient($recipientId, $status)) {
                $_SESSION['admin_success'] = 'Recipient rejected successfully';
                header('Location: ' . URLROOT . '/admins/verifications');
                exit;
            } else {
                $_SESSION['admin_error'] = 'Failed to reject recipient';
                header('Location: ' . URLROOT . '/admins/verifications');
                exit;
            }
        } else {
            // Redirect to verifications page if not POST
            header('Location: ' . URLROOT . '/admins/verifications');
            exit;
        }
    }

    // START OF NEW INQUIRY FUNCTIONS
    
    /**
     * Display a list of all inquiries
     */
    public function inquiries() {
        $data = [
            'title' => 'Manage Inquiries',
            'inquiries' => $this->adminInquiryModel->getInquiries(),
            'new_count' => $this->adminInquiryModel->countInquiriesByStatus('New'),
            'in_progress_count' => $this->adminInquiryModel->countInquiriesByStatus('In Progress'),
            'completed_count' => $this->adminInquiryModel->countInquiriesByStatus('Completed')
        ];
        
        $this->view('admins/inquiries/index', $data);
    }
    
    /**
     * Display a single inquiry
     * @param int $id The inquiry ID
     */
    public function view_inquiry($id) {
        $inquiry = $this->adminInquiryModel->getInquiryById($id);
        
        if (!$inquiry) {
            $_SESSION['admin_error'] = 'Inquiry not found';
            header('Location: ' . URLROOT . '/admins/inquiries');
            exit;
        }
        
        $data = [
            'title' => 'View Inquiry',
            'inquiry' => $inquiry
        ];
        
        $this->view('admins/inquiries/view', $data);
    }
    
    /**
     * Update the status of an inquiry
     */
    public function update_inquiry_status() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $inquiryId = $_POST['inquiry_id'];
            $status = $_POST['status'];
            
            if ($this->adminInquiryModel->updateStatus($inquiryId, $status)) {
                $_SESSION['admin_success'] = 'Inquiry status updated successfully';
                header('Location: ' . URLROOT . '/admins/view_inquiry/' . $inquiryId);
                exit;
            } else {
                $_SESSION['admin_error'] = 'Failed to update inquiry status';
                header('Location: ' . URLROOT . '/admins/view_inquiry/' . $inquiryId);
                exit;
            }
        } else {
            header('Location: ' . URLROOT . '/admins/inquiries');
            exit;
        }
    }
    
    /**
     * Delete an inquiry
     */
    public function delete_inquiry() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $inquiryId = $_POST['inquiry_id'];
            
            if ($this->adminInquiryModel->deleteInquiry($inquiryId)) {
                $_SESSION['admin_success'] = 'Inquiry deleted successfully';
                header('Location: ' . URLROOT . '/admins/inquiries');
                exit;
            } else {
                $_SESSION['admin_error'] = 'Failed to delete inquiry';
                header('Location: ' . URLROOT . '/admins/inquiries');
                exit;
            }
        } else {
            header('Location: ' . URLROOT . '/admins/inquiries');
            exit;
        }
    }
    
    /**
     * Search inquiries
     */
    public function search_inquiries() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['keyword'])) {
            $keyword = trim($_GET['keyword']);
            $inquiries = $this->adminInquiryModel->searchInquiries($keyword);
            
            $data = [
                'title' => 'Search Results for "' . $keyword . '"',
                'inquiries' => $inquiries,
                'keyword' => $keyword,
                'new_count' => $this->adminInquiryModel->countInquiriesByStatus('New'),
                'in_progress_count' => $this->adminInquiryModel->countInquiriesByStatus('In Progress'),
                'completed_count' => $this->adminInquiryModel->countInquiriesByStatus('Completed')
            ];
            
            $this->view('admins/inquiries/index', $data);
        } else {
            header('Location: ' . URLROOT . '/admins/inquiries');
            exit;
        }
    }
    
    public function filter_inquiries($status = null) {
        if ($status) {
            $inquiries = $this->adminInquiryModel->getInquiriesByStatus($status);
            $title = ucfirst($status) . ' Inquiries';
        } else {
            $inquiries = $this->adminInquiryModel->getInquiries();
            $title = 'All Inquiries';
        }
        
        $data = [
            'title' => $title,
            'inquiries' => $inquiries,
            'status_filter' => $status,
            'new_count' => $this->adminInquiryModel->countInquiriesByStatus('New'),
            'completed_count' => $this->adminInquiryModel->countInquiriesByStatus('Completed')
        ];
        
        $this->view('admins/inquiries/index', $data);
    }
    
    // Add this method to your Admins controller class

    public function markInquiryReplied() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $inquiryId = $_POST['inquiry_id'];
            
            // Update the inquiry status to 'Completed'
            if ($this->adminModel->markInquiryReplied($inquiryId, 'Completed')) {
                $_SESSION['admin_success'] = 'Inquiry marked as replied';
            } else {
                $_SESSION['admin_error'] = 'Failed to update inquiry status';
            }
            
            header('Location: ' . URLROOT . '/admins/dashboard');
            exit;
        } else {
            // Redirect to dashboard if not POST
            header('Location: ' . URLROOT . '/admins/dashboard');
            exit;
        }
    }
    
}