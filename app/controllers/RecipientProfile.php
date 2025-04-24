<?php
class RecipientProfile extends Controller {
    private $recipientModel;
    private $donationRequestModel;
    private $donationModel;
    private $userModel;
    private $db;

    public function __construct() {
        // Check if user is logged in and is a recipient
        if (!isLoggedIn()) {
            redirect('users/login');
        } else if ($_SESSION['user_type'] !== 'Recipient') {
            $this->db = new Database();
        }
    
        // Load models
        $this->recipientModel = $this->model('Recipient');
        $this->donationRequestModel = $this->model('DonationRequest');
        $this->donationModel = $this->model('Donation');
        $this->userModel = $this->model('User');
    }

    /**
     * Display recipient profile page
     */
    public function index() {
        // Get recipient ID from session
        $recipientId = $_SESSION['recipient_id'];

        // Get recipient data
        $recipient = $this->recipientModel->getRecipientById($recipientId);

        $recipient->TotalMonetaryDonations = $this->donationModel->getTotalMonetaryDonations($recipientId);

        $recipient->TotalDonationsReceived = $this->donationModel->getTotalDonationsCount($recipientId);
        
        // Get user data (for email, etc.)
        $user = $this->userModel->getUserById($_SESSION['user_id']);

        // Get recipient statistics
        $stats = $this->recipientModel->getRequestStats($recipientId) ?? (object)[
            'TotalRequests' => 0,
            'CompletedRequests' => 0,
            'InProgressRequests' => 0,
            'TotalContributors' => 0
        ];

        // Get recent donations received
        $recentDonations = $this->donationModel->getRecentDonations($recipientId, 5);
        
        // Get active requests
        $activeRequests = $this->donationRequestModel->getActiveRequests($recipientId);
        
        // Get request history
        $requestHistory = $this->donationRequestModel->getRequestHistory($recipientId);

        // Prepare data for the view
        $data = [
            'title' => 'My Profile',
            'recipient' => $recipient,
            'user' => $user,
            'stats' => $stats,
            'recentDonations' => $recentDonations,
            'activeRequests' => $activeRequests,
            'requestHistory' => $requestHistory
        ];

        // Load view
        $this->view('recipients/profile', $data);
    }

    /**
     * Update recipient profile
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('recipients/profile');
            return;
        }

        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

        // Get recipient ID from session
        $recipientId = $_SESSION['recipient_id'];
        $userId = $_SESSION['user_id'];

        // Prepare data for update
        $data = [
            'recipientId' => $recipientId,
            'userId' => $userId,
            'firstName' => trim($_POST['firstName']),
            'lastName' => trim($_POST['lastName']),
            'contactNumber' => trim($_POST['contactNumber']),
            'organization' => trim($_POST['organization']),
            'address' => trim($_POST['address']),
            'firstName_err' => '',
            'lastName_err' => '',
            'contactNumber_err' => ''
        ];

        // Validate firstName
        if (empty($data['firstName'])) {
            $data['firstName_err'] = 'Please enter first name';
        }

        // Validate lastName
        if (empty($data['lastName'])) {
            $data['lastName_err'] = 'Please enter last name';
        }

        // Validate contactNumber
        if (empty($data['contactNumber'])) {
            $data['contactNumber_err'] = 'Please enter contact number';
        }

        // Make sure no errors
        if (empty($data['firstName_err']) && empty($data['lastName_err']) && empty($data['contactNumber_err'])) {
            // Update recipient profile
            if ($this->recipientModel->updateRecipient($data)) {
                flash('profile_success', 'Your profile has been updated successfully');
                redirect('recipients/profile');
            } else {
                flash('profile_error', 'Something went wrong when updating your profile', 'alert alert-danger');
                redirect('recipients/profile');
            }
        } else {
            // Load view with errors
            flash('profile_error', 'Please fix the errors below', 'alert alert-danger');
            redirect('recipients/profile');
        }
    }

    /**
     * Change account password
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('recipients/profile');
            return;
        }

        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

        // Get user ID from session
        $userId = $_SESSION['user_id'];

        // Prepare data for password change
        $data = [
            'userId' => $userId,
            'current_password' => trim($_POST['current_password']),
            'new_password' => trim($_POST['new_password']),
            'confirm_password' => trim($_POST['confirm_password']),
            'current_password_err' => '',
            'new_password_err' => '',
            'confirm_password_err' => ''
        ];

        // Validate current password
        if (empty($data['current_password'])) {
            $data['current_password_err'] = 'Please enter current password';
        } else {
            // Check if current password is correct
            if (!$this->userModel->verifyPassword($userId, $data['current_password'])) {
                $data['current_password_err'] = 'Current password is incorrect';
            }
        }

        // Validate new password
        if (empty($data['new_password'])) {
            $data['new_password_err'] = 'Please enter new password';
        } elseif (strlen($data['new_password']) < 6) {
            $data['new_password_err'] = 'Password must be at least 6 characters';
        }

        // Validate confirm password
        if (empty($data['confirm_password'])) {
            $data['confirm_password_err'] = 'Please confirm new password';
        } elseif ($data['new_password'] !== $data['confirm_password']) {
            $data['confirm_password_err'] = 'Passwords do not match';
        }

        // Make sure no errors
        if (empty($data['current_password_err']) && empty($data['new_password_err']) && empty($data['confirm_password_err'])) {
            // Update password
            if ($this->userModel->updatePassword($userId, $data['new_password'])) {
                flash('profile_success', 'Your password has been updated successfully');
                redirect('recipients/profile');
            } else {
                flash('profile_error', 'Something went wrong when updating your password', 'alert alert-danger');
                redirect('recipients/profile');
            }
        } else {
            // Load view with errors
            flash('profile_error', 'Please fix the errors below', 'alert alert-danger');
            redirect('recipients/profile');
        }
    }

    /**
     * Delete account (requires confirmation)
     */
    public function deleteAccount() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('recipients/profile');
            return;
        }

        // Get user ID and recipient ID from session
        $userId = $_SESSION['user_id'];
        $recipientId = $_SESSION['recipient_id'];

        // Check if confirmation password is provided
        if (empty($_POST['confirmation_password'])) {
            flash('profile_error', 'Please enter your password to confirm account deletion', 'alert alert-danger');
            redirect('recipients/profile');
            return;
        }

        // Verify password
        if (!$this->userModel->verifyPassword($userId, $_POST['confirmation_password'])) {
            flash('profile_error', 'Password is incorrect', 'alert alert-danger');
            redirect('recipients/profile');
            return;
        }

        $this->db = new Database;
        // Begin transaction to ensure all records are deleted properly
        $this->db->beginTransaction();

        try {
            // Delete recipient record
            if (!$this->recipientModel->deleteRecipient($recipientId)) {
                throw new Exception('Failed to delete recipient record');
            }

            // Delete user record
            if (!$this->userModel->deleteUser($userId)) {
                throw new Exception('Failed to delete user record');
            }

            // If all operations are successful, commit the transaction
            $this->db->commit();

            // Destroy session
            session_destroy();
            
            // Redirect to home page with success message
            flash('register_success', 'Your account has been deleted. We\'re sorry to see you go!');
            redirect('');
        } catch (Exception $e) {
            // Rollback transaction if any operation fails
            $this->db->rollBack();
            
            // Show error message
            flash('profile_error', 'Something went wrong when deleting your account: ' . $e->getMessage(), 'alert alert-danger');
            redirect('recipients/profile');
        }
    }

    /**
     * Get recipient's calendar data in JSON format for donation requests
     */
    public function getCalendarData() {
        // Set header to return JSON
        header('Content-Type: application/json');
        
        // Get recipient ID from session
        $recipientId = $_SESSION['recipient_id'];
        
        // Get all active requests with deadlines
        $requests = $this->donationRequestModel->getAllRequestsByRecipient($recipientId);
        
        // Format requests for calendar
        $calendarData = [];
        
        foreach ($requests as $request) {
            // Add request creation date
            $calendarData[] = [
                'id' => 'creation_' . $request->RequestID,
                'title' => 'Created: ' . $request->Title,
                'start' => date('Y-m-d', strtotime($request->DateCreated)),
                'url' => URLROOT . '/requests/view/' . $request->RequestID,
                'type' => 'creation',
                'className' => 'calendar-event-creation'
            ];
            
            // Add request deadline
            $calendarData[] = [
                'id' => 'deadline_' . $request->RequestID,
                'title' => 'Deadline: ' . $request->Title,
                'start' => date('Y-m-d', strtotime($request->Deadline)),
                'url' => URLROOT . '/requests/view/' . $request->RequestID,
                'type' => 'deadline',
                'className' => 'calendar-event-deadline'
            ];
        }
        
        // Return JSON data
        echo json_encode($calendarData);
        exit;
    }
}