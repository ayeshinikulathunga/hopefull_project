<?php require APPROOT . '/views/includes/headers/recipient_header.php'; ?>

<div class="container view-request-page-container">
    <!-- Flash Messages -->
    <?php flash('request_message'); ?>
    
    <div class="request-detail-container">
        <!-- Request Header -->
        <div class="detail-header">
            <div class="header-left">
                <a href="<?php echo URLROOT; ?>/recipients/requests" class="btn btn-sm btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Requests
                </a>
                <h1><?php echo $data['request']->Title; ?></h1>
                <div class="request-meta">
                    <span class="request-category"><?php echo $data['request']->Category; ?></span>
                    <span class="request-type">
                        <?php if($data['request']->RequestType == 'Monetary'): ?>
                            <i class="fas fa-hand-holding-usd"></i> Monetary
                        <?php else: ?>
                            <i class="fas fa-box-open"></i> Non-Monetary
                        <?php endif; ?>
                    </span>
                    <span class="request-status <?php echo strtolower($data['request']->RequestStatus); ?>">
                        <?php echo $data['request']->RequestStatus; ?>
                    </span>
                    <span class="verification-status <?php echo strtolower($data['request']->VerificationStatus); ?>">
                        <?php echo $data['request']->VerificationStatus; ?>
                    </span>
                </div>
            </div>
            <div class="header-right">
                <?php if($data['request']->RequestStatus == 'Pending' || $data['request']->VerificationStatus == 'Rejected'): ?>
                    <a href="<?php echo URLROOT; ?>/recipients/editRequest/<?php echo $data['request']->RequestID; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Request
                    </a>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Request Content -->
        <div class="detail-content">
            <!-- Left Column - Request Details -->
            <div class="detail-main">
                <?php if($data['request']->HasImage): ?>
                    <div class="request-image-container">
                        <img src="<?php echo URLROOT; ?>/uploads/requests/<?php echo $data['request']->RequestID; ?>.jpg?v=<?php echo time(); ?>" alt="<?php echo $data['request']->Title; ?>" class="request-full-image">
                    </div>
                <?php endif; ?>
                
                <div class="detail-section">
                    <h3>Description</h3>
                    <div class="description-content">
                        <?php echo nl2br(htmlspecialchars($data['request']->Description)); ?>
                    </div>
                </div>
                
                <?php if($data['request']->RequestType == 'Monetary'): ?>
                    <div class="detail-section">
                        <h3>Financial Goal</h3>
                        <div class="goal-container">
                            <div class="goal-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Target Amount</span>
                                    <span class="stat-value">Rs. <?php echo number_format($data['request']->TargetAmount); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Raised So Far</span>
                                    <span class="stat-value">Rs. <?php echo number_format($data['request']->CurrentAmount); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Remaining</span>
                                    <span class="stat-value">Rs. <?php echo number_format(max(0, $data['request']->TargetAmount - $data['request']->CurrentAmount)); ?></span>
                                </div>
                            </div>
                            <div class="goal-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $data['request']->Progress; ?>%"></div>
                                </div>
                                <span class="progress-text"><?php echo $data['request']->Progress; ?>% Complete</span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="detail-section">
                        <h3>Item Details</h3>
                        <div class="item-details">
                            <div class="stat-item">
                                <span class="stat-label">Item Needed</span>
                                <span class="stat-value"><?php echo $data['request']->ItemName; ?></span>
                            </div>
                            <div class="goal-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Quantity Needed</span>
                                    <span class="stat-value"><?php echo number_format($data['request']->QuantityNeeded); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Received So Far</span>
                                    <span class="stat-value"><?php echo number_format($data['request']->QuantityReceived); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Remaining</span>
                                    <span class="stat-value"><?php echo number_format(max(0, $data['request']->QuantityNeeded - $data['request']->QuantityReceived)); ?></span>
                                </div>
                            </div>
                            <div class="goal-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $data['request']->Progress; ?>%"></div>
                                </div>
                                <span class="progress-text"><?php echo $data['request']->Progress; ?>% Complete</span>
                            </div>
                            
                            <div class="item-location">
                                <h4>Drop-off Information</h4>
                                <div class="location-details">
                                    <div class="detail-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <div>
                                            <span class="detail-label">Location:</span>
                                            <span class="detail-value"><?php echo nl2br(htmlspecialchars($data['request']->DropOffLocation)); ?></span>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-map"></i>
                                        <div>
                                            <span class="detail-label">Province:</span>
                                            <span class="detail-value"><?php echo $data['request']->Province; ?></span>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-clock"></i>
                                        <div>
                                            <span class="detail-label">Available Time:</span>
                                            <span class="detail-value"><?php echo date('F j, Y g:i A', strtotime($data['request']->DropOffTime)); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Right Column - Donation List and Stats -->
            <div class="detail-sidebar">
                <div class="sidebar-section">
                    <h3>Request Status</h3>
                    <div class="status-info">
                        <div class="status-item">
                            <span class="status-label">Verification Status</span>
                            <span class="status-value verification-badge <?php echo strtolower($data['request']->VerificationStatus); ?>">
                                <i class="<?php echo $data['request']->VerificationStatus == 'Approved' ? 'fas fa-check-circle' : ($data['request']->VerificationStatus == 'Rejected' ? 'fas fa-times-circle' : 'fas fa-clock'); ?>"></i>
                                <?php echo $data['request']->VerificationStatus; ?>
                            </span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Request Status</span>
                            <span class="status-value request-badge <?php echo strtolower($data['request']->RequestStatus); ?>">
                                <i class="<?php 
                                    echo $data['request']->RequestStatus == 'Completed' ? 'fas fa-check-circle' : 
                                         ($data['request']->RequestStatus == 'InProgress' ? 'fas fa-spinner' : 
                                         ($data['request']->RequestStatus == 'Expired' ? 'fas fa-calendar-times' : 'fas fa-hourglass-start')); 
                                ?>"></i>
                                <?php echo $data['request']->RequestStatus; ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="sidebar-section">
                    <h3>Request Timeline</h3>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Created</h4>
                                <p><?php echo date('F j, Y', strtotime($data['request']->CreatedDate)); ?></p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-hourglass-end"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Deadline</h4>
                                <p><?php echo date('F j, Y', strtotime($data['request']->Deadline)); ?></p>
                            </div>
                        </div>
                        
                        <?php if ($data['request']->VerificationStatus == 'Approved'): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon approved">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Verification Approved</h4>
                                <p><?php echo isset($data['request']->ApprovalDate) ? date('F j, Y', strtotime($data['request']->ApprovalDate)) : 'Date not available'; ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($data['request']->RequestStatus == 'Completed'): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon completed">
                                <i class="fas fa-flag-checkered"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Goal Reached</h4>
                                <p>Target <?php echo $data['request']->RequestType == 'Monetary' ? 'amount' : 'quantity'; ?> has been met!</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (!empty($data['donations'])): ?>
                <div class="sidebar-section">
                    <h3>Recent Donations</h3>
                    <div class="donations-list">
                        <?php foreach(array_slice($data['donations'], 0, 5) as $donation): ?>
                            <div class="donation-item">
                                <div class="donation-info">
                                    <div class="donor-name">
                                        <i class="fas fa-user"></i> <?php echo $donation->DonorName; ?>
                                    </div>
                                    <div class="donation-value">
                                        <?php if($data['request']->RequestType == 'Monetary'): ?>
                                            <span class="amount">Rs. <?php echo number_format($donation->Amount); ?></span>
                                        <?php else: ?>
                                            <span class="quantity"><?php echo number_format($donation->QuantityDonated); ?> items</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="donation-date">
                                    <?php echo date('M j, Y', strtotime($donation->DonationDate)); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($data['donations']) > 5): ?>
                            <div class="view-all-link">
                                <a href="#donations-section">View all <?php echo count($data['donations']); ?> donations</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="sidebar-section">
                    <h3>Actions</h3>
                    <div class="request-actions-list">
                        <?php if($data['request']->VerificationStatus == 'Approved'): ?>
                            <a href="<?php echo URLROOT; ?>/recipients/createDonorFeedback/<?php echo $data['request']->RequestID; ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-comment-alt"></i> Send Feedback to Donors
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($data['donations'])): ?>
        <div id="donations-section" class="donations-section">
            <h2>All Donations</h2>
            <div class="donation-table-container">
                <table class="donation-table">
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Date</th>
                            <?php if($data['request']->RequestType == 'Monetary'): ?>
                                <th>Amount</th>
                            <?php else: ?>
                                <th>Quantity</th>
                            <?php endif; ?>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['donations'] as $donation): ?>
                            <tr>
                                <td><?php echo $donation->DonorName; ?></td>
                                <td><?php echo date('M j, Y', strtotime($donation->DonationDate)); ?></td>
                                <?php if($data['request']->RequestType == 'Monetary'): ?>
                                    <td>Rs. <?php echo number_format($donation->Amount); ?></td>
                                <?php else: ?>
                                    <td><?php echo number_format($donation->QuantityDonated); ?> items</td>
                                <?php endif; ?>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($donation->Status); ?>">
                                        <?php echo $donation->Status; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <?php if($data['request']->RequestStatus == 'Pending' || $data['request']->VerificationStatus == 'Rejected'): ?>
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this donation request? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-dismiss="modal">Cancel</button>
                    <form action="<?php echo URLROOT; ?>/recipients/deleteRequest/<?php echo $data['request']->RequestID; ?>" method="POST">
                        <button type="submit" class="btn btn-danger">Delete Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    // Initialize Bootstrap components
    document.addEventListener('DOMContentLoaded', function() {
    // Delete Modal Handling
    const deleteModal = document.getElementById('deleteModal');
    const deleteButtons = document.querySelectorAll('[data-toggle="modal"][data-target="#deleteModal"]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (deleteModal) {
                deleteModal.style.display = 'block';
            }
        });
    });
    
    // Close modal when clicking close button or outside modal
    if (deleteModal) {
        const closeButtons = deleteModal.querySelectorAll('.close, [data-dismiss="modal"]');
        
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                deleteModal.style.display = 'none';
            });
        });
        
        // Close when clicking outside modal
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                deleteModal.style.display = 'none';
            }
        });
    }
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>