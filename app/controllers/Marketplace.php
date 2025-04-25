<?php
class Marketplace extends Controller {
    private $productModel;
    private $userModel;
    private $donorModel;
    private $orderModel;
    private $sellerModel;
    private $db;


    private $paymentModel;

    public function __construct() {
        // Load models
        $this->productModel = $this->model('Product');
        $this->orderModel = $this->model('Order');
        $this->sellerModel = $this->model('Seller');
        $this->orderModel = $this->model('Order');
        $this->paymentModel = $this->model('Payment');
        $this->userModel = $this->model('User');
        
        
        // Initialize database connection
        $this->db = new Database();
    }

    // Main marketplace index page - can be accessed by anyone
    public function index() {
        // Get only 3 recent products for homepage
        $recentProducts = $this->productModel->getRecentProducts(3);
        
        // Get categories with counts
        $categories = $this->productModel->getCategories();

        $totalArtisans = $this->sellerModel->getTotalSellers();
        $totalProducts = $this->productModel->getTotalProductsCount();
        $totalRevenue = $this->orderModel->getTotalRevenue();

        $data = [
            'title' => 'Hopefull Marketplace',
            'description' => 'Support artisans with disabilities by purchasing their handcrafted products',
            'recentProducts' => $recentProducts,
            'categories' => $categories,
            'totalArtisans' => $totalArtisans,
            'totalProducts' => $totalProducts,
            'totalRevenue' => $totalRevenue
        ];

        $this->view('marketplace/index', $data);
    }
    
    // All products page with pagination
    public function allProducts() {
        // Default values
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 12; // Products per page
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        
        // Get paginated products
        $products = $this->productModel->getPaginatedProducts($page, $perPage, $category);
        
        // Get total products count for pagination
        $totalProducts = $this->productModel->getTotalProductsCount($category);
        $totalPages = ceil($totalProducts / $perPage);
        
        $data = [
            'title' => $category ? $category . ' Products' : 'All Products',
            'description' => 'Browse all handcrafted products made by artisans with disabilities',
            'products' => $products,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'perPage' => $perPage,
                'totalProducts' => $totalProducts
            ]
        ];

        $this->view('marketplace/allProducts', $data);
    }
    
    // Search products
    public function search() {
        if(isset($_GET['term'])) {
            $term = trim($_GET['term']);
            
            if(!empty($term)) {
                $products = $this->productModel->searchProducts($term);
                
                $data = [
                    'title' => 'Search Results for "' . $term . '"',
                    'description' => 'Search results for "' . $term . '"',
                    'products' => $products,
                    'searchTerm' => $term
                ];
                
                $this->view('marketplace/search', $data);
            } else {
                redirect('marketplace/allProducts');
            }
        } else {
            redirect('marketplace/allProducts');
        }
    }

    // View single product details
    public function product($id = null) {
        if ($id === null) {
            redirect('marketplace/allProducts');
        }

        $product = $this->productModel->getProductById($id);

        if (!$product) {
            flash('product_error', 'Product not found', 'alert alert-danger');
            redirect('marketplace/allProducts');
        }

        $data = [
            'title' => $product->ProductName,
            'product' => $product
        ];

        $this->view('marketplace/product', $data);
    }

    // Add product to cart (requires login)
    public function addToCart() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            // Store intended product in session for redirect after login
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
                $_SESSION['redirect_after_login'] = 'marketplace/product/' . $_POST['product_id'];
            }
            
            flash('login_message', 'Please log in to add items to your cart', 'alert alert-info');
            redirect('users/login');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'product_id' => trim($_POST['product_id']),
                'quantity' => isset($_POST['quantity']) ? intval($_POST['quantity']) : 1,
                'user_id' => $_SESSION['user_id']
            ];

            // Add to cart (implement cart functionality in session)
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            // Check if product already exists in cart
            $productExists = false;
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['product_id'] == $data['product_id']) {
                    $_SESSION['cart'][$key]['quantity'] += $data['quantity'];
                    $productExists = true;
                    break;
                }
            }
            
            // If product doesn't exist in cart, add it
            if (!$productExists) {
                $product = $this->productModel->getProductById($data['product_id']);
                if ($product) {
                    $_SESSION['cart'][] = [
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity'],
                        'name' => $product->ProductName,
                        'price' => $product->Price,
                        'image' => $product->ProductImage ?? 'default.jpg'
                    ];
                }
            }

            flash('cart_message', 'Product added to cart', 'alert alert-success');
            redirect('marketplace/cart');
        } else {
            redirect('marketplace/allProducts');
        }
    }

    // View shopping cart
    public function cart() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            flash('login_message', 'Please log in to view your cart', 'alert alert-info');
            redirect('users/login');
        }

        $data = [
            'title' => 'Your Shopping Cart',
            'cart_items' => isset($_SESSION['cart']) ? $_SESSION['cart'] : []
        ];
        
        // Calculate total
        $data['total'] = 0;
        foreach ($data['cart_items'] as $item) {
            $data['total'] += $item['price'] * $item['quantity'];
        }

        $this->view('marketplace/cart', $data);
    }

    // Remove item from cart
    public function removeFromCart($productId = null) {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        if ($productId !== null && isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['product_id'] == $productId) {
                    unset($_SESSION['cart'][$key]);
                    // Reset array keys
                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                    break;
                }
            }
        }
        
        flash('cart_message', 'Item removed from cart', 'alert alert-success');
        redirect('marketplace/cart');
    }

    // Update cart quantity
    public function updateCart() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
                foreach ($_POST['quantities'] as $productId => $quantity) {
                    $quantity = intval($quantity);
                    if ($quantity <= 0) {
                        $this->removeFromCart($productId);
                        continue;
                    }
                    
                    // Update quantity
                    foreach ($_SESSION['cart'] as $key => $item) {
                        if ($item['product_id'] == $productId) {
                            $_SESSION['cart'][$key]['quantity'] = $quantity;
                            break;
                        }
                    }
                }
            }
            
            flash('cart_message', 'Cart updated successfully', 'alert alert-success');
            redirect('marketplace/cart');
        } else {
            redirect('marketplace/cart');
        }
    }

   /*check out */
    /*public function checkout() {
        $orderId = $_POST['order_id'] ?? null; // Assign a value to $orderId from POST data
        error_log('Processing PayHere payment for order: ' . $orderId);
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            flash('login_message', 'Please log in to checkout', 'alert alert-info');
            redirect('users/login');
        }
    
        // Check if cart is empty
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            flash('cart_message', 'Your cart is empty', 'alert alert-info');
            redirect('marketplace/cart');
        }
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    
            $data = [
                'user_id' => $_SESSION['user_id'],
                'total_amount' => 0,
                'shipping_address' => trim($_POST['shipping_address']),
                'shipping_address_err' => '',
                'payment_method' => trim($_POST['payment_method']),
                'payment_method_err' => '',
                'contact_phone' => trim($_POST['contact_phone'] ?? ''),
                'contact_phone_err' => '',
                'shipping_notes' => trim($_POST['shipping_notes'] ?? '')
            ];
    
            // Calculate total
            $subtotal = 0;
            foreach ($_SESSION['cart'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            // Add shipping cost (Rs. 350)
            $data['total_amount'] = $subtotal + 350;
    
            // Validate inputs
            if (empty($data['shipping_address'])) {
                $data['shipping_address_err'] = 'Please enter shipping address';
            }
    
            if (empty($data['payment_method'])) {
                $data['payment_method_err'] = 'Please select a payment method';
            }
    
            if (empty($data['contact_phone'])) {
                $data['contact_phone_err'] = 'Please enter contact number for delivery';
            } elseif (strlen($data['contact_phone']) < 10) {
                $data['contact_phone_err'] = 'Contact number must be at least 10 characters';
            }
    
            // If no errors, create order
            if (empty($data['shipping_address_err']) && empty($data['payment_method_err']) && empty($data['contact_phone_err'])) {
                // Start transaction
                $this->db->beginTransaction();
                
                try {
                    // Create order
                    $orderData = [
                        'user_id' => $data['user_id'],
                        'total_amount' => $data['total_amount'],
                        'shipping_address' => $data['shipping_address'],
                        'payment_method' => $data['payment_method']
                    ];
                    
                    $orderId = $this->orderModel->createOrder($orderData);
                    
                    if (!$orderId) {
                        throw new Exception("Failed to create order");
                    }
                    
                    // Create shipping record
                    $shippingData = [
                        'order_id' => $orderId,
                        'shipping_address' => $data['shipping_address'],
                        'contact_phone' => $data['contact_phone'],
                        'payment_method' => $data['payment_method'],
                        'shipping_notes' => $data['shipping_notes']
                    ];
                    
                    $shippingId = $this->orderModel->createShippingDetails($shippingData);
                    
                    if (!$shippingId) {
                        throw new Exception("Failed to create shipping record");
                    }
                    
                    // Add order items
                    $orderItemsSuccess = true;
                    foreach ($_SESSION['cart'] as $item) {
                        $orderItem = [
                            'order_id' => $orderId,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price']
                        ];
                        
                        if (!$this->orderModel->addOrderItem($orderItem)) {
                            $orderItemsSuccess = false;
                            error_log("Failed to add item to order: " . json_encode($orderItem));
                            break;
                        }
                    }
                    
                    if (!$orderItemsSuccess) {
                        throw new Exception("Failed to add items to order");
                    }
                    
                    // Everything successful - commit transaction
                    $this->db->commit();
                    
                    // If payment method is PayHere, create a payment record and redirect to PayHere
                    if ($data['payment_method'] == 'payhere') {
                        // Create payment record
                        $paymentData = [
                            'order_id' => $orderId,
                            'payment_amount' => $data['total_amount'],
                            'payment_method' => 'payhere',
                            'status' => 'Pending',
                            'payment_details' => json_encode([
                                'user_id' => $data['user_id']
                            ])
                        ];
                        
                        $paymentId = $this->paymentModel->createPaymentRecord($paymentData);
                        
                        if (!$paymentId) {
                            throw new Exception("Failed to create payment record");
                        }
                        
                        // Store the order ID in session for later use
                        $_SESSION['payhere_order_id'] = $orderId;
                        $_SESSION['payhere_payment_id'] = $paymentId;
                        
                        // Clear cart
                        unset($_SESSION['cart']);
                        
                        // Redirect to PayHere
                        redirect('marketplace/processPayHere/' . $orderId);
                    } else {
                        // For other payment methods, proceed as usual
                        
                        // Clear cart
                        unset($_SESSION['cart']);
                        
                        flash('order_message', 'Order placed successfully', 'alert alert-success');
                        redirect('marketplace/orderConfirmation/' . $orderId);
                    }
                    
                } catch (Exception $e) {
                    // Something went wrong - rollback
                    $this->db->rollBack();
                    error_log("Checkout error: " . $e->getMessage());
                    flash('order_error', 'Something went wrong, please try again', 'alert alert-danger');
                    $this->view('marketplace/checkout', $data);
                }
            } else {
                // Load view with errors
                $this->view('marketplace/checkout', $data);
            }
        } else {
            $data = [
                'title' => 'Checkout',
                'cart_items' => $_SESSION['cart'],
                'total' => 0,
                'shipping_address' => '',
                'shipping_address_err' => '',
                'payment_method' => '',
                'payment_method_err' => '',
                'contact_phone' => '',
                'contact_phone_err' => '',
                'shipping_notes' => ''
            ];
    
            // Calculate total
            foreach ($data['cart_items'] as $item) {
                $data['total'] += $item['price'] * $item['quantity'];
            }
    
            $this->view('marketplace/checkout', $data);
        }
    }*/

    /*public function checkout() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            flash('login_message', 'Please log in to checkout', 'alert alert-info');
            redirect('users/login');
        }
    
        // Check if cart is empty
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            flash('cart_message', 'Your cart is empty', 'alert alert-info');
            redirect('marketplace/cart');
        }
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    
            $data = [
                'user_id' => $_SESSION['user_id'],
                'total_amount' => 0,
                'shipping_address' => trim($_POST['shipping_address']),
                'shipping_address_err' => '',
                'payment_method' => trim($_POST['payment_method']),
                'payment_method_err' => '',
                'contact_phone' => trim($_POST['contact_phone'] ?? ''),
                'contact_phone_err' => '',
                'shipping_notes' => trim($_POST['shipping_notes'] ?? '')
            ];
    
            // Calculate total
            $subtotal = 0;
            foreach ($_SESSION['cart'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            // Add shipping cost (Rs. 350)
            $data['total_amount'] = $subtotal + 350;
    
            // Validate inputs
            if (empty($data['shipping_address'])) {
                $data['shipping_address_err'] = 'Please enter shipping address';
            }
    
            if (empty($data['payment_method'])) {
                $data['payment_method_err'] = 'Please select a payment method';
            }
    
            if (empty($data['contact_phone'])) {
                $data['contact_phone_err'] = 'Please enter contact number for delivery';
            } elseif (strlen($data['contact_phone']) < 10) {
                $data['contact_phone_err'] = 'Contact number must be at least 10 characters';
            }
    
            // If bank transfer payment method, validate payment slip
            $bankPaymentUploaded = false;
            $slipFileName = '';
            if ($data['payment_method'] == 'bank') {
                if (!isset($_FILES['bank_slip']) || $_FILES['bank_slip']['error'] != 0) {
                    $data['payment_method_err'] = 'Please upload your bank transfer slip';
                } else {
                    // File validation logic here (size, type, etc.)
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                    $fileType = $_FILES['bank_slip']['type'];
                    $fileSize = $_FILES['bank_slip']['size'];
                    $maxSize = 5 * 1024 * 1024; // 5MB max size
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        $data['payment_method_err'] = 'Only JPEG, PNG, GIF images and PDF files are allowed';
                    } elseif ($fileSize > $maxSize) {
                        $data['payment_method_err'] = 'File size must be less than 5MB';
                    } else {
                        $bankPaymentUploaded = true;
                    }
                }
            }
    
            // If no errors, create order
            if (empty($data['shipping_address_err']) && empty($data['payment_method_err']) && empty($data['contact_phone_err'])) {
                // Start transaction
                $this->db->beginTransaction();
                
                try {
                    // Create order
                    $orderData = [
                        'user_id' => $data['user_id'],
                        'total_amount' => $data['total_amount'],
                        'shipping_address' => $data['shipping_address'],
                        'payment_method' => $data['payment_method']
                    ];
                    
                    $orderId = $this->orderModel->createOrder($orderData);
                    
                    if (!$orderId) {
                        throw new Exception("Failed to create order");
                    }
                    
                    // Create shipping record
                    $shippingData = [
                        'order_id' => $orderId,
                        'shipping_address' => $data['shipping_address'],
                        'contact_phone' => $data['contact_phone'],
                        'payment_method' => $data['payment_method'],
                        'shipping_notes' => $data['shipping_notes']
                    ];
                    
                    $shippingId = $this->orderModel->createShippingDetails($shippingData);
                    
                    if (!$shippingId) {
                        throw new Exception("Failed to create shipping record");
                    }
                    
                    // Add order items
                    $orderItemsSuccess = true;
                    foreach ($_SESSION['cart'] as $item) {
                        $orderItem = [
                            'order_id' => $orderId,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price']
                        ];
                        
                        if (!$this->orderModel->addOrderItem($orderItem)) {
                            $orderItemsSuccess = false;
                            error_log("Failed to add item to order: " . json_encode($orderItem));
                            break;
                        }
                    }
                    
                    if (!$orderItemsSuccess) {
                        throw new Exception("Failed to add items to order");
                    }
                    
                    // Handle bank payment slip upload if applicable
                    if ($data['payment_method'] == 'bank' && $bankPaymentUploaded) {
                        // Get file extension
                        $fileExt = pathinfo($_FILES['bank_slip']['name'], PATHINFO_EXTENSION);
                        
                        // Create new filename based on order ID
                        $newFileName = $orderId . '.' . $fileExt;
                        
                        // Set upload directory
                        $slipsDir = UPLOADS_PATH . '/slips';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($slipsDir)) {
                            mkdir($slipsDir, 0777, true);
                        }
                        
                        // Set file destination
                        $destination = $slipsDir . '/' . $newFileName;
                        
                        // Upload file
                        if (move_uploaded_file($_FILES['bank_slip']['tmp_name'], $destination)) {
                            // Create bank payment record
                            $bankPaymentModel = $this->model('BankPayment');
                            $bankPaymentData = [
                                'order_id' => $orderId,
                                'slip_file' => $newFileName
                            ];
                            
                            if (!$bankPaymentModel->createBankPayment($bankPaymentData)) {
                                error_log("Failed to create bank payment record");
                                // Continue anyway as the file is uploaded
                            }
                            
                            $slipFileName = $newFileName;
                        } else {
                            error_log("Failed to move uploaded file");
                            // Continue anyway as the order is created
                        }
                    }
                    
                    // Everything successful - commit transaction
                    $this->db->commit();
                    
                    // If payment method is PayHere, create a payment record and redirect to PayHere
                    if ($data['payment_method'] == 'payhere') {
                        // Create payment record
                        $paymentData = [
                            'order_id' => $orderId,
                            'payment_amount' => $data['total_amount'],
                            'payment_method' => 'payhere',
                            'status' => 'Pending',
                            'payment_details' => json_encode([
                                'user_id' => $data['user_id']
                            ])
                        ];
                        
                        $paymentId = $this->paymentModel->createPaymentRecord($paymentData);
                        
                        if (!$paymentId) {
                            throw new Exception("Failed to create payment record");
                        }
                        
                        // Store the order ID in session for later use
                        $_SESSION['payhere_order_id'] = $orderId;
                        $_SESSION['payhere_payment_id'] = $paymentId;
                        
                        // Clear cart
                        unset($_SESSION['cart']);
                        
                        // Redirect to PayHere
                        redirect('marketplace/processPayHere/' . $orderId);
                    } else {
                        // For other payment methods, proceed as usual
                        
                        // Clear cart
                        unset($_SESSION['cart']);
                        
                        flash('order_message', 'Order placed successfully', 'alert alert-success');
                        redirect('marketplace/orderConfirmation/' . $orderId);
                    }
                    
                } catch (Exception $e) {
                    // Something went wrong - rollback
                    $this->db->rollBack();
                    error_log("Checkout error: " . $e->getMessage());
                    flash('order_error', 'Something went wrong, please try again', 'alert alert-danger');
                    $this->view('marketplace/checkout', $data);
                }
            } else {
                // Load view with errors
                $this->view('marketplace/checkout', $data);
            }
        } else {
            $data = [
                'title' => 'Checkout',
                'cart_items' => $_SESSION['cart'],
                'total' => 0,
                'shipping_address' => '',
                'shipping_address_err' => '',
                'payment_method' => '',
                'payment_method_err' => '',
                'contact_phone' => '',
                'contact_phone_err' => '',
                'shipping_notes' => ''
            ];
    
            // Calculate total
            foreach ($data['cart_items'] as $item) {
                $data['total'] += $item['price'] * $item['quantity'];
            }
    
            $this->view('marketplace/checkout', $data);
        }
    }*/

    public function checkout() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            flash('login_message', 'Please log in to checkout', 'alert alert-info');
            redirect('users/login');
        }
    
        // Check if cart is empty
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            flash('cart_message', 'Your cart is empty', 'alert alert-info');
            redirect('marketplace/cart');
        }
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    
            $data = [
                'user_id' => $_SESSION['user_id'],
                'total_amount' => 0,
                'shipping_address' => trim($_POST['shipping_address']),
                'shipping_address_err' => '',
                'payment_method' => trim($_POST['payment_method']),
                'payment_method_err' => '',
                'contact_phone' => trim($_POST['contact_phone'] ?? ''),
                'contact_phone_err' => '',
                'shipping_notes' => trim($_POST['shipping_notes'] ?? '')
            ];
    
            // Calculate total
            $subtotal = 0;
            foreach ($_SESSION['cart'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            // Add shipping cost (Rs. 350)
            $data['total_amount'] = $subtotal + 350;
    
            // Validate inputs
            if (empty($data['shipping_address'])) {
                $data['shipping_address_err'] = 'Please enter shipping address';
            }
    
            if (empty($data['payment_method'])) {
                $data['payment_method_err'] = 'Please select a payment method';
            }
    
            if (empty($data['contact_phone'])) {
                $data['contact_phone_err'] = 'Please enter contact number for delivery';
            } elseif (strlen($data['contact_phone']) < 10) {
                $data['contact_phone_err'] = 'Contact number must be at least 10 characters';
            }
    
            // If bank transfer payment method, validate payment slip
            $bankPaymentUploaded = false;
            $slipFileName = '';
            if ($data['payment_method'] == 'bank') {
                if (!isset($_FILES['bank_slip']) || $_FILES['bank_slip']['error'] != 0) {
                    $data['payment_method_err'] = 'Please upload your bank transfer slip';
                } else {
                    // File validation logic
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                    $fileType = $_FILES['bank_slip']['type'];
                    $fileSize = $_FILES['bank_slip']['size'];
                    $maxSize = 5 * 1024 * 1024; // 5MB max size
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        $data['payment_method_err'] = 'Only JPEG, PNG, GIF images and PDF files are allowed';
                    } elseif ($fileSize > $maxSize) {
                        $data['payment_method_err'] = 'File size must be less than 5MB';
                    } else {
                        $bankPaymentUploaded = true;
                    }
                }
            }
    
            // If no errors, create order
            if (empty($data['shipping_address_err']) && empty($data['payment_method_err']) && empty($data['contact_phone_err'])) {
                // Start transaction
                $this->db->beginTransaction();
                
                try {
                    // Create order
                    $orderData = [
                        'user_id' => $data['user_id'],
                        'total_amount' => $data['total_amount'],
                        'shipping_address' => $data['shipping_address'],
                        'payment_method' => $data['payment_method']
                    ];
                    
                    $orderId = $this->orderModel->createOrder($orderData);
                    
                    if (!$orderId) {
                        throw new Exception("Failed to create order");
                    }
                    
                    // Create shipping record
                    $shippingData = [
                        'order_id' => $orderId,
                        'shipping_address' => $data['shipping_address'],
                        'contact_phone' => $data['contact_phone'],
                        'payment_method' => $data['payment_method'],
                        'shipping_notes' => $data['shipping_notes']
                    ];
                    
                    $shippingId = $this->orderModel->createShippingDetails($shippingData);
                    
                    if (!$shippingId) {
                        throw new Exception("Failed to create shipping record");
                    }
                    
                    // Add order items
                    $orderItemsSuccess = true;
                    foreach ($_SESSION['cart'] as $item) {
                        $orderItem = [
                            'order_id' => $orderId,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price']
                        ];
                        
                        if (!$this->orderModel->addOrderItem($orderItem)) {
                            $orderItemsSuccess = false;
                            error_log("Failed to add item to order: " . json_encode($orderItem));
                            break;
                        }
                    }
                    
                    if (!$orderItemsSuccess) {
                        throw new Exception("Failed to add items to order");
                    }
                    
                    // Handle bank payment slip upload if applicable
                    if ($data['payment_method'] == 'bank' && $bankPaymentUploaded) {
                        // Get file extension
                        $fileExt = pathinfo($_FILES['bank_slip']['name'], PATHINFO_EXTENSION);
                        
                        // Create new filename based on order ID
                        $newFileName = $orderId . '.' . $fileExt;
                        
                        // Set upload directory
                        $slipsDir = ROOT_PATH . '/../public/uploads/slips';
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($slipsDir)) {
                            mkdir($slipsDir, 0755, true);
                        }
                        
                        // Set file destination
                        $destination = $slipsDir . '/' . $newFileName;
                        
                        // Upload file
                        if (move_uploaded_file($_FILES['bank_slip']['tmp_name'], $destination)) {
                            // Create bank payment record
                            $bankPaymentModel = $this->model('BankPayment');
                            $bankPaymentData = [
                                'order_id' => $orderId,
                                'slip_file' => $newFileName
                            ];
                            
                            if (!$bankPaymentModel->createBankPayment($bankPaymentData)) {
                                error_log("Failed to create bank payment record");
                                // Continue anyway as the file is uploaded
                            }
                            
                            $slipFileName = $newFileName;
                        } else {
                            error_log("Failed to move uploaded file");
                            // Continue anyway as the order is created
                        }
                    }
                    
                    // Everything successful - commit transaction
                    $this->db->commit();
                    
                    // If payment method is PayHere, create a payment record and redirect to PayHere
                    if ($data['payment_method'] == 'payhere') {
                        // Create payment record
                        $paymentData = [
                            'order_id' => $orderId,
                            'payment_amount' => $data['total_amount'],
                            'payment_method' => 'payhere',
                            'status' => 'Pending',
                            'payment_details' => json_encode([
                                'user_id' => $data['user_id']
                            ])
                        ];
                        
                        $paymentId = $this->paymentModel->createPaymentRecord($paymentData);
                        
                        if (!$paymentId) {
                            throw new Exception("Failed to create payment record");
                        }
                        
                        // Store the order ID in session for later use
                        $_SESSION['payhere_order_id'] = $orderId;
                        $_SESSION['payhere_payment_id'] = $paymentId;
                        
                        // Clear cart
                        unset($_SESSION['cart']);
                        
                        // Redirect to PayHere
                        redirect('marketplace/processPayHere/' . $orderId);
                    } else {
                        // For other payment methods, proceed as usual
                        
                        // Clear cart
                        unset($_SESSION['cart']);
                        
                        flash('order_message', 'Order placed successfully', 'alert alert-success');
                        redirect('marketplace/orderConfirmation/' . $orderId);
                    }
                    
                } catch (Exception $e) {
                    // Something went wrong - rollback
                    $this->db->rollBack();
                    error_log("Checkout error: " . $e->getMessage());
                    flash('order_error', 'Something went wrong, please try again', 'alert alert-danger');
                    $this->view('marketplace/checkout', $data);
                }
            } else {
                // Load view with errors
                $this->view('marketplace/checkout', $data);
            }
        } else {
            $data = [
                'title' => 'Checkout',
                'cart_items' => $_SESSION['cart'],
                'total' => 0,
                'shipping_address' => '',
                'shipping_address_err' => '',
                'payment_method' => '',
                'payment_method_err' => '',
                'contact_phone' => '',
                'contact_phone_err' => '',
                'shipping_notes' => ''
            ];
    
            // Calculate total
            foreach ($data['cart_items'] as $item) {
                $data['total'] += $item['price'] * $item['quantity'];
            }
    
            $this->view('marketplace/checkout', $data);
        }
    }

   
    
   
    // Process PayHere Payment
    public function processPayHere($orderId) {

        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }
        
        // Check if it's a valid order
        $order = $this->orderModel->getOrderById($orderId);
        
        if (!$order || $order->UserID != $_SESSION['user_id']) {
            flash('order_error', 'Invalid order', 'alert alert-danger');
            redirect('marketplace/orders');
        }
        
        // Get user information
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        // Get shipping details
        $shipping = $this->orderModel->getShippingDetails($orderId);
        
        // Prepare data for PayHere
        $data = [
            'title' => 'Processing Payment',
            'order' => $order,
            'user' => $user,
            'shipping' => $shipping,
            'merchant_id' => PAYHERE_MERCHANT_ID,
            'return_url' => PAYHERE_RETURN_URL,
            'cancel_url' => PAYHERE_CANCEL_URL,
            'notify_url' => PAYHERE_NOTIFY_URL,
            'sandbox' => PAYHERE_SANDBOX
        ];
        
        $this->view('marketplace/process_payhere', $data);
    }
    
    // PayHere Payment Success
    /*public function paymentSuccess() {
        // Handle return from PayHere after successful payment
        error_log('PayHere payment success callback triggered. Data: ' . json_encode($_POST));
    error_log('PayHere success $_SESSION: ' . json_encode($_SESSION));
        // Check if there's a PayHere order in session
        if (!isset($_SESSION['payhere_order_id'])) {
            redirect('marketplace/orders');
        }
        
        $orderId = $_SESSION['payhere_order_id'];
        
        // Clear the PayHere session variables
        unset($_SESSION['payhere_order_id']);
        unset($_SESSION['payhere_payment_id']);
        
        // Update order status to Processing since payment is successful
        $this->orderModel->updateOrderStatus($orderId, 'Processing');
        
        flash('order_message', 'Payment successful! Your order is being processed.', 'alert alert-success');
        redirect('marketplace/orderConfirmation/' . $orderId);
    }
    
    // PayHere Payment Cancelled
    public function paymentCancelled() {
        // Handle return from PayHere after cancelled payment
        
        // Check if there's a PayHere order in session
        if (!isset($_SESSION['payhere_order_id'])) {
            redirect('marketplace/orders');
        }
        
        $orderId = $_SESSION['payhere_order_id'];
        
        // Clear the PayHere session variables
        unset($_SESSION['payhere_order_id']);
        unset($_SESSION['payhere_payment_id']);
        
        flash('order_message', 'Payment was cancelled. You can try again later.', 'alert alert-warning');
        redirect('marketplace/orderDetails/' . $orderId);
    }*/

    public function paymentSuccess($orderId = null) {
        // Log information for debugging
        error_log('PayHere payment success callback triggered. OrderID: ' . $orderId);
        error_log('PayHere success $_SESSION: ' . json_encode($_SESSION));
        
        // Check if order ID is provided via URL parameter
        if (!$orderId && isset($_SESSION['payhere_order_id'])) {
            // Fall back to session variable if URL parameter is not provided
            $orderId = $_SESSION['payhere_order_id'];
        }
        
        // Redirect to orders page if no order ID is available
        if (!$orderId) {
            flash('order_error', 'Order information is missing', 'alert alert-danger');
            redirect('marketplace/orders');
        }
        
        // Get the payment record for this order
        $payment = $this->paymentModel->getPaymentByOrderId($orderId);
        
        // If payment record exists, update its status
        if ($payment) {
            // Update payment status to Completed
            $this->paymentModel->updatePaymentStatus(
                $payment->ID,
                'Completed',
                null,
                'Updated via return URL'
            );
            error_log("Updated payment ID {$payment->ID} to Completed");
        } else {
            error_log("No payment record found for order: {$orderId}");
        }
        
        // Update order status to Processing since payment is successful
        $this->orderModel->updateOrderStatus($orderId, 'Processing');
        error_log("Updated order {$orderId} to Processing");
        
        // Clear the PayHere session variables if they exist
        if (isset($_SESSION['payhere_order_id'])) {
            unset($_SESSION['payhere_order_id']);
        }
        if (isset($_SESSION['payhere_payment_id'])) {
            unset($_SESSION['payhere_payment_id']);
        }
        
        // Flash success message and redirect to order confirmation
        flash('order_message', 'Payment successful! Your order is being processed.', 'alert alert-success');
        redirect('marketplace/orderConfirmation/' . $orderId);
    }
    
    // PayHere Payment Notification
    public function paymentNotify() {
        // This endpoint will receive server-to-server notifications from PayHere
        error_log('PayHere payment notification callback triggered. Data: ' . json_encode($_POST));
        // This should be accessible without a session, as PayHere servers will call it
        
        // Get the POST data
        $data = $_POST;
        
        // Log the notification
        error_log('PayHere Notification: ' . json_encode($data));
        
        // Verify the payment
        if (isset($data['merchant_id']) && $data['merchant_id'] == PAYHERE_MERCHANT_ID) {
            // Extract order ID from the merchant-specific data
            $orderId = $data['order_id'] ?? null;
            
            if ($orderId) {
                $order = $this->orderModel->getOrderById($orderId);
                
                if ($order) {
                    // Verify the payment status
                    if ($data['status_code'] == '2') { // 2 = Success
                        // Update the order status
                        $this->orderModel->updateOrderStatus($orderId, 'Processing');
                        
                        // Update the payment record
                        $payment = $this->paymentModel->getPaymentByOrderId($orderId);
                        
                        if ($payment) {
                            $this->paymentModel->updatePaymentStatus(
                                $payment->ID,
                                'Completed',
                                $data['payment_id'] ?? null,
                                json_encode($data)
                            );
                        }
                        
                        // Return success response
                        http_response_code(200);
                        echo 'Payment verified';
                    } else {
                        // Payment failed
                        // Update the payment record
                        $payment = $this->paymentModel->getPaymentByOrderId($orderId);
                        
                        if ($payment) {
                            $this->paymentModel->updatePaymentStatus(
                                $payment->ID,
                                'Failed',
                                $data['payment_id'] ?? null,
                                json_encode($data)
                            );
                        }
                        
                        // Return error response
                        http_response_code(400);
                        echo 'Payment failed';
                    }
                } else {
                    // Order not found
                    http_response_code(404);
                    echo 'Order not found';
                }
            } else {
                // Invalid data
                http_response_code(400);
                echo 'Invalid data';
            }
        } else {
            // Invalid merchant
            http_response_code(403);
            echo 'Invalid merchant';
        }
        
        exit;
    }

   

       

    // Order confirmation
    public function orderConfirmation($orderId = null) {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        if ($orderId === null) {
            redirect('marketplace/allProducts');
        }

        $order = $this->orderModel->getOrderById($orderId);
        $orderItems = $this->orderModel->getOrderItems($orderId);

        if (!$order || $order->UserID != $_SESSION['user_id']) {
            flash('order_error', 'Order not found', 'alert alert-danger');
            redirect('marketplace/allProducts');
        }

        $data = [
            'title' => 'Order Confirmation',
            'order' => $order,
            'order_items' => $orderItems
        ];

        $this->view('marketplace/orderConfirmation', $data);
    }

    // View order history (for logged in users)
    public function orders() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            flash('login_message', 'Please log in to view your orders', 'alert alert-info');
            redirect('users/login');
        }

        $orders = $this->orderModel->getUserOrders($_SESSION['user_id']);

        $data = [
            'title' => 'Your Orders',
            'orders' => $orders
        ];

        $this->view('marketplace/orders', $data);
    }

   
    
    // Track an order
    public function track() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            flash('login_message', 'Please log in to track your orders', 'alert alert-info');
            redirect('users/login');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form submission
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $orderNumber = trim($_POST['order_number']);
            
            if (!empty($orderNumber)) {
                $order = $this->orderModel->getOrderById($orderNumber);
                
                if ($order && $order->UserID == $_SESSION['user_id']) {
                    redirect('marketplace/orderDetails/' . $orderNumber);
                } else {
                    flash('track_error', 'Order not found or does not belong to you', 'alert alert-danger');
                    redirect('marketplace/track');
                }
            } else {
                flash('track_error', 'Please enter an order number', 'alert alert-danger');
                redirect('marketplace/track');
            }
        }

        $data = [
            'title' => 'Track Your Order'
        ];

        $this->view('marketplace/track', $data);
    }
    
   

// Product inquiries
public function inquiries() {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        flash('login_message', 'Please log in to submit inquiries', 'alert alert-info');
        redirect('users/login');
    }

    // Load the inquiry model
    $inquiryModel = $this->model('Inquiry');
    
    // Load the product model to get actual products
    $productModel = $this->productModel;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Process form submission
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        $data = [
            'product_id' => trim($_POST['product_id']),
            'user_id' => $_SESSION['user_id'],
            'message' => trim($_POST['message']),
            'product_id_err' => '',
            'message_err' => '',
            'products' => $productModel->getAllProducts(),
            'inquiries' => $inquiryModel->getUserInquiries($_SESSION['user_id'])
        ];
        
        // Validate product ID
        if (empty($data['product_id'])) {
            $data['product_id_err'] = 'Please select a product';
        } else {
            // Check if product exists
            $product = $productModel->getProductById($data['product_id']);
            if (!$product) {
                $data['product_id_err'] = 'Selected product does not exist';
            }
        }
        
        // Validate message
        if (empty($data['message'])) {
            $data['message_err'] = 'Please enter your inquiry';
        }
        
        // If no errors, save inquiry
        if (empty($data['product_id_err']) && empty($data['message_err'])) {
            if ($inquiryModel->createInquiry($data)) {
                flash('inquiry_message', 'Your inquiry has been submitted. We will get back to you soon.', 'alert alert-success');
                redirect('marketplace/inquiries');
            } else {
                flash('inquiry_message', 'Something went wrong. Please try again.', 'alert alert-danger');
                $this->view('marketplace/inquiries', $data);
            }
        } else {
            // Load view with errors
            $this->view('marketplace/inquiries', $data);
        }
    } else {
        // Load page with empty form and existing inquiries
        $data = [
            'product_id' => '',
            'message' => '',
            'product_id_err' => '',
            'message_err' => '',
            'products' => $productModel->getAllProducts(),
            'inquiries' => $inquiryModel->getUserInquiries($_SESSION['user_id'])
        ];

        $this->view('marketplace/inquiries', $data);
    }
}
   


// Request order cancellation
public function requestCancellation() {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        redirect('users/login');
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Process form
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        $data = [
            'order_id' => trim($_POST['order_id']),
            'user_id' => $_SESSION['user_id'],
            'reason' => trim($_POST['reason']),
            'reason_err' => ''
        ];
        
        // Validate reason
        if (empty($data['reason'])) {
            $data['reason_err'] = 'Please provide a reason for cancellation';
        }
        
        // Get order to validate it belongs to user and is in cancellable state
        $order = $this->orderModel->getOrderById($data['order_id']);
        
        if (!$order || $order->UserID != $_SESSION['user_id']) {
            flash('order_error', 'Invalid order or permission denied', 'alert alert-danger');
            redirect('marketplace/orders');
        }
        
        // Check if cancellation is allowed based on order status
        if (!$this->orderModel->isCancellationAllowed($data['order_id'])) {
            flash('order_error', 'This order cannot be cancelled in its current state', 'alert alert-danger');
            redirect('marketplace/orderDetails/' . $data['order_id']);
        }
        
        // Check if there's already a pending cancellation request
        if ($this->orderModel->hasPendingCancellation($data['order_id'])) {
            flash('order_message', 'A cancellation request for this order is already pending', 'alert alert-info');
            redirect('marketplace/orderDetails/' . $data['order_id']);
        }
        
        // If no errors, submit cancellation request
        if (empty($data['reason_err'])) {
            if ($this->orderModel->requestCancellation($data)) {
                flash('order_message', 'Cancellation request submitted. You will be notified once it has been processed.', 'alert alert-success');
                redirect('marketplace/orderDetails/' . $data['order_id']);
            } else {
                flash('order_error', 'Something went wrong. Please try again.', 'alert alert-danger');
                redirect('marketplace/orderDetails/' . $data['order_id']);
            }
        } else {
            // Load view with errors
            $order = $this->orderModel->getOrderById($data['order_id']);
            $orderItems = $this->orderModel->getOrderItems($data['order_id']);
            
            $viewData = [
                'title' => 'Order Details',
                'order' => $order,
                'order_items' => $orderItems,
                'cancellation' => $data
            ];
            
            $this->view('marketplace/orderDetails', $viewData);
        }
    } else {
        redirect('marketplace/orders');
    }
}

// Get updated order details
/*public function orderDetails($orderId = null) {
    // Existing code from your function...
    
    // Adding cancellation request information
    $order = $this->orderModel->getOrderById($orderId);
    $orderItems = $this->orderModel->getOrderItems($orderId);
    $cancellationRequest = $this->orderModel->getCancellationRequest($orderId);

     // Get payment information based on payment method
     if ($order->PaymentMethod == 'bank') {
        $bankPaymentModel = $this->model('BankPayment');
        $bankPayment = $bankPaymentModel->getPaymentByOrderId($orderId);
        $data['bank_payment'] = $bankPayment;
    }
    
    // Load the view with data
    $this->view('marketplace/orderDetails', [
        'title' => 'Order Details',
        'order' => $order,
        'order_items' => $orderItems,
        'cancellation_request' => $cancellationRequest,
        'cancellation_allowed' => $this->orderModel->isCancellationAllowed($orderId)
    ]);
}*/


public function orderDetails($orderId = null) {
    // Redirect if no order ID provided
    if (!$orderId) {
        redirect('marketplace/orders');
    }
    
    // Make sure user is logged in by checking session
    if (!isset($_SESSION['user_id'])) {
        redirect('users/login');
    }
    
    // Fetch the order details with shipping information
    $order = $this->orderModel->getOrderById($orderId);
    
    // If order doesn't exist or doesn't belong to the current user, redirect
    if (!$order || $order->UserID != $_SESSION['user_id']) {
        flash('order_error', 'Order not found or access denied', 'alert alert-danger');
        redirect('marketplace/orders');
    }
    
    // Fetch order items with product details
    $orderItems = $this->orderModel->getOrderItems($orderId);
    
    // Get cancellation request information if exists
    $cancellationRequest = $this->orderModel->getCancellationRequest($orderId);
    
    // Get payment information based on payment method
    $data = [
        'title' => 'Order Details',
        'order' => $order,
        'order_items' => $orderItems,
        'cancellation_request' => $cancellationRequest,
        'cancellation_allowed' => $this->orderModel->isCancellationAllowed($orderId)
    ];
    
    // Load bank payment data if payment method is bank transfer
    if ($order->PaymentMethod == 'bank') {
        $bankPaymentModel = $this->model('BankPayment');
        $bankPayment = $bankPaymentModel->getPaymentByOrderId($orderId);
        $data['bank_payment'] = $bankPayment;
    }
    
    // Load the view with data
    $this->view('marketplace/orderDetails', $data);
}


/*public function orderDetails($orderId = null) {
    // Redirect if no order ID provided
    if (!$orderId) {
        redirect('marketplace/orders');
    }
    
    // Make sure user is logged in by checking session
    if (!isset($_SESSION['user_id'])) {
        redirect('users/login');
    }
    
    // Initialize the Order model if not already loaded
    if (!isset($this->orderModel)) {
        $this->orderModel = $this->model('Order');
    }
    
    // Fetch the order details with shipping information
    $order = $this->orderModel->getOrderById($orderId);
    
    // If order doesn't exist or doesn't belong to the current user, redirect
    if (!$order || $order->UserID != $_SESSION['user_id']) {
        flash('order_error', 'Order not found or access denied', 'alert alert-danger');
        redirect('marketplace/orders');
    }
    
    // Fetch order items with product details
    $orderItems = $this->orderModel->getOrderItems($orderId);
    
    // Load the view with data
    $this->view('marketplace/orderDetails', [
        'title' => 'Order Details',
        'order' => $order,
        'order_items' => $orderItems
    ]);
}*/




// Wishlist functionality
public function wishlist() {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        flash('login_message', 'Please log in to view your wishlist', 'alert alert-info');
        redirect('users/login');
    }

    // Get wishlist items from session
    $wishlistItems = [];
    if (isset($_SESSION['wishlist']) && !empty($_SESSION['wishlist'])) {
        foreach ($_SESSION['wishlist'] as $productId) {
            $product = $this->productModel->getProductById($productId);
            if ($product) {
                $wishlistItems[] = $product;
            }
        }
    }

    $data = [
        'title' => 'Your Wishlist',
        'wishlistItems' => $wishlistItems
    ];

    $this->view('marketplace/wishlist', $data);
}

// Add to wishlist
public function addToWishlist($productId = null) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Store intended product in session for redirect after login
        if ($productId) {
            $_SESSION['redirect_after_login'] = 'marketplace/product/' . $productId;
        }
        
        flash('login_message', 'Please log in to add items to your wishlist', 'alert alert-info');
        redirect('users/login');
    }

    if ($productId) {
        // Initialize wishlist session array if it doesn't exist
        if (!isset($_SESSION['wishlist'])) {
            $_SESSION['wishlist'] = [];
        }
        
        // Check if product already exists in wishlist
        if (!in_array($productId, $_SESSION['wishlist'])) {
            // Add product to wishlist
            $_SESSION['wishlist'][] = $productId;
            flash('wishlist_message', 'Product added to your wishlist', 'alert alert-success');
        } else {
            flash('wishlist_message', 'Product is already in your wishlist', 'alert alert-info');
        }
    }

    // Redirect to wishlist page
    redirect('marketplace/wishlist');
}


// Remove from wishlist
public function removeFromWishlist($productId = null) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        redirect('users/login');
    }

    if ($productId !== null && isset($_SESSION['wishlist'])) {
        // Find and remove the product from wishlist
        $key = array_search($productId, $_SESSION['wishlist']);
        if ($key !== false) {
            unset($_SESSION['wishlist'][$key]);
            // Reset array keys
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
            flash('wishlist_message', 'Item removed from wishlist', 'alert alert-success');
        }
    }
    
    redirect('marketplace/wishlist');
}



    
}