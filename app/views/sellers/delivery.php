<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-delivery">
    <?php flash('delivery_message'); ?>
    
    <div class="delivery-header">
        <h2><i class="fas fa-truck"></i> Delivery Management</h2>
        
        <div class="filters">
            <div class="search-container">
                <input type="text" id="deliverySearch" placeholder="Search by Order ID or Customer..." class="search-input">
                <i class="fas fa-search"></i>
            </div>
            <div class="filter-container">
                <select id="statusFilter" class="filter-select">
                    <option value="">All Status</option>
                    <option value="Processing">Processing</option>
                    <option value="Shipped">Shipped</option>
                </select>
            </div>
        </div>
    </div>
    
    <?php if(empty($data['pending_deliveries'])): ?>
        <div class="no-deliveries">
            <div class="no-data-message">
                <i class="fas fa-shipping-fast"></i>
                <h3>No Pending Deliveries</h3>
                <p>There are no orders waiting to be shipped at the moment.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="delivery-cards-container">
            <?php foreach($data['pending_deliveries'] as $delivery): ?>
                <div class="delivery-card" data-order-id="<?php echo $delivery->OrderID; ?>" data-customer="<?php echo $delivery->Username; ?>" data-status="<?php echo $delivery->Status; ?>">
                    <div class="delivery-header">
                        <div class="delivery-order-info">
                            <h3>Order #<?php echo $delivery->OrderID; ?></h3>
                            <span class="status-badge status-<?php echo strtolower($delivery->Status); ?>"><?php echo $delivery->Status; ?></span>
                        </div>
                        <div class="delivery-date">
                            <i class="far fa-calendar-alt"></i> 
                            <?php echo date('M j, Y', strtotime($delivery->OrderDate)); ?>
                        </div>
                    </div>
                    
                    <div class="delivery-body">
                        <div class="delivery-info-section">
                            <div class="delivery-info-item">
                                <div class="info-label"><i class="fas fa-user"></i> Customer:</div>
                                <div class="info-value"><?php echo $delivery->Username; ?></div>
                            </div>
                            <div class="delivery-info-item">
                                <div class="info-label"><i class="fas fa-envelope"></i> Email:</div>
                                <div class="info-value"><?php echo $delivery->Email; ?></div>
                            </div>
                            <?php if(isset($delivery->ContactPhone)): ?>
                                <div class="delivery-info-item">
                                    <div class="info-label"><i class="fas fa-phone"></i> Phone:</div>
                                    <div class="info-value"><?php echo $delivery->ContactPhone; ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="delivery-info-section">
                            <?php if(isset($delivery->ShippingAddress)): ?>
                                <div class="delivery-info-item">
                                    <div class="info-label"><i class="fas fa-map-marker-alt"></i> Shipping Address:</div>
                                    <div class="info-value address"><?php echo nl2br($delivery->ShippingAddress); ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($delivery->Status == 'Processing'): ?>
                                <div class="delivery-action">
                                    <button class="btn btn-primary add-tracking-btn" data-order-id="<?php echo $delivery->OrderID; ?>">
                                        <i class="fas fa-truck"></i> Ship Order
                                    </button>
                                </div>
                            <?php elseif($delivery->Status == 'Shipped'): ?>
                                <div class="delivery-info-item">
                                    <div class="info-label"><i class="fas fa-barcode"></i> Tracking Number:</div>
                                    <div class="info-value tracking"><?php echo $delivery->TrackingNumber ?? 'Not available'; ?></div>
                                </div>
                                <?php if(empty($delivery->TrackingNumber)): ?>
                                    <div class="delivery-action">
                                        <button class="btn btn-secondary add-tracking-btn" data-order-id="<?php echo $delivery->OrderID; ?>">
                                            <i class="fas fa-plus"></i> Add Tracking
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="delivery-footer">
                        <a href="<?php echo URLROOT; ?>/sellers/orderDetails/<?php echo $delivery->OrderID; ?>" class="btn-sm btn-secondary">
                            <i class="fas fa-eye"></i> View Order Details
                        </a>
                        <button class="btn-sm btn-primary print-label-btn" data-order-id="<?php echo $delivery->OrderID; ?>">
                            <i class="fas fa-print"></i> Print Shipping Label
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Hidden Shipping Labels Container (For Print Only) -->
        <div class="shipping-labels-container" style="display: none;">
            <?php foreach($data['pending_deliveries'] as $delivery): ?>
                <div class="shipping-label" id="shippingLabel-<?php echo $delivery->OrderID; ?>">
                    <div class="shipping-label__header">
                        <div class="shipping-label__logo">
                            <!-- Replace with your actual logo path -->
                            <img src="<?php echo URLROOT; ?>/images/logo.png" alt="Logo">
                        </div>
                        <div class="shipping-label__title">
                            <h1>Shipping Label</h1>
                            <p>Order #<?php echo $delivery->OrderID; ?></p>
                        </div>
                    </div>
                    
                    <div class="shipping-label__info">
                        <div class="shipping-label__sender">
                            <h2>Shipped From</h2>
                            <p><strong>Your Store Name</strong></p>
                            <p>123 Seller Street</p>
                            <p>City, State, ZIP</p>
                            <p>Phone: (123) 456-7890</p>
                        </div>
                        <div class="shipping-label__recipient">
                            <h2>Ship To</h2>
                            <p><strong><?php echo $delivery->Username; ?></strong></p>
                            <p><?php echo nl2br($delivery->ShippingAddress ?? 'No shipping address provided'); ?></p>
                            <?php if(isset($delivery->ContactPhone)): ?>
                                <p>Phone: <?php echo $delivery->ContactPhone; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="shipping-label__order-info">
                        <div class="shipping-label__details">
                            <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($delivery->OrderDate)); ?></p>
                            <p><strong>Shipping Method:</strong> Standard Shipping</p>
                            <?php if(!empty($delivery->TrackingNumber)): ?>
                                <p><strong>Tracking Number:</strong> <?php echo $delivery->TrackingNumber; ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="shipping-label__barcode">
                            <!-- Placeholder for barcode - In a real implementation, you would generate a proper barcode -->
                            <div class="barcode-placeholder">
                                <i class="fas fa-barcode"></i>
                                <p><?php echo $delivery->OrderID; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="shipping-label__footer">
                        <p>Thank you for your business!</p>
                        <p>© <?php echo date('Y'); ?> Your Company Name</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Add Tracking Modal -->
    <div id="addTrackingModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Tracking Information</h2>
            <form id="addTrackingForm" action="<?php echo URLROOT; ?>/sellers/updateTracking" method="POST">
                <input type="hidden" id="orderIdInput" name="order_id">
                
                <div class="form-group">
                    <label for="orderIdDisplay">Order ID:</label>
                    <input type="text" id="orderIdDisplay" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label for="trackingNumber">Tracking Number:</label>
                    <input type="text" id="trackingNumber" name="tracking_number" class="form-control" required>
                    <small class="form-text text-muted">Enter the tracking number provided by your shipping carrier.</small>
                </div>
                
                <div class="form-group shipping-info">
                    <p>Once you add a tracking number:</p>
                    <ul>
                        <li>The order status will automatically change to "Shipped"</li>
                        <li>Customers will be able to track their package</li>
                        <li>You cannot remove or change the tracking number after submission</li>
                    </ul>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary cancel-tracking">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Tracking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Delivery search and filter functionality
    const deliverySearch = document.getElementById('deliverySearch');
    const statusFilter = document.getElementById('statusFilter');
    const deliveryCards = document.querySelectorAll('.delivery-card');
    
    function filterDeliveries() {
        const searchTerm = deliverySearch.value.toLowerCase();
        const statusValue = statusFilter.value;
        
        deliveryCards.forEach(card => {
            const orderId = card.getAttribute('data-order-id').toLowerCase();
            const customer = card.getAttribute('data-customer').toLowerCase();
            const status = card.getAttribute('data-status');
            
            const matchesSearch = orderId.includes(searchTerm) || customer.includes(searchTerm);
            const matchesStatus = statusValue === '' || status === statusValue;
            
            if (matchesSearch && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    deliverySearch.addEventListener('input', filterDeliveries);
    statusFilter.addEventListener('change', filterDeliveries);
    
    // Add tracking modal functionality
    const addTrackingModal = document.getElementById('addTrackingModal');
    const addTrackingBtns = document.querySelectorAll('.add-tracking-btn');
    const addTrackingCloseBtn = addTrackingModal.querySelector('.close');
    const cancelTrackingBtn = addTrackingModal.querySelector('.cancel-tracking');
    
    addTrackingBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            
            document.getElementById('orderIdInput').value = orderId;
            document.getElementById('orderIdDisplay').value = orderId;
            document.getElementById('trackingNumber').value = '';
            
            addTrackingModal.style.display = 'block';
        });
    });
    
    addTrackingCloseBtn.addEventListener('click', function() {
        addTrackingModal.style.display = 'none';
    });
    
    cancelTrackingBtn.addEventListener('click', function() {
        addTrackingModal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target == addTrackingModal) {
            addTrackingModal.style.display = 'none';
        }
    });
    
    // Print shipping label functionality - Updated version
    const printLabelBtns = document.querySelectorAll('.print-label-btn');
    
    printLabelBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            printShippingLabel(orderId);
        });
    });
    
    function printShippingLabel(orderId) {
        // First get the shipping label element
        const shippingLabel = document.getElementById('shippingLabel-' + orderId);
        
        if (!shippingLabel) {
            alert('Could not find shipping label for Order #' + orderId);
            return;
        }
        
        // Create a new printable div to hold just the shipping label content
        const printContent = document.createElement('div');
        printContent.classList.add('print-only');
        printContent.appendChild(shippingLabel.cloneNode(true));
        
        // Add the printable content to the page temporarily
        document.body.appendChild(printContent);
        
        // Small delay to ensure all styles are applied
        setTimeout(function() {
            window.print();
            
            // Clean up - remove the temporary print content
            document.body.removeChild(printContent);
        }, 100);
    }
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>