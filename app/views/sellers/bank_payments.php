<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-bank-payments">
    <?php flash('payment_message'); ?>
    
    <div class="section-header">
        <h2><i class="fas fa-money-check"></i> Bank Payment Verification</h2>
        <p>Review and verify customer bank transfer payment slips.</p>
    </div>
    
    <!-- Tabs for navigation -->
    <div class="bank-payment-tabs">
        <button class="bank-payment-tab-btn active" data-tab="pending">Pending Verification (<?php echo count($data['pending_payments']); ?>)</button>
        <button class="bank-payment-tab-btn" data-tab="history">Payment History (<?php echo count($data['payment_history']); ?>)</button>
    </div>
    
    <!-- Pending Payments Tab -->
    <div id="pending-tab" class="bank-payment-tab-content active">
        <?php if(empty($data['pending_payments'])): ?>
            <div class="no-data-container">
                <div class="no-data-message">
                    <i class="fas fa-check-circle"></i>
                    <h3>No Pending Bank Payments</h3>
                    <p>There are no pending bank payment slips to verify at this time.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table bank-payment-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Upload Date</th>
                            <th>Payment Slip</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['pending_payments'] as $payment): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/sellers/orderDetails/<?php echo $payment->OrderID; ?>" class="order-link">
                                        <?php echo $payment->OrderID; ?>
                                    </a>
                                </td>
                                <td><?php echo $payment->Username; ?></td>
                                <td><?php echo 'Rs. ' . number_format($payment->TotalAmount, 2); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($payment->UploadDate)); ?></td>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/public/uploads/slips/<?php echo $payment->SlipFile; ?>" 
                                       class="slip-preview-link" target="_blank">
                                        <img src="<?php echo URLROOT; ?>/public/uploads/slips/<?php echo $payment->SlipFile; ?>" 
                                             alt="Payment Slip" class="slip-thumbnail" />
                                        <span>View Full Size</span>
                                    </a>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?php echo URLROOT; ?>/sellers/viewBankSlip/<?php echo $payment->ID; ?>" 
                                           class="btn-sm btn-primary">
                                            <i class="fas fa-search"></i> Details
                                        </a>
                                        <button class="btn-sm btn-success process-btn" 
                                                data-payment-id="<?php echo $payment->ID; ?>"
                                                data-order-id="<?php echo $payment->OrderID; ?>"
                                                data-action="verify">
                                            <i class="fas fa-check"></i> Verify
                                        </button>
                                        <button class="btn-sm btn-danger process-btn" 
                                                data-payment-id="<?php echo $payment->ID; ?>"
                                                data-order-id="<?php echo $payment->OrderID; ?>"
                                                data-action="reject">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Payment History Tab -->
    <div id="history-tab" class="bank-payment-tab-content">
        <?php if(empty($data['payment_history'])): ?>
            <div class="no-data-container">
                <div class="no-data-message">
                    <i class="fas fa-history"></i>
                    <h3>No Payment History</h3>
                    <p>There are no verified or rejected bank payments in the history yet.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table bank-payment-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Verification Date</th>
                            <th>Verified By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['payment_history'] as $payment): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/sellers/orderDetails/<?php echo $payment->OrderID; ?>" class="order-link">
                                        <?php echo $payment->OrderID; ?>
                                    </a>
                                </td>
                                <td><?php echo $payment->Username; ?></td>
                                <td><?php echo 'Rs. ' . number_format($payment->TotalAmount, 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($payment->Status); ?>">
                                        <?php echo $payment->Status; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y g:i A', strtotime($payment->VerificationDate)); ?></td>
                                <td><?php echo $payment->VerifierName; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?php echo URLROOT; ?>/sellers/viewBankSlip/<?php echo $payment->ID; ?>" 
                                           class="btn-sm btn-primary">
                                            <i class="fas fa-search"></i> Details
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/public/uploads/slips/<?php echo $payment->SlipFile; ?>" 
                                           class="btn-sm btn-secondary" target="_blank">
                                            <i class="fas fa-file-image"></i> View Slip
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
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
    // Tab functionality
    const tabButtons = document.querySelectorAll('.bank-payment-tab-btn');
    const tabContents = document.querySelectorAll('.bank-payment-tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Show corresponding tab content
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId + '-tab').classList.add('active');
        });
    });
    
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
    
    // Image preview hover effect
    const slipPreviewLinks = document.querySelectorAll('.slip-preview-link');
    
    slipPreviewLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.querySelector('span').style.opacity = '1';
        });
        
        link.addEventListener('mouseleave', function() {
            this.querySelector('span').style.opacity = '0';
        });
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>