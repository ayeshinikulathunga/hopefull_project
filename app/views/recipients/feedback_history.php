<?php require APPROOT . '/views/includes/headers/recipient_header.php'; ?>

<div class="container feedback-history-page-container">
  
    <?php flash('feedback_message'); ?>
    
    <div class="feedback-history-container">
        <div class="history-header">
            <div class="header-left">
                <a href="<?php echo URLROOT; ?>/recipients/dashboard" class="btn1 btn1-sm btn1-outline">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <h1>Feedback History</h1>
                <p class="subtitle">All impact feedback you've sent to donors</p>
            </div>
        </div>
        
        <?php if (empty($data['feedback'])): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-comment-slash"></i>
                </div>
                <h3>No Feedback Sent Yet</h3>
                <p>You haven't sent any impact feedback to donors yet. When you send feedback, it will appear here.</p>
                <a href="<?php echo URLROOT; ?>/recipients/requests" class="btn1 btn1-primary">
                    View My Requests
                </a>
            </div>
        <?php else: ?>
            <div class="feedback-display-container">
                <?php foreach($data['feedback'] as $feedback): ?>
                    <div class="feedback-item">
                        <div class="feedback-header">
                            <div class="feedback-meta">
                                <span class="feedback-title">
                                    Feedback to <?php echo $feedback->DonorFirstName . ' ' . $feedback->DonorLastName; ?>
                                </span>
                                <span class="feedback-date">
                                    Sent on <?php echo date('F j, Y', strtotime($feedback->CreatedDate)); ?>
                                </span>
                            </div>
                            <div class="feedback-type">
                                <?php echo $feedback->FeedbackType; ?>
                            </div>
                        </div>
                        
                        <div class="feedback-request-details">
                            <span class="request-label">For request:</span>
                            <span class="request-title"><?php echo $feedback->RequestTitle; ?></span>
                            <span class="donation-details">
                                <?php if($feedback->DonationType == 'Monetary'): ?>
                                    <span class="amount">Rs. <?php echo number_format($feedback->Amount); ?></span>
                                <?php else: ?>
                                    <span class="quantity"><?php echo number_format($feedback->QuantityDonated); ?> items</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="feedback-content">
                            <?php echo nl2br(htmlspecialchars($feedback->Content)); ?>
                        </div>
                        
                        <div class="feedback-actions">
                        <form action="<?php echo URLROOT; ?>/recipients/deleteFeedback/<?php echo $feedback->FeedbackID; ?>" method="POST" style="display:inline;">
                            <button type="submit" class="btn1 btn1-sm btn1-danger" onclick="return confirm('Are you sure you want to delete this feedback?');">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>