<?php
class Recipients extends Controller {
    private $userModel;
    private $recipientModel;
    
    public function __construct() {
        // Check if user is logged in and is a recipient
        
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'Recipient') {
            redirect('users/login');
        }

        
        if($_SESSION['user_type'] !== 'Recipient') {
            redirect('users/login');
        }
        
        $this->userModel = $this->model('User');
        $this->recipientModel = $this->model('Recipient');
    }
    
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
    
    // More methods will be added for creating requests, etc.
}