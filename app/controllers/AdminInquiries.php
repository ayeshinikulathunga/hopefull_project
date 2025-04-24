<?php
class AdminInquiries extends Controller {
    private $adminInquiryModel;
    
    public function __construct() {
        // Check if user is logged in as admin
        if (!isLoggedIn() || !isAdmin()) {
            redirect('users/login');
        }
        
        $this->adminInquiryModel = $this->model('AdminInquiry');
    }
    
    // Main inquiries management page
    public function index() {
        // Get all inquiries
        $inquiries = $this->adminInquiryModel->getInquiries();
        
        $data = [
            'title' => 'Manage Inquiries',
            'inquiries' => $inquiries,
            'new_count' => $this->adminInquiryModel->countInquiriesByStatus('New'),
            'in_progress_count' => $this->adminInquiryModel->countInquiriesByStatus('In Progress'),
            'completed_count' => $this->adminInquiryModel->countInquiriesByStatus('Completed')
        ];
        
        $this->view('admins/inquiries/index', $data);
    }
    
    // View single inquiry
    public function view($id) {
        $inquiry = $this->adminInquiryModel->getInquiryById($id);
        
        if (!$inquiry) {
            flash('inquiry_message', 'Inquiry not found', 'alert alert-danger');
            redirect('adminInquiries');
        }
        
        $data = [
            'title' => 'View Inquiry',
            'inquiry' => $inquiry
        ];
        
        $this->view('admins/inquiries/view', $data);
    }
    
    // Update inquiry status
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $id = trim($_POST['id']);
            $status = trim($_POST['status']);
            
            if ($this->adminInquiryModel->updateStatus($id, $status)) {
                flash('inquiry_message', 'Inquiry status updated successfully');
                redirect('adminInquiries/view/' . $id);
            } else {
                flash('inquiry_message', 'Something went wrong', 'alert alert-danger');
                redirect('adminInquiries/view/' . $id);
            }
        } else {
            redirect('adminInquiries');
        }
    }
    
    // Delete inquiry
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->adminInquiryModel->deleteInquiry($id)) {
                flash('inquiry_message', 'Inquiry deleted successfully');
            } else {
                flash('inquiry_message', 'Something went wrong', 'alert alert-danger');
            }
        }
        redirect('adminInquiries');
    }
    
    // Search inquiries
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['keyword'])) {
            $keyword = trim($_GET['keyword']);
            $inquiries = $this->adminInquiryModel->searchInquiries($keyword);
            
            $data = [
                'title' => 'Search Results',
                'inquiries' => $inquiries,
                'keyword' => $keyword
            ];
            
            $this->view('admins/inquiries/search', $data);
        } else {
            redirect('adminInquiries');
        }
    }
}