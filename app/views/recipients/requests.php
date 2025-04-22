<?php require APPROOT . '/views/includes/headers/recipient_header.php'; ?>

<div class="container requests-page-container">
    <!-- Flash Messages -->
    <?php flash('request_message'); ?>
    
    <div class="requests-header">
        <div>
            <h1>My Donation Requests</h1>
            <p class="subtitle">Manage all your donation requests</p>
        </div>
        <a href="<?php echo URLROOT; ?>/recipients/createRequest" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Request
        </a>
    </div>
    
    <?php if(empty($data['requests'])): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3>No Donation Requests Yet</h3>
            <p>You haven't created any donation requests yet. Start by creating a new request to receive donations for your cause.</p>
            <a href="<?php echo URLROOT; ?>/recipients/createRequest" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create First Request
            </a>
        </div>
    <?php else: ?>
        <div class="requests-grid">
            <?php foreach($data['requests'] as $request): ?>
                <div class="request-card">
                    <?php if($request->HasImage): ?>
                        <div class="request-image">
                            <img src="<?php echo URLROOT; ?>/uploads/requests/<?php echo $request->RequestID; ?>.jpg?v=<?php echo time(); ?>" alt="<?php echo $request->Title; ?>">
                            <span class="request-status <?php echo strtolower($request->RequestStatus); ?>">
                                <?php echo $request->RequestStatus; ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="request-image">
                            <div class="no-image">
                                <?php if($request->RequestType == 'Monetary'): ?>
                                    <i class="fas fa-hand-holding-usd"></i>
                                <?php else: ?>
                                    <i class="fas fa-box-open"></i>
                                <?php endif; ?>
                                <span>No Image</span>
                            </div>
                            <span class="request-status <?php echo strtolower($request->RequestStatus); ?>">
                                <?php echo $request->RequestStatus; ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="request-details">
                        <h3 class="request-title"><?php echo $request->Title; ?></h3>
                        
                        <div class="request-meta">
                            <span class="request-category"><?php echo $request->Category; ?></span>
                            <span class="request-type">
                                <?php if($request->RequestType == 'Monetary'): ?>
                                    <i class="fas fa-hand-holding-usd"></i> Monetary
                                <?php else: ?>
                                    <i class="fas fa-box-open"></i> Non-Monetary
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="request-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $request->Progress; ?>%"></div>
                            </div>
                            <span class="progress-text"><?php echo $request->Progress; ?>% Complete</span>
                        </div>
                        
                        <div class="request-stats">
                            <?php if($request->RequestType == 'Monetary'): ?>
                                <div class="stat">
                                    <i class="fas fa-bullseye"></i>
                                    <span>Target: Rs. <?php echo number_format($request->TargetAmount); ?></span>
                                </div>
                                <div class="stat">
                                    <i class="fas fa-coins"></i>
                                    <span>Raised: Rs. <?php echo number_format($request->CurrentAmount); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="stat">
                                    <i class="fas fa-list"></i>
                                    <span><?php echo $request->ItemName; ?></span>
                                </div>
                                <div class="stat">
                                    <i class="fas fa-box"></i>
                                    <span>Received: <?php echo number_format($request->QuantityReceived); ?>/<?php echo number_format($request->QuantityNeeded); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="stat">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Deadline: <?php echo date('M j, Y', strtotime($request->Deadline)); ?></span>
                            </div>
                        </div>
                        
                        <div class="request-actions">
                            <a href="<?php echo URLROOT; ?>/recipients/viewRequest/<?php echo $request->RequestID; ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <?php if($request->RequestStatus == 'Pending' || $request->VerificationStatus == 'Rejected'): ?>
                                <a href="<?php echo URLROOT; ?>/recipients/editRequest/<?php echo $request->RequestID; ?>" class="btn btn-outline">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
  