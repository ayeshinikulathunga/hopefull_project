<?php require APPROOT . '/views/includes/headers/recipient_header.php'; ?>

<div class="container create-feedback-page-container">
    <!-- Flash Messages -->
    <?php flash('feedback_message'); ?>
    
    <div class="feedback-form-container">
        <div class="form-header">
            <div class="header-left">
                <a href="<?php echo URLROOT; ?>/recipients/viewRequest/<?php echo $data['request']->RequestID; ?>" class="btn btn-sm btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Request
                </a>
                <h1><?php echo $data['title']; ?></h1>
                <p class="subtitle">Share how donations have impacted your cause with individual donors</p>
            </div>
        </div>
        
        <?php if (empty($data['donations'])): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-comment-slash"></i>
                </div>
                <h3>No Eligible Donations Found</h3>
                <p>There are no completed non-anonymous donations for this request yet. Once donors make non-anonymous donations, you'll be able to send them personalized impact feedback.</p>
                <a href="<?php echo URLROOT; ?>/recipients/viewRequest/<?php echo $data['request']->RequestID; ?>" class="btn btn-primary">
                    Return to Request
                </a>
            </div>
        <?php else: ?>
            <div class="form-card">
                <form action="<?php echo URLROOT; ?>/recipients/createDonorFeedback/<?php echo $data['request']->RequestID; ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-section">
                        <h3>Select Donations to Send Feedback</h3>
                        <p class="instructions">Choose donations from the list below. For each selected donation, you can send a personalized impact message.</p>
                        
                        <div class="donation-selection">
                            <?php foreach($data['donations'] as $donation): ?>
                                <div class="donation-item-selectable">
                                    <div class="checkbox-wrapper">
                                        <input type="checkbox" name="donations[]" id="donation-<?php echo $donation->DonationID; ?>" value="<?php echo $donation->DonationID; ?>" class="donation-checkbox">
                                        <label for="donation-<?php echo $donation->DonationID; ?>" class="checkbox-label"></label>
                                    </div>
                                    <div class="donation-details">
                                        <div class="donor-info">
                                            <div class="donor-name">
                                                <i class="fas fa-user"></i> <?php echo $donation->DonorName; ?>
                                            </div>
                                            <div class="donation-date">
                                                <?php echo date('M j, Y', strtotime($donation->DonationDate)); ?>
                                            </div>
                                        </div>
                                        <div class="donation-value">
                                            <?php if($data['request']->RequestType == 'Monetary'): ?>
                                                <span class="amount">Rs. <?php echo number_format($donation->Amount); ?></span>
                                            <?php else: ?>
                                                <span class="quantity"><?php echo number_format($donation->QuantityDonated); ?> items</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-section impact-messages-section" style="display: none;">
                        <h3>Create Impact Messages</h3>
                        <p class="instructions">Provide a personalized message for each donor explaining how their donation has made an impact on your cause.</p>
                        
                        <div class="impact-messages">
                            <?php foreach($data['donations'] as $donation): ?>
                                <div class="impact-message-form" id="impact-form-<?php echo $donation->DonationID; ?>" style="display: none;">
                                    <div class="message-header">
                                        <h4>Message for <?php echo $donation->DonorName; ?></h4>
                                        <div class="donation-summary">
                                            <span class="summary-label">Donation:</span>
                                            <?php if($data['request']->RequestType == 'Monetary'): ?>
                                                <span class="summary-value">Rs. <?php echo number_format($donation->Amount); ?></span>
                                            <?php else: ?>
                                                <span class="summary-value"><?php echo number_format($donation->QuantityDonated); ?> <?php echo $data['request']->ItemName ?? 'items'; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="impact-message-<?php echo $donation->DonationID; ?>">Impact Message <span class="required">*</span></label>
                                        <textarea name="impact_message[<?php echo $donation->DonationID; ?>]" id="impact-message-<?php echo $donation->DonationID; ?>" class="form-control" rows="5" placeholder="Explain how this donation has helped your cause..."></textarea>
                                        <div class="form-text">
                                            Share a personal story about the impact. Be specific about how their contribution has helped.
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="impact-image-<?php echo $donation->DonationID; ?>">Impact Image (Optional)</label>
                                        <input type="file" name="impact_image[<?php echo $donation->DonationID; ?>]" id="impact-image-<?php echo $donation->DonationID; ?>" class="form-control file-input" accept="image/jpeg, image/jpg, image/png">
                                        <div class="form-text">
                                            Add a photo showcasing the impact of their donation. Maximum size: 2MB. Formats: JPG, PNG.
                                        </div>
                                        <div class="image-preview" id="preview-<?php echo $donation->DonationID; ?>">
                                            <div class="preview-container">
                                                <div class="no-preview">
                                                    <i class="fas fa-image"></i>
                                                    <span>No image selected</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="impact-video-<?php echo $donation->DonationID; ?>">Video Link (Optional)</label>
                                        <input type="url" name="impact_video[<?php echo $donation->DonationID; ?>]" id="impact-video-<?php echo $donation->DonationID; ?>" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                                        <div class="form-text">
                                            You can include a YouTube or Vimeo link to a video showcasing the impact.
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="<?php echo URLROOT; ?>/recipients/viewRequest/<?php echo $data['request']->RequestID; ?>" class="btn btn-outline">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary send-feedback-btn" disabled>
                            <i class="fas fa-paper-plane"></i> Send Feedback
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle donation selection
    const donationCheckboxes = document.querySelectorAll('.donation-checkbox');
    const impactMessagesSection = document.querySelector('.impact-messages-section');
    const sendFeedbackBtn = document.querySelector('.send-feedback-btn');
    
    function updateFormVisibility() {
        let checkedCount = 0;
        
        // Hide all impact forms first
        document.querySelectorAll('.impact-message-form').forEach(form => {
            form.style.display = 'none';
        });
        
        // Show forms for checked donations
        donationCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkedCount++;
                const donationId = checkbox.value;
                const impactForm = document.getElementById('impact-form-' + donationId);
                if (impactForm) {
                    impactForm.style.display = 'block';
                }
            }
        });
        
        // Show/hide the impact messages section based on selection
        if (checkedCount > 0) {
            impactMessagesSection.style.display = 'block';
            sendFeedbackBtn.disabled = false;
        } else {
            impactMessagesSection.style.display = 'none';
            sendFeedbackBtn.disabled = true;
        }
    }
    
    donationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateFormVisibility);
    });
    
    // Image preview functionality
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const donationId = this.id.replace('impact-image-', '');
            const previewContainer = document.getElementById('preview-' + donationId);
            
            if (previewContainer) {
                const previewImage = previewContainer.querySelector('img') || document.createElement('img');
                previewImage.className = 'preview-image';
                
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        
                        // Clear the no-preview div and add the image
                        const noPreview = previewContainer.querySelector('.preview-container .no-preview');
                        if (noPreview) {
                            const previewContainer2 = previewContainer.querySelector('.preview-container');
                            previewContainer2.innerHTML = '';
                            previewContainer2.appendChild(previewImage);
                        }
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            }
        });
    });
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>