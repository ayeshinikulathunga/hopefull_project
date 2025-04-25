<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-order-details">
    <?php flash('order_message'); ?>
<<<<<<< HEAD
=======
    <?php flash('cancellation_message'); ?>
    <?php flash('cancellation_error'); ?>
>>>>>>> cd92d372a120695a1d0602d06341b236d9fb1dd0
    
    <div class="order-header">
        <div class="back-link">
            <a href="<?php echo URLROOT; ?>/sellers/orders" class="btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
        
        <div class="order-title">
            <h2>Order #<?php echo $data['order']->OrderID; ?></h2>
            <span class="status-badge status-<?php echo strtolower($data['order']->Status); ?>"><?php echo $data['order']->Status; ?></span>
        </div>
        
        <div class="order-actions">
            <?php if($data['order']->Status == 'Pending'): ?>
                <button class="btn btn-primary update-status-btn" data-order-id="<?php echo $data['order']->OrderID; ?>" data-current-status="<?php echo $data['order']->Status; ?>">
                    <i class="fas fa-cog"></i> Update Status
                </button>
            <?php endif; ?>
            
            <button class="btn btn-secondary print-order-btn" onclick="window.print();">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
    
    <div class="order-info-grid">
        <!-- Order Information -->
        <div class="info-card">
            <h3><i class="fas fa-info-circle"></i> Order Information</h3>
            <div class="info-content">
                <div class="info-row">
                    <div class="info-label">Order Date:</div>
                    <div class="info-value"><?php echo date('F j, Y - g:i A', strtotime($data['order']->OrderDate)); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Customer:</div>
                    <div class="info-value"><?php echo $data['order']->Username; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?php echo $data['order']->Email; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge status-<?php echo strtolower($data['order']->Status); ?>"><?php echo $data['order']->Status; ?></span>
                    </div>
                </div>
            </div>
        </div>
        
<<<<<<< HEAD
=======
        <!-- Cancellation Request Information (if exists) -->
        <?php if(isset($data['cancellation_request']) && $data['cancellation_request']): ?>
        <div class="info-card cancellation-info-card">
            <h3>
                <i class="fas fa-times-circle"></i> Cancellation Request
                <span class="status-badge status-<?php echo strtolower($data['cancellation_request']->Status); ?>">
                    <?php echo $data['cancellation_request']->Status; ?>
                </span>
            </h3>
            <div class="info-content">
                <div class="info-row">
                    <div class="info-label">Request ID:</div>
                    <div class="info-value"><?php echo $data['cancellation_request']->CancellationID; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Requested By:</div>
                    <div class="info-value"><?php echo $data['cancellation_request']->Username; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Request Date:</div>
                    <div class="info-value"><?php echo date('F j, Y - g:i A', strtotime($data['cancellation_request']->RequestDate)); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Reason:</div>
                    <div class="info-value"><?php echo $data['cancellation_request']->Reason; ?></div>
                </div>
                
                <?php if($data['cancellation_request']->Status !== 'Pending'): ?>
                    <div class="info-row">
                        <div class="info-label">Processed Date:</div>
                        <div class="info-value"><?php echo date('F j, Y - g:i A', strtotime($data['cancellation_request']->ProcessedDate)); ?></div>
                    </div>
                    <?php if(!empty($data['cancellation_request']->Notes)): ?>
                        <div class="info-row">
                            <div class="info-label">Notes:</div>
                            <div class="info-value"><?php echo $data['cancellation_request']->Notes; ?></div>
                        </div>
                    <?php endif; ?>
                <?php elseif(isset($data['cancellation_allowed']) && $data['cancellation_allowed']): ?>
                    <div class="cancellation-actions">
                        <button class="btn btn-success process-btn" 
                                data-cancellation-id="<?php echo $data['cancellation_request']->CancellationID; ?>"
                                data-order-id="<?php echo $data['order']->OrderID; ?>"
                                data-action="approve">
                            <i class="fas fa-check"></i> Approve Cancellation
                        </button>
                        <button class="btn btn-danger process-btn" 
                                data-cancellation-id="<?php echo $data['cancellation_request']->CancellationID; ?>"
                                data-order-id="<?php echo $data['order']->OrderID; ?>"
                                data-action="reject">
                            <i class="fas fa-times"></i> Reject Cancellation
                        </button>
                    </div>
                <?php else: ?>
                    <div class="info-row">
                        <div class="info-value text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            This order is already <?php echo $data['order']->Status; ?> and cannot be cancelled.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
>>>>>>> cd92d372a120695a1d0602d06341b236d9fb1dd0
        <!-- Shipping Information -->
        <div class="info-card">
            <h3><i class="fas fa-shipping-fast"></i> Shipping Information</h3>
            <div class="info-content">
                <?php if(isset($data['shipping']) && $data['shipping']): ?>
                    <div class="info-row">
                        <div class="info-label">Shipping Address:</div>
                        <div class="info-value"><?php echo nl2br($data['shipping']->ShippingAddress); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Contact Phone:</div>
                        <div class="info-value"><?php echo $data['shipping']->ContactPhone; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Payment Method:</div>
                        <div class="info-value">
                            <?php 
                               $paymentMethod = !empty($data['order']->PaymentMethod) ? $data['order']->PaymentMethod : 
                               ($data['shipping']->PaymentMethod ?? 'Not specified');
                                switch($paymentMethod) {
                                    case 'cash_on_delivery':
                                        echo 'Cash on Delivery';
                                        break;
                                    case 'bank_transfer':
                                        echo 'Bank Transfer';
                                        break;
                                    case 'online_payment':
                                        echo 'Credit/Debit Card';
                                        break;
                                    default:
                                        echo $paymentMethod;
                                }
                            ?>
                        </div>
                    </div>
                    <?php if(!empty($data['shipping']->ShippingNotes)): ?>
                        <div class="info-row">
                            <div class="info-label">Notes:</div>
                            <div class="info-value"><?php echo $data['shipping']->ShippingNotes; ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($data['shipping']->TrackingNumber)): ?>
                        <div class="info-row">
                            <div class="info-label">Tracking Number:</div>
                            <div class="info-value"><?php echo $data['shipping']->TrackingNumber; ?></div>
                        </div>
                    <?php elseif($data['order']->Status == 'Processing'): ?>
                        <div class="add-tracking-container">
                            <button class="btn-sm btn-primary add-tracking-btn">
                                <i class="fas fa-plus"></i> Add Tracking
                            </button>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="no-data">No shipping information available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Order Items -->
    <div class="order-items-card">
        <h3><i class="fas fa-box-open"></i> Order Items (Your Products Only)</h3>
        
        <?php if(empty($data['order_items'])): ?>
            <p class="no-data">No items from your products in this order.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="item-col">Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subtotal = 0;
                        foreach($data['order_items'] as $item): 
                            $itemTotal = $item->Price * $item->Quantity;
                            $subtotal += $itemTotal;
                        ?>
                            <tr>
                                <td class="item-col">
                                    <div class="item-info">
                                        <?php if(!empty($item->ProductImage)): ?>
                                            <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $item->ProductImage; ?>" alt="<?php echo $item->ProductName; ?>" class="item-image">
                                        <?php else: ?>
                                            <div class="item-image placeholder">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="item-details">
                                            <h4><?php echo $item->ProductName; ?></h4>
                                            <span class="item-id">Product ID: <?php echo $item->ProductID; ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>Rs. <?php echo number_format($item->Price, 2); ?></td>
                                <td><?php echo $item->Quantity; ?></td>
                                <td>Rs. <?php echo number_format($itemTotal, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Subtotal:</th>
                            <th>Rs. <?php echo number_format($subtotal, 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Update Status Modal -->
    <div id="updateStatusModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Order Status</h2>
            <form id="updateStatusForm" action="<?php echo URLROOT; ?>/sellers/updateOrderStatus" method="POST">
                <input type="hidden" id="orderIdInput" name="order_id" value="<?php echo $data['order']->OrderID; ?>">
                
                <div class="form-group">
                    <label for="currentStatus">Current Status:</label>
                    <input type="text" id="currentStatus" class="form-control" value="<?php echo $data['order']->Status; ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="newStatus">New Status:</label>
                    <select id="newStatus" name="status" class="form-control" required>
                        <option value="">Select New Status</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary cancel-update">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add Tracking Modal -->
    <div id="addTrackingModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Tracking Information</h2>
            <form id="addTrackingForm" action="<?php echo URLROOT; ?>/sellers/updateTracking" method="POST">
                <input type="hidden" name="order_id" value="<?php echo $data['order']->OrderID; ?>">
                
                <div class="form-group">
                    <label for="trackingNumber">Tracking Number:</label>
                    <input type="text" id="trackingNumber" name="tracking_number" class="form-control" required>
                    <small class="form-text text-muted">Enter the tracking number provided by your shipping carrier.</small>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary cancel-tracking">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Tracking</button>
                </div>
            </form>
        </div>
    </div>
<<<<<<< HEAD
=======
    
    <!-- Process Cancellation Modal -->
    <div id="processCancellationModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Process Cancellation Request</h2>
            <form id="processCancellationForm" action="<?php echo URLROOT; ?>/sellers/processCancellation" method="POST">
                <input type="hidden" id="cancellationIdInput" name="cancellation_id">
                <input type="hidden" id="orderIdCancellationInput" name="order_id">
                <input type="hidden" id="statusInput" name="status">
                
                <div class="form-group">
                    <label for="notesInput">Notes (optional):</label>
                    <textarea id="notesInput" name="notes" class="form-control" rows="3" placeholder="Add any additional notes about this decision..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary cancel-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirmBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
>>>>>>> cd92d372a120695a1d0602d06341b236d9fb1dd0
</div>

<script>
    // Update status modal functionality
    const updateStatusModal = document.getElementById('updateStatusModal');
    const updateStatusBtns = document.querySelectorAll('.update-status-btn');
    const updateStatusCloseBtn = updateStatusModal.querySelector('.close');
    const cancelUpdateBtn = updateStatusModal.querySelector('.cancel-update');
    
    updateStatusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            updateStatusModal.style.display = 'block';
        });
    });
    
    updateStatusCloseBtn.addEventListener('click', function() {
        updateStatusModal.style.display = 'none';
    });
    
    cancelUpdateBtn.addEventListener('click', function() {
        updateStatusModal.style.display = 'none';
    });
    
    // Add tracking modal functionality
    const addTrackingModal = document.getElementById('addTrackingModal');
    const addTrackingBtns = document.querySelectorAll('.add-tracking-btn');
    const addTrackingCloseBtn = addTrackingModal.querySelector('.close');
    const cancelTrackingBtn = addTrackingModal.querySelector('.cancel-tracking');
    
    addTrackingBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            addTrackingModal.style.display = 'block';
        });
    });
    
    addTrackingCloseBtn.addEventListener('click', function() {
        addTrackingModal.style.display = 'none';
    });
    
    cancelTrackingBtn.addEventListener('click', function() {
        addTrackingModal.style.display = 'none';
    });
    
<<<<<<< HEAD
=======
    // Process cancellation modal functionality
    const processCancellationModal = document.getElementById('processCancellationModal');
    const processBtns = document.querySelectorAll('.process-btn');
    const modalTitle = document.getElementById('modalTitle');
    const confirmBtn = document.getElementById('confirmBtn');
    const processCancellationCloseBtn = processCancellationModal.querySelector('.close');
    const cancelCancellationBtn = processCancellationModal.querySelector('.cancel-btn');
    
    if (processBtns.length > 0) {
        // Open modal with correct title and action when process button is clicked
        processBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const cancellationId = this.getAttribute('data-cancellation-id');
                const orderId = this.getAttribute('data-order-id');
                const action = this.getAttribute('data-action');
                
                document.getElementById('cancellationIdInput').value = cancellationId;
                document.getElementById('orderIdCancellationInput').value = orderId;
                
                if (action === 'approve') {
                    modalTitle.textContent = 'Approve Cancellation Request';
                    confirmBtn.textContent = 'Approve Cancellation';
                    confirmBtn.className = 'btn btn-success';
                    document.getElementById('statusInput').value = 'Approved';
                } else {
                    modalTitle.textContent = 'Reject Cancellation Request';
                    confirmBtn.textContent = 'Reject Cancellation';
                    confirmBtn.className = 'btn btn-danger';
                    document.getElementById('statusInput').value = 'Rejected';
                }
                
                processCancellationModal.style.display = 'block';
            });
        });
        
        // Close modal when close button is clicked
        processCancellationCloseBtn.addEventListener('click', function() {
            processCancellationModal.style.display = 'none';
        });
        
        // Close modal when cancel button is clicked
        cancelCancellationBtn.addEventListener('click', function() {
            processCancellationModal.style.display = 'none';
        });
    }
    
>>>>>>> cd92d372a120695a1d0602d06341b236d9fb1dd0
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target == updateStatusModal) {
            updateStatusModal.style.display = 'none';
        }
        if (event.target == addTrackingModal) {
            addTrackingModal.style.display = 'none';
        }
<<<<<<< HEAD
=======
        if (event.target == processCancellationModal) {
            processCancellationModal.style.display = 'none';
        }
>>>>>>> cd92d372a120695a1d0602d06341b236d9fb1dd0
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>