<?php
// Define the ROOTPATH constant if not already defined
if (!defined('ROOTPATH')) {
    define('ROOTPATH', dirname(dirname(dirname(__FILE__))));
}

class Sellers extends Controller {
    private $sellerModel;
    private $orderModel;
    private $userModel;
    private $db;
    
    public function __construct() {
        // Check if user is logged in
        if(!isLoggedIn()) {
            redirect('users/login');
        }
        
        // Load models
        $this->sellerModel = $this->model('Seller');
        $this->orderModel = $this->model('Order');
        $this->userModel = $this->model('User');
        
        // Initialize the database
        $this->db = new Database;
        
        // Get user info
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        // Check if user is a seller
        if(!$user || $user->UserType !== 'Seller') {
            // Not a seller, redirect to home
            flash('access_error', 'You do not have permission to access the seller panel', 'alert alert-danger');
            redirect('');
        }
    }
    
    // Dashboard
    // Update the dashboard method in your Sellers controller

public function dashboard() {
    // Get seller info
    $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
    
    // Handle case where seller record doesn't exist
    if(!$seller) {
        flash('seller_error', 'Seller profile not found. Please contact support.', 'alert alert-danger');
        redirect('');
    }
    
    // Get all products for this seller (for display in the dashboard)
    $products = $this->sellerModel->getSellerProducts($seller->SellerID);
    
    // Get recent orders directly from the database
    $this->db->query('SELECT DISTINCT o.*, u.Username 
                    FROM orders o
                    JOIN orders_items oi ON o.OrderID = oi.OrderID
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    JOIN users u ON o.UserID = u.UserID
                    WHERE p.SellerID = :sellerId
                    ORDER BY o.OrderDate DESC
                    LIMIT 5');
    
    $this->db->bind(':sellerId', $seller->SellerID);
    $recentOrders = $this->db->resultSet();
    
    // Get order count
    $this->db->query('SELECT COUNT(DISTINCT o.OrderID) as count
                    FROM orders o
                    JOIN orders_items oi ON o.OrderID = oi.OrderID
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    WHERE p.SellerID = :sellerId');
    
    $this->db->bind(':sellerId', $seller->SellerID);
    $row = $this->db->single();
    $ordersCount = $row->count;
    
    // Get total revenue
    $this->db->query('SELECT SUM(oi.Price * oi.Quantity) as total
                    FROM orders_items oi
                    JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                    JOIN orders o ON oi.OrderID = o.OrderID
                    WHERE p.SellerID = :sellerId
                    AND o.Status != "Cancelled"');
    
    $this->db->bind(':sellerId', $seller->SellerID);
    $row = $this->db->single();
    $revenue = $row->total ?? 0;
    
    // Get inventory summary
    $this->db->query('SELECT 
                        COUNT(*) as totalProducts,
                        SUM(CASE WHEN Status = "Available" THEN 1 ELSE 0 END) as availableProducts,
                        SUM(CASE WHEN Status = "OutOfStock" THEN 1 ELSE 0 END) as outOfStockProducts,
                        SUM(StockQuantity) as totalStock
                     FROM marketplace_inventory
                     WHERE SellerID = :sellerId');
    
    $this->db->bind(':sellerId', $seller->SellerID);
    $inventory = $this->db->single();
    
    // Get low stock products
    $this->db->query('SELECT *
                     FROM marketplace_inventory
                     WHERE SellerID = :sellerId
                     AND StockQuantity <= 5
                     AND StockQuantity > 0
                     ORDER BY StockQuantity ASC');
    
    $this->db->bind(':sellerId', $seller->SellerID);
    $lowStock = $this->db->resultSet();
    
    // Get pending inquiries
    $this->db->query('SELECT pi.*, p.ProductName, u.Username
                      FROM product_inquiries pi
                      JOIN marketplace_inventory p ON pi.ProductID = p.ProductID
                      JOIN users u ON pi.UserID = u.UserID
                      WHERE p.SellerID = :sellerId
                      AND pi.Status = "Pending"
                      ORDER BY pi.CreatedDate DESC');
    
    $this->db->bind(':sellerId', $seller->SellerID);
    $pendingInquiries = $this->db->resultSet();
    
    $data = [
        'title' => 'Seller Dashboard',
        'seller' => $seller,
        'products' => $products,
        'recent_orders' => $recentOrders,
        'orders_count' => $ordersCount,
        'revenue' => $revenue,
        'inventory' => $inventory,
        'low_stock' => $lowStock,
        'pending_inquiries' => $pendingInquiries
    ];
    
    $this->view('sellers/dashboard', $data);
}
// Add this method to your Sellers.php controller
public function index() {
    // Redirect to dashboard as the default action
    $this->dashboard();
}
    
    // Products management
    public function products() {
        // Get seller info
        $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
        
        // Get all products for this seller
        $products = $this->sellerModel->getSellerProducts($seller->SellerID);
        
        $data = [
            'title' => 'Manage Products',
            'seller' => $seller,
            'products' => $products
        ];
        
        $this->view('sellers/products', $data);
    }
    

public function addProduct() {
    // Get seller info
    $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Process form
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        // Generate a new product ID first
        $productId = 'P' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        // Initialize upload variables
        $productImage = '';
        $uploadError = '';
        
        // Upload image if one was selected
        if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
            // Get file extension
            $fileExt = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            
            // Create new filename with product ID
            $fileName = $productId . '.' . $fileExt;
            
            // Define upload path
            $uploadDir = ROOT_PATH . '/../public/uploads/products/';
            
            // Make directory if it doesn't exist
            if(!file_exists($uploadDir)) {
                if(!mkdir($uploadDir, 0755, true)) {
                    $uploadError = "Failed to create upload directory. Please contact administrator.";
                }
            }
            
            $uploadFile = $uploadDir . $fileName;
            
            // Check if it's a valid image file
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if(in_array(strtolower($fileExt), $validExtensions)) {
                // Check file size (max 2MB)
                if($_FILES['product_image']['size'] <= 2 * 1024 * 1024) {
                    // Move uploaded file
                    if(move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadFile)) {
                        $productImage = $fileName;
                    } else {
                        $uploadError = "Failed to upload image. Please try again.";
                    }
                } else {
                    $uploadError = "File is too large. Maximum size is 2MB.";
                }
            } else {
                $uploadError = "Invalid file type. Please upload jpg, jpeg, png, or gif";
            }
        } else if($_FILES['product_image']['error'] !== 4) { // Error 4 is "no file uploaded"
            $uploadError = "Error uploading file. Error code: " . $_FILES['product_image']['error'];
        } else {
            $uploadError = "Please select an image for the product.";
        }
        
        $data = [
            'title' => 'Add Product',
            'seller' => $seller,
            'seller_id' => $seller->SellerID,
            'product_id' => $productId,
            'product_name' => trim($_POST['product_name']),
            'description' => trim($_POST['description']),
            'price' => floatval($_POST['price']),
            'stock_quantity' => intval($_POST['stock_quantity']),
            'category' => trim($_POST['category']),
            'product_image' => $productImage,
            'product_name_err' => '',
            'description_err' => '',
            'price_err' => '',
            'stock_quantity_err' => '',
            'category_err' => '',
            'upload_err' => $uploadError
        ];
        
        // Validate data
        if(empty($data['product_name'])) {
            $data['product_name_err'] = 'Please enter a product name';
        }
        
        if(empty($data['description'])) {
            $data['description_err'] = 'Please enter a product description';
        }
        
        if($data['price'] <= 0) {
            $data['price_err'] = 'Please enter a valid price';
        }
        
        if($data['stock_quantity'] < 0) {
            $data['stock_quantity_err'] = 'Stock quantity cannot be negative';
        }
        
        if(empty($data['category'])) {
            $data['category_err'] = 'Please select a category';
        }
        
        // Make sure no errors
        if(empty($data['product_name_err']) && empty($data['description_err']) && 
           empty($data['price_err']) && empty($data['stock_quantity_err']) && 
           empty($data['category_err']) && empty($data['upload_err'])) {
            
            // Add product to database
            if($this->sellerModel->addProduct($data)) {
                flash('product_message', 'Product Added Successfully');
                redirect('sellers/products');
            } else {
                // Delete the uploaded image if product couldn't be added
                if(!empty($productImage) && file_exists($uploadFile)) {
                    unlink($uploadFile);
                }
                
                flash('product_message', 'Error adding product. Please try again.', 'alert alert-danger');
                redirect('sellers/products');
            }
        } else {
            // Delete the uploaded image if validation failed
            if(!empty($productImage) && file_exists($uploadFile)) {
                unlink($uploadFile);
            }
            
            // Load view with errors
            $this->view('sellers/add_product', $data);
        }
    } else {
        $data = [
            'title' => 'Add Product',
            'seller' => $seller,
            'product_name' => '',
            'description' => '',
            'price' => '',
            'stock_quantity' => '',
            'category' => '',
            'product_name_err' => '',
            'description_err' => '',
            'price_err' => '',
            'stock_quantity_err' => '',
            'category_err' => '',
            'upload_err' => ''
        ];
        
        $this->view('sellers/add_product', $data);
    }
}

public function editProduct($id) {
    // Get seller info
    $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
    
    // Get product info
    $product = $this->sellerModel->getProductById($id);
    
    // Check if product belongs to this seller
    if(!$product || $product->SellerID !== $seller->SellerID) {
        flash('product_message', 'Unauthorized access or product not found', 'alert alert-danger');
        redirect('sellers/products');
    }
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Process form
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        // Initialize upload variables
        $productImage = '';
        $uploadError = '';
        
        // Upload image if one was selected
        if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
            // Get file extension
            $fileExt = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            
            // Create new filename with product ID
            $fileName = $id . '.' . $fileExt;
            
            // Define upload path - CORRECTED PATH HERE
            $uploadDir = ROOT_PATH . '/../public/uploads/products/';
            
            // Debug logging
            error_log("Edit - Upload directory: " . $uploadDir);
            
            // Make directory if it doesn't exist
            if(!file_exists($uploadDir)) {
                if(!mkdir($uploadDir, 0755, true)) {
                    error_log("Failed to create directory: " . $uploadDir);
                    $uploadError = "Failed to create upload directory.";
                }
            }
            
            $uploadFile = $uploadDir . $fileName;
            
            // Check if it's a valid image file
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if(in_array(strtolower($fileExt), $validExtensions)) {
                // Check file size (max 2MB)
                if($_FILES['product_image']['size'] <= 2 * 1024 * 1024) {
                    // Delete old image files with any extension
                    foreach($validExtensions as $ext) {
                        $oldFile = $uploadDir . $id . '.' . $ext;
                        if(file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                    
                    // Debug logging
                    error_log("Edit - Temp file exists: " . (file_exists($_FILES['product_image']['tmp_name']) ? 'Yes' : 'No'));
                    error_log("Edit - Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No'));
                    
                    // Move uploaded file
                    if(move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadFile)) {
                        $productImage = $fileName;
                        error_log("Edit - File uploaded successfully to: " . $uploadFile);
                    } else {
                        $uploadError = "Failed to upload image. Please try again.";
                        error_log("Edit - Failed to move uploaded file. Source: " . $_FILES['product_image']['tmp_name'] . " Destination: " . $uploadFile);
                    }
                } else {
                    $uploadError = "File is too large. Maximum size is 2MB.";
                }
            } else {
                $uploadError = "Invalid file type. Please upload jpg, jpeg, png, or gif";
            }
        }
        
        $data = [
            'title' => 'Edit Product',
            'seller' => $seller,
            'seller_id' => $seller->SellerID,
            'product_id' => $id,
            'product_name' => trim($_POST['product_name']),
            'description' => trim($_POST['description']),
            'price' => floatval($_POST['price']),
            'stock_quantity' => intval($_POST['stock_quantity']),
            'category' => trim($_POST['category']),
            'product_image' => $productImage,
            'current_image' => $product->ProductImage,
            'product_name_err' => '',
            'description_err' => '',
            'price_err' => '',
            'stock_quantity_err' => '',
            'category_err' => '',
            'upload_err' => $uploadError
        ];
        
        // Validate data
        if(empty($data['product_name'])) {
            $data['product_name_err'] = 'Please enter a product name';
        }
        
        if(empty($data['description'])) {
            $data['description_err'] = 'Please enter a product description';
        }
        
        if($data['price'] <= 0) {
            $data['price_err'] = 'Please enter a valid price';
        }
        
        if($data['stock_quantity'] < 0) {
            $data['stock_quantity_err'] = 'Stock quantity cannot be negative';
        }
        
        if(empty($data['category'])) {
            $data['category_err'] = 'Please select a category';
        }
        
        // Make sure no errors
        if(empty($data['product_name_err']) && empty($data['description_err']) && 
           empty($data['price_err']) && empty($data['stock_quantity_err']) && 
           empty($data['category_err']) && empty($data['upload_err'])) {
            
            // Update product
            if($this->sellerModel->updateProduct($data)) {
                flash('product_message', 'Product Updated Successfully');
                redirect('sellers/products');
            } else {
                flash('product_message', 'Error updating product. Please try again.', 'alert alert-danger');
                redirect('sellers/products');
            }
        } else {
            // Load view with errors
            $this->view('sellers/edit_product', $data);
        }
    } else {
        // Find existing product image
        $currentImage = '';
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        // CORRECTED PATH HERE
        $uploadDir = ROOT_PATH . '/../public/uploads/products/';
        
        foreach($validExtensions as $ext) {
            $imagePath = $uploadDir . $id . '.' . $ext;
            if(file_exists($imagePath)) {
                $currentImage = $id . '.' . $ext;
                break;
            }
        }
        
        // If no new format image found, use the one stored in database
        if(empty($currentImage)) {
            $currentImage = $product->ProductImage;
        }
        
        $data = [
            'title' => 'Edit Product',
            'seller' => $seller,
            'product' => $product,
            'product_name' => $product->ProductName,
            'description' => $product->Description,
            'price' => $product->Price,
            'stock_quantity' => $product->StockQuantity,
            'category' => $product->Category,
            'current_image' => $currentImage,
            'product_name_err' => '',
            'description_err' => '',
            'price_err' => '',
            'stock_quantity_err' => '',
            'category_err' => '',
            'upload_err' => ''
        ];
        
        $this->view('sellers/edit_product', $data);
    }
}


public function deleteProduct($id) {
    // Get seller info
    $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
    
    // Get product info
    $product = $this->sellerModel->getProductById($id);
    
    // Check if product belongs to this seller
    if(!$product || $product->SellerID !== $seller->SellerID) {
        flash('product_message', 'Unauthorized access or product not found', 'alert alert-danger');
        redirect('sellers/products');
    }
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // First, check if this product is used in any orders
        $this->db->query('SELECT COUNT(*) as count FROM orders_items WHERE ProductID = :productId');
        $this->db->bind(':productId', $id);
        $row = $this->db->single();
        
        if($row->count > 0) {
            // Product is in orders, don't delete
            flash('product_message', 'Cannot delete product because it is associated with orders. Consider marking it as out of stock instead.', 'alert alert-danger');
            redirect('sellers/products');
            return;
        }
        
        // If we get here, product has no orders, safe to delete
        if($this->sellerModel->deleteProduct($id, $seller->SellerID)) {
            // Delete all product images
            $uploadDir = ROOT_PATH . '/../public/uploads/products/';
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            foreach($validExtensions as $ext) {
                $imagePath = $uploadDir . $id . '.' . $ext;
                if(file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            // Also check for original image format
            if(!empty($product->ProductImage)) {
                $oldImagePath = $uploadDir . $product->ProductImage;
                if(file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            flash('product_message', 'Product Removed Successfully');
        } else {
            flash('product_message', 'Error removing product', 'alert alert-danger');
        }
        
        redirect('sellers/products');
    } else {
        redirect('sellers/products');
    }
}

    
  
    // Orders management
    public function orders() {
        // Get seller info
        $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
        
        // Get all orders for this seller's products
        $orders = $this->sellerModel->getSellerOrders($seller->SellerID);
        
        $data = [
            'title' => 'Orders',
            'seller' => $seller,
            'orders' => $orders
        ];
        
        $this->view('sellers/orders', $data);
    }
    
    // Order details
    /*public function orderDetails($id) {
        // Get seller info
        $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
        
        // Get order info
        $order = $this->orderModel->getOrderById($id);
        
        // Check if order exists
        if(!$order) {
            flash('order_message', 'Order not found', 'alert alert-danger');
            redirect('sellers/orders');
        }
        
        // Get order items that belong to this seller's products
        $orderItems = $this->sellerModel->getSellerOrderItems($id, $seller->SellerID);
        
        // If no items found for this seller, redirect
        if(empty($orderItems)) {
            flash('order_message', 'No items in this order belong to your products', 'alert alert-danger');
            redirect('sellers/orders');
        }
        
        // Get shipping details
        $shipping = $this->orderModel->getShippingDetails($id);
        
        $data = [
            'title' => 'Order Details',
            'seller' => $seller,
            'order' => $order,
            'order_items' => $orderItems,
            'shipping' => $shipping
        ];
        
        $this->view('sellers/order_details', $data);
    }*/
    
  

// View cancellation requests
public function cancellationRequests() {
    // Get seller info
    $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
    
    // Get all pending cancellation requests for this seller's orders
    $cancellationRequests = $this->orderModel->getSellerCancellationRequests($seller->SellerID);
    
    $data = [
        'title' => 'Cancellation Requests',
        'seller' => $seller,
        'cancellation_requests' => $cancellationRequests
    ];
    
    $this->view('sellers/cancellation_requests', $data);
}

// Process cancellation request
public function processCancellation() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get seller info
        $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
        
        // Process form
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        $data = [
            'cancellation_id' => trim($_POST['cancellation_id']),
            'order_id' => trim($_POST['order_id']),
            'status' => trim($_POST['status']),
            'notes' => trim($_POST['notes'] ?? ''),
            'processed_by' => $_SESSION['user_id']
        ];
        
        // Validate cancellation status
        if (!in_array($data['status'], ['Approved', 'Rejected'])) {
            flash('cancellation_error', 'Invalid status', 'alert alert-danger');
            redirect('sellers/cancellationRequests');
        }
        
        // Get the order to check if it's in a valid state for cancellation
        $order = $this->orderModel->getOrderById($data['order_id']);
        
        // If approving cancellation, check if order is in a cancellable state
        if ($data['status'] === 'Approved') {
            if (!$order || !in_array($order->Status, ['Pending', 'Processing'])) {
                flash('cancellation_error', 'This order cannot be cancelled in its current state', 'alert alert-danger');
                redirect('sellers/orderDetails/' . $data['order_id']);
            }
        }
        
        // Process the cancellation request
        if ($this->orderModel->processCancellationRequest($data)) {
            $statusMessage = $data['status'] === 'Approved' ? 'approved' : 'rejected';
            flash('cancellation_message', "Cancellation request {$statusMessage} successfully", 'alert alert-success');
        } else {
            flash('cancellation_error', 'Something went wrong. Please try again.', 'alert alert-danger');
        }
        
        // Redirect back to cancellation requests
        redirect('sellers/cancellationRequests');
    } else {
        redirect('sellers/cancellationRequests');
    }
}

// Update order details to include cancellation request info
public function orderDetails($id) {
    // Get seller info
    $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
    
    // Get order info
    $order = $this->orderModel->getOrderById($id);
    
    // Check if order exists
    if(!$order) {
        flash('order_message', 'Order not found', 'alert alert-danger');
        redirect('sellers/orders');
    }
    
    // Get order items that belong to this seller's products
    $orderItems = $this->sellerModel->getSellerOrderItems($id, $seller->SellerID);
    
    // If no items found for this seller, redirect
    if(empty($orderItems)) {
        flash('order_message', 'No items in this order belong to your products', 'alert alert-danger');
        redirect('sellers/orders');
    }
    
    // Get shipping details
    $shipping = $this->orderModel->getShippingDetails($id);
    
    // Get cancellation request if any
    $cancellationRequest = $this->orderModel->getCancellationRequest($id);
    
    $data = [
        'title' => 'Order Details',
        'seller' => $seller,
        'order' => $order,
        'order_items' => $orderItems,
        'shipping' => $shipping,
        'cancellation_request' => $cancellationRequest,
        'cancellation_allowed' => in_array($order->Status, ['Pending', 'Processing'])
    ];
    
    $this->view('sellers/order_details', $data);
}
    // Update order status
    public function updateOrderStatus() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get seller info
            $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
            
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $orderId = $_POST['order_id'];
            $status = $_POST['status'];
            
            // Check if order has items from this seller
            $orderItems = $this->sellerModel->getSellerOrderItems($orderId, $seller->SellerID);
            
            if(empty($orderItems)) {
                flash('order_message', 'No items in this order belong to your products', 'alert alert-danger');
                redirect('sellers/orders');
            }
            
            // Update order status
            if($this->orderModel->updateOrderStatus($orderId, $status)) {
                flash('order_message', 'Order status updated successfully');
            } else {
                flash('order_message', 'Error updating order status', 'alert alert-danger');
            }
            
            redirect('sellers/orderDetails/' . $orderId);
        } else {
            redirect('sellers/orders');
        }
    }
    
    // Inventory management
    public function inventory() {
        // Get seller info
        $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
        
        // Get inventory summary
        $inventory = $this->sellerModel->getInventorySummary($seller->SellerID);
        
        // Get all products for this seller
        $products = $this->sellerModel->getSellerProducts($seller->SellerID);
        
        // Get low stock products
        $lowStock = $this->sellerModel->getLowStockProducts($seller->SellerID);
        
        // Get top selling products
        $topProducts = $this->sellerModel->getTopSellingProducts($seller->SellerID);
        
        $data = [
            'title' => 'Inventory',
            'seller' => $seller,
            'inventory' => $inventory,
            'products' => $products,
            'low_stock' => $lowStock,
            'top_products' => $topProducts
        ];
        
        $this->view('sellers/inventory', $data);
    }
    
    // Update inventory
    public function updateInventory() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get seller info
            $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
            
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $productId = $_POST['product_id'];
            $newStock = intval($_POST['stock_quantity']);
            
            // Get product info
            $product = $this->sellerModel->getProductById($productId);
            
            // Check if product belongs to this seller
            if(!$product || $product->SellerID !== $seller->SellerID) {
                flash('inventory_message', 'Unauthorized access or product not found', 'alert alert-danger');
                redirect('sellers/inventory');
            }
            
            // Prepare data for update
            $data = [
                'product_id' => $productId,
                'seller_id' => $seller->SellerID,
                'product_name' => $product->ProductName,
                'description' => $product->Description,
                'price' => $product->Price,
                'stock_quantity' => $newStock,
                'category' => $product->Category
            ];
            
            // Update product stock
            if($this->sellerModel->updateProduct($data)) {
                flash('inventory_message', 'Inventory updated successfully');
            } else {
                flash('inventory_message', 'Error updating inventory', 'alert alert-danger');
            }
            
            redirect('sellers/inventory');
        } else {
            redirect('sellers/inventory');
        }
    }
    
    // Product inquiries management
    public function inquiries() {
        // Get seller info
        $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
        
        // Get all inquiries for this seller's products
        $inquiries = $this->sellerModel->getProductInquiries($seller->SellerID);
        
        $data = [
            'title' => 'Product Inquiries',
            'seller' => $seller,
            'inquiries' => $inquiries
        ];
        
        $this->view('sellers/inquiries', $data);
    }
    
    // Answer inquiry
    public function answerInquiry($id = null) {
        // Get seller info
        $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $inquiryId = isset($_POST['inquiry_id']) ? $_POST['inquiry_id'] : $id;
            $response = trim($_POST['response']);
            
            if(empty($response)) {
                flash('inquiry_message', 'Please provide a response', 'alert alert-danger');
                redirect('sellers/inquiries');
            }
            
            // Update inquiry
            if($this->sellerModel->answerInquiry($inquiryId, $response)) {
                flash('inquiry_message', 'Inquiry answered successfully');
            } else {
                flash('inquiry_message', 'Error answering inquiry', 'alert alert-danger');
            }
            
            redirect('sellers/inquiries');
        } else {
            redirect('sellers/inquiries');
        }
    }
    
    // Delivery management
    public function delivery() {
        // Get seller info
        $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
        
        // Get orders with status "Processing" or "Shipped"
        $this->db->query('SELECT DISTINCT o.*, u.Username, u.Email, sd.ShippingAddress, sd.ContactPhone, sd.TrackingNumber 
                         FROM orders o
                         JOIN orders_items oi ON o.OrderID = oi.OrderID
                         JOIN marketplace_inventory p ON oi.ProductID = p.ProductID
                         JOIN users u ON o.UserID = u.UserID
                         LEFT JOIN shipping_details sd ON o.OrderID = sd.OrderID
                         WHERE p.SellerID = :sellerId
                         AND (o.Status = "Processing" OR o.Status = "Shipped")
                         ORDER BY o.OrderDate DESC');
        
        $this->db->bind(':sellerId', $seller->SellerID);
        
        $pendingDeliveries = $this->db->resultSet();
        
        $data = [
            'title' => 'Delivery Management',
            'seller' => $seller,
            'pending_deliveries' => $pendingDeliveries
        ];
        
        $this->view('sellers/delivery', $data);
    }
    
    // Update tracking number
    public function updateTracking() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get seller info
            $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
            
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $orderId = $_POST['order_id'];
            $trackingNumber = trim($_POST['tracking_number']);
            
            // Check if order has items from this seller
            $orderItems = $this->sellerModel->getSellerOrderItems($orderId, $seller->SellerID);
            
            if(empty($orderItems)) {
                flash('delivery_message', 'No items in this order belong to your products', 'alert alert-danger');
                redirect('sellers/delivery');
            }
            
            // Update tracking number and set status to Shipped
            $this->db->query('UPDATE shipping_details 
                             SET TrackingNumber = :trackingNumber
                             WHERE OrderID = :orderId');
            
            $this->db->bind(':trackingNumber', $trackingNumber);
            $this->db->bind(':orderId', $orderId);
            
            if($this->db->execute()) {
                // Update order status to Shipped
                $this->orderModel->updateOrderStatus($orderId, 'Shipped');
                
                flash('delivery_message', 'Tracking information updated successfully');
            } else {
                flash('delivery_message', 'Error updating tracking information', 'alert alert-danger');
            }
            
            redirect('sellers/delivery');
        } else {
            redirect('sellers/delivery');
        }
    }


    //payments methods


// Bank payment verification page
public function bankPayments() {
    // Get seller info
    $seller = $this->sellerModel->getSellerByUserId($_SESSION['user_id']);
    
    // Load bank payment model
    $bankPaymentModel = $this->model('BankPayment');
    
    // Get pending bank payments
    $pendingPayments = $bankPaymentModel->getPendingPayments();
    
    $data = [
        'title' => 'Bank Payment Verification',
        'seller' => $seller,
        'payments' => $pendingPayments
    ];
    
    $this->view('sellers/bank_payments', $data);
}

}