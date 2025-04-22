<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<div class="container view-feedback-container">
    <div class="feedback-header">
        <a href="<?php echo URLROOT; ?>/donors/feedback" class="btn btn-sm btn-outline">
            <i class="fas fa-arrow-left"></i> Back to All Feedback
        </a>
        <h1><?php echo $data['title']; ?></h1>
    </div>
    
    <div class="feedback-detail-card">
        <div class="feedback-meta">
            <div class="feedback-type-badge <?php echo $data['feedback']->FeedbackType == 'ImpactReport' ? 'impact-badge' : 'general-badge'; ?>">
                <i class="<?php echo $data['feedback']->FeedbackType == 'ImpactReport' ? 'fas fa-chart-line' : 'fas fa-comment'; ?>"></i>
                <?php echo $data['feedback']->FeedbackType == 'ImpactReport' ? 'Impact Report' : 'General Feedback'; ?>
            </div>
            <div class="feedback-date">
                <i class="far fa-calendar-alt"></i> Received on <?php echo date('F j, Y', strtotime($data['feedback']->CreatedDate)); ?>
            </div>
        </div>
        
        <div class="feedback-content-section">
            <div class="feedback-title">
                <h2><?php echo $data['feedback']->RequestTitle; ?></h2>
            </div>
            
            <div class="feedback-sender">
                <div class="sender-profile">
                    <div class="sender-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="sender-info">
                        <div class="sender-name">
                            <?php echo $data['feedback']->RecipientFirstName . ' ' . $data['feedback']->RecipientLastName; ?>
                        </div>
                        <div class="sender-type">
                            <?php 
                                if(!empty($data['feedback']->OrganizationType)) {
                                    echo $data['feedback']->OrganizationType;
                                } else {
                                    echo 'Individual Recipient';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="feedback-message">
                <h3>Feedback Message</h3>
                <div class="message-content">
                    <?php echo nl2br($data['feedback']->Content); ?>
                </div>
            </div>
            
            <?php 
            // Check for impact images
            $impactImagePath = UPLOADS_PATH . '/impacts/' . $data['feedback']->FeedbackID . '.jpg';
            $impactImageUrl = UPLOADS_URL . '/impacts/' . $data['feedback']->FeedbackID . '.jpg';
            
            // Check for alternative extensions if jpg doesn't exist
            if(!file_exists($impactImagePath)) {
                $extensions = ['.png', '.jpeg', '.gif'];
                foreach($extensions as $ext) {
                    $testPath = UPLOADS_PATH . '/impacts/' . $data['feedback']->FeedbackID . $ext;
                    if(file_exists($testPath)) {
                        $impactImagePath = $testPath;
                        $impactImageUrl = UPLOADS_URL . '/impacts/' . $data['feedback']->FeedbackID . $ext;
                        break;
                    }
                }
            }
            
            if(file_exists($impactImagePath)): 
            ?>
            <div class="feedback-images">
                <h3>Impact Photos</h3>
                <div class="image-gallery">
                    <img src="<?php echo $impactImageUrl; ?>" alt="Impact Image" class="impact-image">
                </div>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($data['feedback']->ImpactVideoLink)): ?>
            <div class="feedback-video">
                <h3>Impact Video</h3>
                <div class="video-container">
                    <?php 
                        // Function to convert YouTube URLs to embed format
                        function getYoutubeEmbedUrl($url) {
                            $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
                            $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';
                            
                            if (preg_match($longUrlRegex, $url, $matches)) {
                                $youtube_id = $matches[3];
                            } else if (preg_match($shortUrlRegex, $url, $matches)) {
                                $youtube_id = $matches[1];
                            } else {
                                return false;
                            }
                            
                            return 'https://www.youtube.com/embed/' . $youtube_id;
                        }
                        
                        $embedUrl = getYoutubeEmbedUrl($data['feedback']->ImpactVideoLink);
                        
                        if($embedUrl) {
                            echo '<iframe width="100%" height="400" src="' . $embedUrl . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                        } else {
                            echo '<a href="' . $data['feedback']->ImpactVideoLink . '" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt"></i> View Video
                                  </a>';
                        }
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="donation-details-section">
            <h3>Donation Details</h3>
            <div class="donation-details-grid">
                <div class="detail-item">
                    <div class="detail-label">Donation Type</div>
                    <div class="detail-value">
                        <i class="<?php echo $data['feedback']->DonationType == 'Monetary' ? 'fas fa-dollar-sign' : 'fas fa-box'; ?>"></i>
                        <?php echo $data['feedback']->DonationType; ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Donation Date</div>
                    <div class="detail-value">
                        <i class="far fa-calendar"></i>
                        <?php echo date('M j, Y', strtotime($data['feedback']->DonationDate)); ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <?php echo $data['feedback']->DonationType == 'Monetary' ? 'Amount' : 'Quantity'; ?>
                    </div>
                    <div class="detail-value highlight">
                        <?php if($data['feedback']->DonationType == 'Monetary'): ?>
                            <i class="fas fa-money-bill-wave"></i>
                            Rs. <?php echo number_format($data['feedback']->Amount, 2); ?>
                        <?php else: ?>
                            <i class="fas fa-cubes"></i>
                            <?php echo number_format($data['feedback']->QuantityDonated); ?> 
                            <?php echo $data['feedback']->ItemName ?? 'items'; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Category</div>
                    <div class="detail-value">
                        <i class="fas fa-tag"></i>
                        <?php echo $data['feedback']->Category; ?>
                    </div>
                </div>
                
                <div class="detail-item full-width">
                    <div class="detail-label">Request Description</div>
                    <div class="detail-value description">
                        <?php echo $data['feedback']->RequestDescription; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="feedback-actions">
            <a href="<?php echo URLROOT; ?>/donors/feedback" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to All Feedback
            </a>
            
            <?php if ($data['feedback']->Status == 'Completed'): ?>
                <a href="<?php echo URLROOT; ?>/donations/donate/<?php echo $data['feedback']->RequestID; ?>" class="btn btn-primary">
                    <i class="fas fa-hand-holding-heart"></i> Donate Again
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

// Add this script to the end of your view_feedback.php file before the closing </body> tag

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all impact images
    const impactImages = document.querySelectorAll('.impact-image');
    
    // If no images, exit early
    if (impactImages.length === 0) return;
    
    // Create lightbox elements
    const lightbox = document.createElement('div');
    lightbox.className = 'feedback-lightbox';
    
    const lightboxContent = document.createElement('div');
    lightboxContent.className = 'lightbox-content';
    
    const lightboxImage = document.createElement('img');
    lightboxImage.className = 'lightbox-image';
    
    const closeButton = document.createElement('button');
    closeButton.className = 'lightbox-close';
    closeButton.innerHTML = '<i class="fas fa-times"></i>';
    
    // Assemble lightbox
    lightboxContent.appendChild(lightboxImage);
    lightboxContent.appendChild(closeButton);
    lightbox.appendChild(lightboxContent);
    document.body.appendChild(lightbox);
    
    // Add click event to each image
    impactImages.forEach(image => {
        image.addEventListener('click', function() {
            lightboxImage.src = this.src;
            lightbox.classList.add('active');
        });
    });
    
    // Close lightbox on button click
    closeButton.addEventListener('click', function() {
        lightbox.classList.remove('active');
    });
    
    // Close lightbox when clicking outside the image
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            lightbox.classList.remove('active');
        }
    });
    
    // Close lightbox with escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && lightbox.classList.contains('active')) {
            lightbox.classList.remove('active');
        }
    });
    
    // Add a class for single image styling
    if (impactImages.length === 1) {
        document.querySelector('.image-gallery').classList.add('single-image');
    }
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>