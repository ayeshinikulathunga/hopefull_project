<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-cancellation-requests">
    <?php flash('cancellation_message'); ?>
    <?php flash('cancellation_error'); ?>
    
    <div class="section-header">
        <h2><i class="fas fa-times-circle"></i> Cancellation Requests</h2>
        <p>Review and manage customer cancellation requests for orders.</p>
    </div>
    
    <?php if(empty($data['cancellation_requests'])): ?>
        <div class="no-data-container">
            <div class="no-data-message">
                <i class="fas fa-check-circle"></i>
                <h3>No Pending Cancellation Requests</h3>
                <p>There are no pending cancellation requests at this time.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table cancellation-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Order Status</th>
                        <th>Request Date</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['cancellation_requests'] as $request): ?>
                        <tr>
                            <td><?php echo $request->CancellationID; ?></td>
                            <td>
                                <a href="<?php echo URLROOT; ?>/sellers/orderDetails/<?php echo $request->OrderID; ?>" class="order-link">
                                    <?php echo $request->OrderID; ?>
                                </a>
                            </td>
                            <td><?php echo $request->CustomerName; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($request->OrderStatus); ?>">
                                    <?php echo $request->OrderStatus; ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($request->RequestDate)); ?></td>
                            <td>
                                <div class="reason-text">
                                    <?php echo $request->Reason; ?>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if(in_array($request->OrderStatus, ['Pending', 'Processing'])): ?>
                                        <button class="btn-sm btn-success process-btn" 
                                                data-cancellation-id="<?php echo $request->CancellationID; ?>"
                                                data-order-id="<?php echo $request->OrderID; ?>"
                                                data-action="approve">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-sm btn-secondary" disabled title="Order is already <?php echo $request->OrderStatus; ?>">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button class="btn-sm btn-danger process-btn" 
                                            data-cancellation-id="<?php echo $request->CancellationID; ?>"
                                            data-order-id="<?php echo $request->OrderID; ?>"
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
    
    <!-- Process Cancellation Modal -->
    <div id="processCancellationModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Process Cancellation Request</h2>
            <form id="processCancellationForm" action="<?php echo URLROOT; ?>/sellers/processCancellation" method="POST">
                <input type="hidden" id="cancellationIdInput" name="cancellation_id">
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
    // Process cancellation modal functionality
    const processCancellationModal = document.getElementById('processCancellationModal');
    const modalTitle = document.getElementById('modalTitle');
    const confirmBtn = document.getElementById('confirmBtn');
    const processBtns = document.querySelectorAll('.process-btn');
    const closeBtn = processCancellationModal.querySelector('.close');
    const cancelBtn = processCancellationModal.querySelector('.cancel-btn');
    
    // Open modal with correct title and action when process button is clicked
    processBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const cancellationId = this.getAttribute('data-cancellation-id');
            const orderId = this.getAttribute('data-order-id');
            const action = this.getAttribute('data-action');
            
            document.getElementById('cancellationIdInput').value = cancellationId;
            document.getElementById('orderIdInput').value = orderId;
            
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
    closeBtn.addEventListener('click', function() {
        processCancellationModal.style.display = 'none';
    });
    
    // Close modal when cancel button is clicked
    cancelBtn.addEventListener('click', function() {
        processCancellationModal.style.display = 'none';
    });
    
    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target == processCancellationModal) {
            processCancellationModal.style.display = 'none';
        }
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>