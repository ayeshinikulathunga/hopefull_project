<?php
class Profile extends Controller {
    private $donorModel;
    private $donationModel;
    private $userModel;
    private $badgeModel;
    private $db;

    public function __construct() {
        // Check if user is logged in and is a donor
        if (!isLoggedIn()) {
            redirect('users/login');
        } else if ($_SESSION['user_type'] !== 'Donor') {
            // Initialize database instance
            $this->db = new Database();
        }

        // Load models
        $this->donorModel = $this->model('Donor');
        $this->donationModel = $this->model('Donation');
        $this->userModel = $this->model('User');
        $this->badgeModel = $this->model('Badge');
    }

    /**
     * Display donor profile page
     */
    public function index() {
        // Get donor ID from session
        $donorId = $_SESSION['donor_id'];

        // Get donor data
        $donor = $this->donorModel->getDonorById($donorId);
        
        // Get user data (for email, etc.)
        $user = $this->userModel->getUserById($_SESSION['user_id']);

        // Get donor statistics
        $stats = $this->donorModel->getDonorStats($donorId);

        // Get donor ranking (position in leaderboard)
        $ranking = $this->donorModel->getDonorRanking($donorId);
        
        // Get top donors for leaderboard (limit to top 10)
        $topDonors = $this->donorModel->getTopDonors(10);
        
        // Get pending donations for calendar
        $pendingDonations = $this->donationModel->getPendingNonMonetaryDonations($donorId);
        
        // Get upcoming donations for the next 30 days
        $upcomingDonations = $this->donationModel->getUpcomingDonations($donorId, 30);
        
        // Get badges earned by the donor
        $badges = $this->badgeModel->getDonorBadges($donorId);

        // Prepare data for the view
        $data = [
            'title' => 'My Profile',
            'donor' => $donor,
            'user' => $user,
            'stats' => $stats,
            'ranking' => $ranking,
            'topDonors' => $topDonors,
            'pendingDonations' => $pendingDonations,
            'upcomingDonations' => $upcomingDonations,
            'badges' => $badges
        ];

        // Load view
        $this->view('donors/profile', $data);
    }

    /**
     * Update donor profile
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('profile');
            return;
        }

        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

        // Get donor ID from session
        $donorId = $_SESSION['donor_id'];
        $userId = $_SESSION['user_id'];

        // Prepare data for update
        $data = [
            'donorId' => $donorId,
            'userId' => $userId,
            'firstName' => trim($_POST['firstName']),
            'lastName' => trim($_POST['lastName']),
            'contactNumber' => trim($_POST['contactNumber']),
            'anonymousPreference' => isset($_POST['anonymousPreference']) ? 1 : 0,
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
            // Update donor profile
            if ($this->donorModel->updateDonor($data)) {
                flash('profile_success', 'Your profile has been updated successfully');
                redirect('profile');
            } else {
                flash('profile_error', 'Something went wrong when updating your profile', 'alert alert-danger');
                redirect('profile');
            }
        } else {
            // Load view with errors
            flash('profile_error', 'Please fix the errors below', 'alert alert-danger');
            redirect('profile');
        }
    }

    /**
     * Change account password
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('profile');
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
                redirect('profile');
            } else {
                flash('profile_error', 'Something went wrong when updating your password', 'alert alert-danger');
                redirect('profile');
            }
        } else {
            // Load view with errors
            flash('profile_error', 'Please fix the errors below', 'alert alert-danger');
            redirect('profile');
        }
    }

    /**
     * Delete account (requires confirmation)
     */
    public function deleteAccount() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('profile');
            return;
        }

        // Get user ID and donor ID from session
        $userId = $_SESSION['user_id'];
        $donorId = $_SESSION['donor_id'];

        // Check if confirmation password is provided
        if (empty($_POST['confirmation_password'])) {
            flash('profile_error', 'Please enter your password to confirm account deletion', 'alert alert-danger');
            redirect('profile');
            return;
        }

        // Verify password
        if (!$this->userModel->verifyPassword($userId, $_POST['confirmation_password'])) {
            flash('profile_error', 'Password is incorrect', 'alert alert-danger');
            redirect('profile');
            return;
        }

        // Begin transaction to ensure all records are deleted properly
        $this->db = new Database;
        $this->db->beginTransaction();

        try {
            // Delete donor record
            if (!$this->donorModel->deleteDonor($donorId)) {
                throw new Exception('Failed to delete donor record');
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
            redirect('profile');
        }
    }

    /**
     * Get donor's calendar data in JSON format
     */
    public function getCalendarData() {
        // Set header to return JSON
        header('Content-Type: application/json');
        
        // Get donor ID from session
        $donorId = $_SESSION['donor_id'];
        
        // Get pending donations for the next 6 months
        $upcomingDonations = $this->donationModel->getUpcomingDonations($donorId, 180);
        
        // Format donations for calendar
        $calendarData = [];
        
        foreach ($upcomingDonations as $donation) {
            $calendarData[] = [
                'id' => $donation->DonationID,
                'title' => $donation->ItemName . ' - ' . $donation->Title,
                'start' => $donation->DropOffDate . 'T' . $donation->DropOffTime,
                'url' => URLROOT . '/donations/donationDetails/' . $donation->DonationID,
                'className' => 'calendar-event-donation'
            ];
        }
        
        // Return JSON data
        echo json_encode($calendarData);
        exit;
    }
}