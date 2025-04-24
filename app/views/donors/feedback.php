<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<div class="container donor-feedback-container">
    <!-- Flash Messages -->
    <?php flash('feedback_message'); ?>
    <?php flash('feedback_error'); ?>
    
    <div class="section-header">
        <h1><?php echo $data['title']; ?></h1>
        <p class="section-subtitle">Review impact reports and feedback from donation recipients</p>
    </div>
    
    <!-- Feedback Statistics -->
    <div class="feedback-stats-container">
        <div class="feedback-stats-card">
            <div class="stats-item">
                <div class="stats-value"><?php echo $data['feedbackStats']->TotalFeedback ?? 0; ?></div>
                <div class="stats-label">Total Feedback</div>
            </div>
            <div class="stats-item">
                <div class="stats-value highlight"><?php echo $data['feedbackStats']->UnreadFeedback ?? 0; ?></div>
                <div class="stats-label">Unread Feedback</div>
            </div>
            <div class="stats-item">
                <div class="stats-value"><?php echo $data['feedbackStats']->ImpactReports ?? 0; ?></div>
                <div class="stats-label">Impact Reports</div>
            </div>
        </div>
        
        <?php if ($data['feedbackStats']->UnreadFeedback > 0): ?>
        <form action="<?php echo URLROOT; ?>/donors/markAllFeedbackAsRead" method="POST" class="mark-all-read-form">
            <button type="submit" class="btn btn-outline-primary">
                <i class="fas fa-check-double"></i> Mark All as Read
            </button>
        </form>
        <?php endif; ?>
    </div>
    
    <?php if (empty($data['feedback'])): ?>
        <div class="empty-feedback-container">
            <div class="empty-state-icon">
                <i class="fas fa-comment-alt"></i>
            </div>
            <h3>No Feedback Yet</h3>
            <p>Recipients haven't sent any feedback for your donations yet. When they do, you'll see them listed here.</p>
            <a href="<?php echo URLROOT; ?>/donors/allRequests" class="btn btn-primary">Browse Donation Requests</a>
        </div>
    <?php else: ?>
        <!-- Feedback List -->
        <div class="feedback-list">
            <?php foreach($data['feedback'] as $feedback): ?>
                <div class="feedback-card <?php echo $feedback->ViewedByDonor ? '' : 'unread'; ?>">
                    <div class="feedback-header">
                        <div class="feedback-type-badge <?php echo $feedback->FeedbackType == 'ImpactReport' ? 'impact-badge' : 'general-badge'; ?>">
                            <i class="<?php echo $feedback->FeedbackType == 'ImpactReport' ? 'fas fa-chart-line' : 'fas fa-comment'; ?>"></i>
                            <?php echo $feedback->FeedbackType == 'ImpactReport' ? 'Impact Report' : 'General Feedback'; ?>
                        </div>
                        
                        <?php if (!$feedback->ViewedByDonor): ?>
                            <div class="unread-badge">
                                <i class="fas fa-circle"></i> Unread
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="feedback-content">
                        <div class="feedback-info">
                            <div class="feedback-title">
                                <h3><?php echo $feedback->RequestTitle; ?></h3>
                                <span class="feedback-date"><?php echo date('M j, Y', strtotime($feedback->CreatedDate)); ?></span>
                            </div>
                            <div class="feedback-recipient">
                                From: <strong><?php echo $feedback->RecipientFirstName . ' ' . $feedback->RecipientLastName; ?></strong>
                            </div>
                            <div class="donation-details">
                                <div class="donation-type">
                                    <i class="<?php echo $feedback->DonationType == 'Monetary' ? 'fas fa-dollar-sign' : 'fas fa-box'; ?>"></i>
                                    <?php echo $feedback->DonationType == 'Monetary' ? 'Monetary Donation' : 'Non-Monetary Donation'; ?>
                                </div>
                                <div class="donation-value">
                                    <?php if ($feedback->DonationType == 'Monetary'): ?>
                                        <span class="amount">Rs. <?php echo number_format($feedback->Amount, 2); ?></span>
                                    <?php else: ?>
                                        <span class="quantity"><?php echo number_format($feedback->QuantityDonated); ?> items</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="feedback-excerpt">
                                <?php 
                                    // Limit the content preview to 150 characters
                                    $excerpt = strlen($feedback->Content) > 150 ? 
                                               substr($feedback->Content, 0, 150) . '...' : 
                                               $feedback->Content;
                                    echo $excerpt;
                                ?>
                            </div>
                        </div>
                        <div class="feedback-actions">
                            <a href="<?php echo URLROOT; ?>/donors/viewFeedback/<?php echo $feedback->FeedbackID; ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($data['totalPages'] > 1): ?>
            <div class="pagination-container">
                <ul class="pagination">
                    <?php if ($data['currentPage'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo URLROOT; ?>/donors/feedback?page=<?php echo $data['currentPage'] - 1; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                        <li class="page-item <?php echo $i == $data['currentPage'] ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo URLROOT; ?>/donors/feedback?page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($data['currentPage'] < $data['totalPages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo URLROOT; ?>/donors/feedback?page=<?php echo $data['currentPage'] + 1; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>