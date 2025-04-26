<?php
class Users extends Controller {
    private $userModel;
   
    public function __construct() {
        // Ensure the user model is always loaded
        $this->userModel = $this->model('User');
    }
    public function index() {
        redirect('users/login');
    }

    public function register() {
        $data = [
            'title' => 'Register'
        ];
        $this->view('users/register', $data);
    }

    public function register_donor() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    
            // Init data
            $data = [
                'title' => 'Register as Donor',
                'firstName' => trim($_POST['firstName']),
                'lastName' => trim($_POST['lastName']),
                'email' => trim($_POST['email']),
                'contactNumber' => trim($_POST['contactNumber']),
                'password' => trim($_POST['password']),
                'confirmPassword' => trim($_POST['confirmPassword']),
                'terms' => isset($_POST['terms']),
                'errors' => []
            ];
    
            // Validation
            if(empty($data['firstName'])) {
                $data['errors']['firstName'] = 'Please enter first name';
            }
    
            if(empty($data['lastName'])) {
                $data['errors']['lastName'] = 'Please enter last name';
            }
    
            if(empty($data['email'])) {
                $data['errors']['email'] = 'Please enter email';
            } elseif($this->userModel->findUserByEmail($data['email'])) {
                $data['errors']['email'] = 'Email is already registered';
            }
    
            // Make sure errors are empty
            if(empty($data['errors'])) {
                // Register User
                if($this->userModel->register($data)) {
                    flash('register_success', 'You are registered and can log in');
                    redirect('users/login');
                } else {
                    $data['errors']['general'] = 'Something went wrong during registration';
                    $this->view('users/register_donor', $data);
                }
            } else {
                // Load view with errors
                $this->view('users/register_donor', $data);
            }
        } else {
            // Init data
            $data = [
                'title' => 'Register as Donor',
                'firstName' => '',
                'lastName' => '',
                'email' => '',
                'contactNumber' => '',
                'password' => '',
                'confirmPassword' => '',
                'errors' => []
            ];
    
            $this->view('users/register_donor', $data);
        }
    }

  
public function register_recipient() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Process form
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Handle file upload for documentation
        $documentationURL = '';
        if(isset($_FILES['documentation']) && $_FILES['documentation']['error'] == 0) {
            // Set upload directory
            $upload_dir = '../public/uploads/documents/';
            
            // Create directory if it doesn't exist
            if(!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $filename = uniqid() . '_' . $_FILES['documentation']['name'];
            $destination = $upload_dir . $filename;
            
            // Move uploaded file
            if(move_uploaded_file($_FILES['documentation']['tmp_name'], $destination)) {
                $documentationURL = $filename;
            }
        }

        // Init data
        $data = [
            'title' => 'Register as Recipient',
            'firstName' => trim($_POST['firstName']),
            'lastName' => trim($_POST['lastName']),
            'email' => trim($_POST['email']),
            'contactNumber' => trim($_POST['contactNumber']),
            'address' => trim($_POST['address']),
            'organizationType' => trim($_POST['organizationType']),
            'documentationURL' => $documentationURL, 
            'password' => trim($_POST['password']),
            'confirmPassword' => trim($_POST['confirmPassword']),
            'terms' => isset($_POST['terms']),
            'errors' => [],
            'pending_approval' => false
        ];

        // Validation
        if(empty($data['firstName'])) {
            $data['errors']['firstName'] = 'Please enter first name';
        }

        if(empty($data['lastName'])) {
            $data['errors']['lastName'] = 'Please enter last name';
        }

        if(empty($data['email'])) {
            $data['errors']['email'] = 'Please enter email';
        } elseif($this->userModel->findUserByEmail($data['email'])) {
            $data['errors']['email'] = 'Email is already registered';
        }

        if(empty($data['password'])) {
            $data['errors']['password'] = 'Please enter password';
        } elseif(strlen($data['password']) < 6) {
            $data['errors']['password'] = 'Password must be at least 6 characters';
        }

        if($data['password'] !== $data['confirmPassword']) {
            $data['errors']['confirmPassword'] = 'Passwords do not match';
        }

        if(empty($data['organizationType'])) {
            $data['errors']['organizationType'] = 'Please select an organization type';
        }

        if(empty($documentationURL)) {
            $data['errors']['documentation'] = 'Please upload documentation';
        }

        // Make sure errors are empty
        if(empty($data['errors'])) {
            $existingUser = $this->userModel->findUserByEmail($data['email']);
            if($existingUser && $this->userModel->findUserByEmail($data['email'])){
                if($existingUser && $this->userModel->hasPendingRecipientRequest($existingUser->UserID)){
                    $data['pending_approval'] = true;
                    flash('register_info', 'You already have a pending registration request. Please wait for approval','alert alert-danger');
                    $this->view('users/register_recipient', $data);
                    return;
                }

            }
            if($this->userModel->registerRecipient($data)) {
                $data['pending_approval'] = true;
                flash('register_success', 'Your registration request has been submitted for approval.', 'alert alert-success');
                $this->view('users/register_recipient', $data);
            } else {
                $data['errors']['general'] = 'Something went wrong during registration';
                $this->view('users/register_recipient', $data);
            }
        } else {
            // Load view with errors
            $this->view('users/register_recipient', $data);
        }
    } else {
        // Init data
        $data = [
            'title' => 'Register as Recipient',
            'firstName' => '',
            'lastName' => '',
            'email' => '',
            'contactNumber' => '',
            'address' => '',
            'organizationType' => '',
            'documentationURL' => '',
            'password' => '',
            'confirmPassword' => '',
            'errors' => [],
            'pending_approval' => false
        ];

        if(isset($_SESSION['user_email'])){
            $existingUser = $this->userModel->findUserByEmail($_SESSION['user_email']);
            if($existingUser && $this->userModel->hasPendingRecipientRequest($existingUser->UserID)){
                $data['pending_approval'] = true;
                flash('register_info', 'You already have a pending registration request. Please wait for approval','alert alert-danger');
            }
        }

        $this->view('users/register_recipient', $data);
    }
}




    public function login() {
        // Store the redirect location in session if it exists in query params
        if(isset($_GET['redirect'])) {
        $_SESSION['redirect_after_login'] = $_GET['redirect'];
        }
    
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Process form
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        // Init data
        $data = [
            'title' => 'Login',
            'email' => trim($_POST['email']),
            'password' => trim($_POST['password']),
            'errors' => []
        ];
    }


        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Init data
            $data = [
                'title' => 'Login',
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'errors' => []
            ];
            
            // Validate Email
            if(empty($data['email'])) {
                $data['errors']['email'] = 'Please enter email';
            }
            
            // Validate Password
            if(empty($data['password'])) {
                $data['errors']['password'] = 'Please enter password';
            }
            
            // Check for errors
            if(empty($data['errors'])) {
                // Add detailed logging
                error_log("Attempting to log in with email: " . $data['email']);
                
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                
                if (!$loggedInUser) {
                    error_log('Login Failed for email: ' . $data['email']);
                    $data['errors']['login'] = 'Invalid email or password';
                    $this->view('users/login', $data);
                    return;
                }
                
                if ($loggedInUser->UserType === 'Recipient') {
                    $recipientStatus = $this->userModel->getRecipientVerificationStatus($loggedInUser->UserID);
                
                    if ($recipientStatus === 'Pending') {
                        flash('login_error', 'Your account is pending approval. Please wait for administrator approval.', 'alert alert-danger');
                        redirect('users/login');
                        return;
                    }
                
                    if ($recipientStatus === 'Rejected') {
                        flash('login_error', 'Your account request has been rejected. Please contact support for more information.', 'alert alert-danger');
                        redirect('users/register_recipient');
                        return;
                    }
                }
                
                error_log('Login Successful for: ' . $data['email']);
                $this->createUserSession($loggedInUser);
            } else {
                // Load view with errors
                $this->view('users/login', $data);
            }
        } else {
            // Init data
            $data = [
                'title' => 'Login',
                'email' => '',
                'password' => '',
                'errors' => []
            ];
            
            $this->view('users/login', $data);
        }

        
    }
    
    


    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->UserID;
        $_SESSION['user_email'] = $user->Email;
        $_SESSION['user_name'] = $user->Username;
        $_SESSION['user_type'] = $user->UserType;
        
        // Set specific IDs based on user type
        if(property_exists($user, 'DonorID') && $user->DonorID) {
            $_SESSION['donor_id'] = $user->DonorID;
        }
        
        if(property_exists($user, 'RecipientID') && $user->RecipientID) {
            $_SESSION['recipient_id'] = $user->RecipientID;
        }
    
        if(property_exists($user, 'SystemAdminID') && $user->SystemAdminID) {
            $_SESSION['admin_id'] = $user->SystemAdminID;
        }

        if(property_exists($user, 'ModeratorID') && $user->ModeratorID) {
            $_SESSION['moderator_id'] = $user->ModeratorID;
        }

        if(property_exists($user, 'RegionalOfficerID') && $user->RegionalOfficerID) {
            $_SESSION['regional_officer_id'] = $user->RegionalOfficerID;
        }
        
        if(property_exists($user, 'SellerID') && $user->SellerID) {
            $_SESSION['seller_id'] = $user->SellerID;
        }

         // Check if there's a redirect stored in session
        if(isset($_SESSION['redirect_after_login'])) {
        $redirect = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']); // Clear the redirect
        
        if($redirect == 'marketplace') {
            redirect('marketplace');
            return; // Stop execution here
        }
        }
        // Redirect based on user type
        switch($user->UserType) {
            case 'Donor':
                redirect('donors/dashboard');
                break;
            case 'Recipient':
                redirect('recipients/dashboard');
                break;
            case 'SystemAdmin':
            case 'Admin':
                redirect('admins/dashboard');
                break;

            case 'AuthModerator':
                redirect('authModerators/dashboard');
                break;

            case 'RegionalOfficer':
                redirect('regionalOfficers/dashboard');
                break;
            case 'Seller':
                redirect('sellers/dashboard');
                break;
            default:
                redirect('users/login');


        }
    }

    public function logout() {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        redirect('users/login');
    }
   
   

    }