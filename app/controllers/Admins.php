<?php
class Admins extends Controller {
    private $adminModel;
    private $userModel;
    private $adminInquiryModel;
    
    public function __construct() {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
 
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'SystemAdmin') {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        
        $this->adminModel = $this->model('Admin');
        $this->userModel = $this->model('User');
        $this->adminInquiryModel = $this->model('AdminInquiry');
    }
    
    public function index() {
        header("Location: " . URLROOT . "/admins/addUser ");
        exit();
    }

    public function dashboard() {

        $data = [
            'title' => 'Admin Dashboard',
            'user_counts' => $this->adminModel->getUserCounts(),
            'recent_users' => $this->adminModel->getRecentUsers(),
            'pending_verifications' => $this->adminModel->getPendingVerifications(),
            'inquiries' => $this->adminInquiryModel->getRecentInquiries(5),
            'new_inquiries' => $this->adminInquiryModel->countInquiriesByStatus('New'),
            'in_progress_inquiries' => $this->adminInquiryModel->countInquiriesByStatus('In Progress'),
            'total_inquiries' => $this->adminInquiryModel->countInquiriesByStatus()
        ];
        
        $this->view('admins/dashboard', $data);
    }
    
    public function users() {
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

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'SystemAdmin') {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_POST['user_id'];
            $status = $_POST['status'];
     
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
 
            $data = [
                'email' => trim($_POST['Email']),
                'username' => trim($_POST['Username']),
                'password' => password_hash(trim($_POST['Password']), PASSWORD_DEFAULT),
                'user_type' => trim($_POST['user_type']),
                'user_status' => trim($_POST['user_status'])
            ];
    
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['admin_error'] = "Invalid email format.";
                header("Location: " . URLROOT . "/admins/add_user");
                exit();
            }
    
    
   
            if ($this->adminModel->addUser ($data)) {
                $_SESSION['admin_success'] = "User  added successfully!";

                header("Location: " . URLROOT . "/admins/users");
                exit();
            } else {
                $_SESSION['admin_error'] = "Error adding user.";
            }
    
    
            header("Location: " . URLROOT . "/admins/add_user");
            exit();
        }
    
        $data['title'] = 'Add User';
        $this->view('admins/add_user', $data); 
    }
    
    public function editUser($id) {
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
 
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
   
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'SystemAdmin') {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_POST['user_id'];
            
    
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
      
            header('Location: ' . URLROOT . '/admins/users');
            exit;
        }
    }
    

    public function verify_recipient() {
     
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
 
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'SystemAdmin') {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recipientId = $_POST['recipient_id'];
            $status = $_POST['status'];
            $moderatorId = $_SESSION['user_id']; 
            
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
            $recipientId = $_POST['recipient_id']; 
            $status = 'Approved'; 
            
        
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