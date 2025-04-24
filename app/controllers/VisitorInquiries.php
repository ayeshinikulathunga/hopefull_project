<?php
class VisitorInquiries extends Controller {
    private $inquiryModel;
    
    public function __construct() {
        $this->inquiryModel = $this->model('VisitorInquiry');
    }
    
    public function index() {
        // If admin wants to view all inquiries, implement here
        // For now, redirect to home
        redirect('pages/index');
    }
    
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Process form
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'subject' => trim($_POST['subject']),
                'message' => trim($_POST['message']),
                'name_err' => '',
                'email_err' => '',
                'subject_err' => '',
                'message_err' => ''
            ];
            
            // Validate inputs
            if (empty($data['name'])) {
                $data['name_err'] = 'Please enter your name';
            }
            
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter your email';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email';
            }
            
            if (empty($data['subject'])) {
                $data['subject_err'] = 'Please enter a subject';
            }
            
            if (empty($data['message'])) {
                $data['message_err'] = 'Please enter your message';
            }
            
            // Make sure errors are empty
            if (empty($data['name_err']) && empty($data['email_err']) && 
                empty($data['subject_err']) && empty($data['message_err'])) {
                // Validated
                if ($this->inquiryModel->addInquiry($data)) {
                    flash('inquiry_success', 'Your inquiry has been submitted successfully');
                    redirect('pages/index?success=Your inquiry has been submitted successfully. We will get back to you soon.');
                } else {
                    flash('inquiry_error', 'Something went wrong, please try again');
                    redirect('pages/index?error=Something went wrong. Please try again later.');
                }
            } else {
                // Build error message
                $errorMsg = '';
                if (!empty($data['name_err'])) $errorMsg .= $data['name_err'] . '. ';
                if (!empty($data['email_err'])) $errorMsg .= $data['email_err'] . '. ';
                if (!empty($data['subject_err'])) $errorMsg .= $data['subject_err'] . '. ';
                if (!empty($data['message_err'])) $errorMsg .= $data['message_err'] . '. ';
                
                redirect('pages/index?error=' . urlencode($errorMsg));
            }
        } else {
            // Not a POST request, redirect to home
            redirect('pages/index');
        }
    }
    
    // Admin methods could be added here for managing inquiries
}
