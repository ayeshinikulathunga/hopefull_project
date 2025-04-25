<?php
class RegionalOfficers extends Controller {
    private $regionalOfficerModel;
    private $userModel;
    
    public function __construct() {
        // Check if user is logged in and is a regional officer
        if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'RegionalOfficer') {
            redirect('users/login');
        }
        
        // Load models
        try {
            $this->regionalOfficerModel = $this->model('RegionalOfficer');
            $this->userModel = $this->model('User');
        } catch (Exception $e) {
            die('Error loading models: ' . $e->getMessage());
        }
    }
    
    public function index() {
        redirect('regionalOfficers/dashboard');
    }
    
    public function dashboard() {
        try {
            // Get officer information
            $officer = $this->regionalOfficerModel->getOfficerByUserId($_SESSION['user_id']);
            
            // Get inventory stats
            $stats = $this->regionalOfficerModel->getInventoryStats($_SESSION['regional_officer_id']);
            
            // Get inventory items
            $inventoryItems = $this->regionalOfficerModel->getInventoryItems($_SESSION['regional_officer_id']);
            
            // Get pending non-monetary requests
            $pendingRequests = $this->regionalOfficerModel->getPendingNonMonetaryRequests();
            
            $data = [
                'title' => 'Regional Officer Dashboard',
                'officer' => $officer,
                'stats' => $stats,
                'inventoryItems' => $inventoryItems,
                'pendingRequests' => $pendingRequests
            ];
            
            $this->view('regionalOfficers/dashboard', $data);
        } catch (Exception $e) {
            // Log error
            error_log("Error in regional officer dashboard: " . $e->getMessage());
            
            flash('dashboard_error', 'An error occurred while loading the dashboard', 'alert alert-danger');
            
            // Load dashboard with empty data
            $data = [
                'title' => 'Regional Officer Dashboard',
                'officer' => null,
                'stats' => null,
                'inventoryItems' => [],
                'pendingRequests' => []
            ];
            
            $this->view('regionalOfficers/dashboard', $data);
        }
    }
    
    public function inventory() {
        try {
            $inventoryItems = $this->regionalOfficerModel->getInventoryItems($_SESSION['regional_officer_id']);
            
            $data = [
                'title' => 'Regional Inventory Management',
                'inventoryItems' => $inventoryItems
            ];
            
            $this->view('regionalOfficers/inventory', $data);
        } catch (Exception $e) {
            flash('error', 'An error occurred while loading inventory', 'alert alert-danger');
            redirect('regionalOfficers/dashboard');
        }
    }
    
    public function addItem() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data = [
                'itemName' => trim($_POST['itemName']),
                'category' => trim($_POST['category']),
                'quantity' => intval($_POST['quantity']),
                'status' => trim($_POST['status']),
                'officerId' => $_SESSION['regional_officer_id'],
                'errors' => []
            ];
            
            // Validate data
            if(empty($data['itemName'])) {
                $data['errors']['itemName'] = 'Please enter item name';
            }
            
            if(empty($data['category'])) {
                $data['errors']['category'] = 'Please select a category';
            }
            
            if($data['quantity'] <= 0) {
                $data['errors']['quantity'] = 'Quantity must be greater than 0';
            }
            
            // If no errors, add item
            if(empty($data['errors'])) {
                if($this->regionalOfficerModel->addInventoryItem($data)) {
                    flash('success_message', 'Item added successfully');
                    redirect('regionalOfficers/inventory');
                } else {
                    flash('error_message', 'Failed to add item', 'alert alert-danger');
                    $this->view('regionalOfficers/addItem', $data);
                }
            } else {
                $this->view('regionalOfficers/addItem', $data);
            }
        } else {
            $data = [
                'title' => 'Add Inventory Item',
                'itemName' => '',
                'category' => '',
                'quantity' => 1,
                'status' => 'Available',
                'errors' => []
            ];
            
            $this->view('regionalOfficers/addItem', $data);
        }
    }
    
    public function editItem($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    
            $data = [
                'itemId' => $id,
                'itemName' => trim($_POST['itemName']),
                'category' => trim($_POST['category']),
                'quantity' => intval($_POST['quantity']),
                'status' => trim($_POST['status']),
                'officerId' => $_SESSION['regional_officer_id'],
                'errors' => []
            ];
    
            // Validation
            if (empty($data['itemName'])) {
                $data['errors']['itemName'] = 'Please enter item name';
            }
    
            if (empty($data['category'])) {
                $data['errors']['category'] = 'Please select a category';
            }
    
            if ($data['quantity'] <= 0) {
                $data['errors']['quantity'] = 'Quantity must be greater than 0';
            }
    
            // If no errors, update item
            if (empty($data['errors'])) {
                if ($this->regionalOfficerModel->updateInventoryItem($data)) {
                    flash('success_message', 'Item updated successfully');
                    redirect('regionalOfficers/inventory');
                } else {
                    flash('error_message', 'Failed to update item', 'alert alert-danger');
                    $this->view('regionalOfficers/editItem', $data);
                }
            } else {
                // Reload form with errors
                $this->view('regionalOfficers/editItem', $data);
            }
        } else {
            // Load item data from the model
            $item = $this->regionalOfficerModel->getInventoryItemById($id);
    
            // Check if item exists and belongs to current officer
            if ($item && $item->RegionalOfficerID == $_SESSION['regional_officer_id']) {
                $data = [
                    'title' => 'Edit Inventory Item',
                    'itemId' => $item->ItemID,
                    'itemName' => $item->ItemName,
                    'category' => $item->Category,
                    'quantity' => $item->Quantity,
                    'status' => $item->Status,
                    'errors' => []
                ];
    
                $this->view('regionalOfficers/editItem', $data);
            } else {
                flash('error_message', 'Invalid item or unauthorized access', 'alert alert-danger');
                redirect('regionalOfficers/inventory');
            }
        }
    }
    
    public function deleteItem($id) {
        // Check if item exists and belongs to this officer
        $item = $this->regionalOfficerModel->getInventoryItemById($id);
        
        if(!$item || $item->RegionalOfficerID != $_SESSION['regional_officer_id']) {
            flash('error', 'Item not found or unauthorized', 'alert alert-danger');
            redirect('regionalOfficers/inventory');
        }
        
        if($this->regionalOfficerModel->deleteInventoryItem($id, $_SESSION['regional_officer_id'])) {
            flash('success_message', 'Item deleted successfully');
        } else {
            flash('error_message', 'Failed to delete item', 'alert alert-danger');
        }
        
        redirect('regionalOfficers/inventory');
    }
    
    public function requests() {
        // Get all approved requests
        $approvedRequests = $this->regionalOfficerModel->getApprovedRequests();
        
        $data = [
            'title' => 'Approved Requests',
            'approvedRequests' => $approvedRequests
        ];

        $this->view('regionalOfficers/requests', $data);
    }

    // Add these methods to the RegionalOfficers controller class

    public function allocate() {
        try {
            $donationStatus = $this->regionalOfficerModel->getNonMonetaryDonationStatus($_SESSION['regional_officer_id']);
            $cancellationRequests = $this->regionalOfficerModel->getPendingCancellationRequests($_SESSION['regional_officer_id']);
            
            $data = [
                'title' => 'Donation Allocation',
                'donation_status' => $donationStatus,
                'cancellation_requests' => $cancellationRequests
            ];
            
            $this->view('regionalOfficers/allocate', $data);
        } catch (Exception $e) {
            // Temporarily comment out the redirect for testing
            // flash('error', 'Error loading allocation page: ' . $e->getMessage(), 'alert alert-danger');
            // redirect('regionalOfficers/dashboard');
            echo "Error: " . $e->getMessage(); // For testing purposes
        }
    }

public function markReceived() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $donationId = $_POST['donation_id'];
        
        if ($this->regionalOfficerModel->markDonationReceived($donationId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }
}

public function approveCancellation() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $donationId = $_POST['donation_id'];
        
        if ($this->regionalOfficerModel->approveCancellation($donationId)) {
            flash('success_message', 'Cancellation approved successfully');
        } else {
            flash('error_message', 'Failed to approve cancellation', 'alert alert-danger');
        }
        
        redirect('regionalOfficers/allocate');
    }
}

public function rejectCancellation() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $donationId = $_POST['donation_id'];
        
        if ($this->regionalOfficerModel->rejectCancellation($donationId)) {
            flash('success_message', 'Cancellation rejected successfully');
        } else {
            flash('error_message', 'Failed to reject cancellation', 'alert alert-danger');
        }
        
        redirect('regionalOfficers/allocate');
    }
}

}