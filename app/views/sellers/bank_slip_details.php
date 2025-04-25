<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-bank-slip-details">
    <?php flash('payment_message'); ?>
    
    <div class="slip-header">
        <a href="<?php echo URLROOT; ?>/sellers/bankPayments" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Bank Payments
        </a>
        <h2><i class="fas fa-file-invoice-dollar"></i> Bank Slip Details</h2>
    </div>
    
    <div class="slip-details-container">
        <div class="slip-info-grid">
            <!-- Order Details Card -->
            <div class="slip-info-card">
                <h3><i class="fas fa-shopping-cart"></i> Order Information</h3>
                <div class="info-content">
                    <div class="info-row">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">
                            <a href="<?php echo URLROOT; ?>/sellers/orderDetails/<?php echo $data['payment']->OrderID; ?>" class="order-link">
                                <?php echo $data['payment']->OrderID; ?>
                            </a>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Order Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-<?php echo strtolower($data['payment']->OrderStatus); ?>">
                                <?php echo $data['payment']->OrderStatus; ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Order Total:</span>
                        <span class="info-value price-value"><?php echo 'Rs. ' . number_format($data['payment']->TotalAmount, 2); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Customer:</span>
                        <span class="info-value"><?php echo $data['payment']->Username; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Details Card -->
            <div class="slip-info-card">
                <h3><i class="fas fa-money-check-alt"></i> Payment Information</h3>
                <div class="info-content">
                    <div class="info-row">
                        <span class="info-label">Payment ID:</span>
                        <span class="info-value"><?php echo $data['payment']->ID; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Upload Date:</span>
                        <span class="info-value"><?php echo date('M j, Y g:i A', strtotime($data['payment']->UploadDate)); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-<?php echo strtolower($data['payment']->Status); ?>">
                                <?php echo $data['payment']->Status; ?>
                            </span>
                        </span>
                    </div>
                    <?php if($data['payment']->Status != 'Pending'): ?>
                    <div class="info-row">
                        <span class="info-label">Verified By:</span>
                        <span class="info-value"><?php echo $data['payment']->VerifierName ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Verification Date:</span>
                        <span class="info-value"><?php echo $data['payment']->VerificationDate ? date('M j, Y g:i A', strtotime($data['payment']->VerificationDate)) : 'N/A'; ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Payment Slip Image Card -->
        <div class="slip-image-card">
            <h3><i class="fas fa-image"></i> Bank Payment Slip</h3>
            <div class="slip-image-container">
                <img src="<?php echo URLROOT; ?>/public/uploads/slips/<?php echo $data['payment']->SlipFile; ?>" 
                     alt="Bank Payment Slip" class="slip-image" />
            </div>
            
            <?php if($data['payment']->Status == 'Pending'): ?>
            <div class="slip-actions">
                <button class="btn btn-success process-btn" 
                        data-payment-id="<?php echo $data['payment']->ID; ?>"
                        data-order-id="<?php echo $data['payment']->OrderID; ?>"
                        data-action="verify">
                    <i class="fas fa-check"></i> Verify Payment
                </button>
                <button class="btn btn-danger process-btn" 
                        data-payment-id="<?php echo $data['payment']->ID; ?>"
                        data-order-id="<?php echo $data['payment']->OrderID; ?>"
                        data-action="reject">
                    <i class="fas fa-times"></i> Reject Payment
                </button>
            </div>
            <?php elseif(!empty($data['payment']->Notes)): ?>
            <div class="slip-notes">
                <h4><i class="fas fa-clipboard"></i> Notes</h4>
                <p><?php echo $data['payment']->Notes; ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Order Items Card -->
        <div class="slip-order-items-card">
            <h3><i class="fas fa-box-open"></i> Order Items</h3>
            <?php if(empty($data['order_items'])): ?>
                <p class="no-items">No items found for this order.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table order-items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['order_items'] as $item): ?>
                                <tr>
                                    <td class="item-col">
                                        <div class="item-info">
                                            <?php if(!empty($item->ProductImage)): ?>
                                                <img src="<?php echo URLROOT; ?>/public/uploads/products/<?php echo $item->ProductImage; ?>" 
                                                     alt="<?php echo $item->ProductName; ?>" class="item-image" />
                                            <?php else: ?>
                                                <div class="item-image placeholder">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="item-details">
                                                <h4><?php echo $item->ProductName; ?></h4>
                                                <span class="item-id"><?php echo $item->ProductID; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo 'Rs. ' . number_format($item->Price, 2); ?></td>
                                    <td><?php echo $item->Quantity; ?></td>
                                    <td><?php echo 'Rs. ' . number_format($item->Price * $item->Quantity, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Order Total:</strong></td>
                                <td><strong><?php echo 'Rs. ' . number_format($data['payment']->TotalAmount, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Process Payment Modal -->
    <div id="processPaymentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Process Bank Payment</h2>
            <form id="processPaymentForm" action="<?php echo URLROOT; ?>/sellers/processBankPayment" method="POST">
                <input type="hidden" id="paymentIdInput" name="payment_id">
                <input type="hidden" id="orderIdInput" name="order_id">
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
</div>

<script>
    // Process payment modal functionality
    const processPaymentModal = document.getElementById('processPaymentModal');
    const modalTitle = document.getElementById('modalTitle');
    const confirmBtn = document.getElementById('confirmBtn');
    const processBtns = document.querySelectorAll('.process-btn');
    const closeBtn = processPaymentModal.querySelector('.close');
    const cancelBtn = processPaymentModal.querySelector('.cancel-btn');
    
    // Open modal with correct title and action when process button is clicked
    processBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const paymentId = this.getAttribute('data-payment-id');
            const orderId = this.getAttribute('data-order-id');
            const action = this.getAttribute('data-action');
            
            document.getElementById('paymentIdInput').value = paymentId;
            document.getElementById('orderIdInput').value = orderId;
            
            if (action === 'verify') {
                modalTitle.textContent = 'Verify Bank Payment';
                confirmBtn.textContent = 'Confirm Verification';
                confirmBtn.className = 'btn btn-success';
                document.getElementById('statusInput').value = 'Verified';
            } else {
                modalTitle.textContent = 'Reject Bank Payment';
                confirmBtn.textContent = 'Confirm Rejection';
                confirmBtn.className = 'btn btn-danger';
                document.getElementById('statusInput').value = 'Rejected';
            }
            
            processPaymentModal.style.display = 'block';
        });
    });
    
    // Close modal when close button is clicked
    closeBtn.addEventListener('click', function() {
        processPaymentModal.style.display = 'none';
    });
    
    // Close modal when cancel button is clicked
    cancelBtn.addEventListener('click', function() {
        processPaymentModal.style.display = 'none';
    });
    
    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target == processPaymentModal) {
            processPaymentModal.style.display = 'none';
        }
    });
    
    // Image click zoom functionality
    const slipImage = document.querySelector('.slip-image');
    
    if (slipImage) {
        slipImage.addEventListener('click', function() {
            window.open(this.src, '_blank');
        });
    }
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>