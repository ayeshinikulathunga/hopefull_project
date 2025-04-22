<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<div class="donation-details">
    <div class="container">
        <!-- Breadcrumb Navigation -->
        <div class="donation-breadcrumb">
            <a href="<?php echo URLROOT; ?>">Home</a> &gt;
            <a href="<?php echo URLROOT; ?>/donors/dashboard">Dashboard</a> &gt;
            <span><?php echo $data['request']->Title; ?></span>
        </div>

        <?php if(isset($data['request']) && $data['request']) : ?>
            <!-- Donation Detail Content -->
            <div class="donation-detail__container">
                <!-- Left Column - Image -->
                <div class="donation-detail__images">
                    <?php
                    // Try common image extensions
                    $extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    $imageFound = false;
                    $imagePath = URLROOT . '/img/placeholder.jpg'; // Default to placeholder
                    
                    foreach($extensions as $ext) {
                        $testPath = APPROOT . '/../public/uploads/requests/' . $data['request']->RequestID . '.' . $ext;
                        if(file_exists($testPath)) {
                            $imagePath = URLROOT . '/uploads/requests/' . $data['request']->RequestID . '.' . $ext;
                            $imageFound = true;
                            break;
                        }
                    }
                    ?>
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $data['request']->Title; ?>" class="donation-detail__main-image">
                    <?php if($data['request']->RequestType == 'Monetary') : ?>
                        <div class="donation-type monetary">Monetary Donation</div>
                    <?php else : ?>
                        <div class="donation-type nonmonetary">Non-Monetary Donation</div>
                    <?php endif; ?>
                </div>

                <!-- Right Column - Info -->
                <div class="donation-detail__info">
                    <h1><?php echo $data['request']->Title; ?></h1>
                    
                    <div class="donation-detail__meta">
                        <span class="donation-detail__category"><?php echo $data['request']->Category; ?></span>
                        <span class="donation-detail__recipient">
                            Requested by: <?php echo isset($data['request']->RecipientName) ? $data['request']->RecipientName : 'Anonymous'; ?>
                        </span>
                    </div>

                    <div class="donation-detail__description">
                        <p><?php echo $data['request']->Description; ?></p>
                    </div>
                    
                    <div class="donation-detail__progress-section">
                        <h3>Donation Progress</h3>
                        
                        <?php if($data['request']->RequestType == 'Monetary') : ?>
                            <div class="donation-detail__progress">
                                <?php 
                                    $percentage = 0;
                                    if(isset($data['request']->CurrentAmount) && isset($data['request']->TargetAmount) && $data['request']->TargetAmount > 0) {
                                        $percentage = ($data['request']->CurrentAmount / $data['request']->TargetAmount) * 100;
                                    }
                                ?>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress-stats">
                                        <span>Rs. <?php echo number_format($data['request']->CurrentAmount ?? 0); ?> raised</span>
                                        <span>of Rs. <?php echo number_format($data['request']->TargetAmount ?? 0); ?> goal</span>
                                    </div>
                                    <div class="progress-percentage"><?php echo round($percentage); ?>%</div>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="donation-detail__progress">
                                <?php 
                                    $percentage = 0;
                                    if(isset($data['request']->QuantityReceived) && isset($data['request']->QuantityNeeded) && $data['request']->QuantityNeeded > 0) {
                                        $percentage = ($data['request']->QuantityReceived / $data['request']->QuantityNeeded) * 100;
                                    }
                                ?>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <div class="progress-info">
                                    <div class="progress-stats">
                                        <span><?php echo ($data['request']->QuantityReceived ?? 0); ?> items received</span>
                                        <span>of <?php echo ($data['request']->QuantityNeeded ?? 0); ?> items needed</span>
                                    </div>
                                    <div class="progress-percentage"><?php echo round($percentage); ?>%</div>
                                </div>
                                
                                <div class="donation-detail__item-info">
                                    <p><strong>Item Needed:</strong> <?php echo isset($data['itemDetails']) ? $data['itemDetails']->ItemName : 'Items'; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="donation-detail__deadline">
                            <p><i class="fas fa-calendar-alt"></i> Deadline: <?php echo date('M d, Y', strtotime($data['request']->Deadline)); ?></p>
                            <?php 
                                $today = new DateTime();
                                $deadline = new DateTime($data['request']->Deadline);
                                $diff = $today->diff($deadline);
                                if($deadline > $today) {
                                    echo '<p class="days-left"><i class="fas fa-clock"></i> ' . $diff->days . ' days left</p>';
                                } else {
                                    echo '<p class="expired"><i class="fas fa-exclamation-circle"></i> Deadline has passed</p>';
                                }
                            ?>
                        </div>
                    </div>
                    
                    <div class="donation-detail__actions">
                        <a href="<?php echo URLROOT; ?>/donations/donate/<?php echo $data['request']->RequestID; ?>" class="donation-btn">
                            <i class="fas fa-hand-holding-heart"></i> Donate Now
                        </a>
                        
                        <?php if(isset($data['request']->ProofDocumentExists) && $data['request']->ProofDocumentExists): ?>
                            <!-- Option 1: Open directly in new tab -->
                            <a href="<?php echo URLROOT; ?>/donations/downloadProof/<?php echo $data['request']->RequestID; ?>" target="_blank" class="view-proof-btn">
                                <i class="fas fa-file-alt"></i> View Proof
                            </a>
                        <?php else: ?>
                            <a href="#" class="view-proof-btn" onclick="openProofModal('<?php echo $data['request']->RequestID; ?>', '<?php echo $data['request']->Title; ?>', false)">
                                <i class="fas fa-file-alt"></i> View Proof
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information Tabs -->
            <div class="donation-tabs">
                <div class="donation-tab-buttons">
                    <button class="donation-tab-btn active" data-tab="details">Details</button>
                    <button class="donation-tab-btn" data-tab="impact">Impact</button>
                    <button class="donation-tab-btn" data-tab="updates">Updates</button>
                </div>
                
                <!-- Tab Content -->
                <div class="donation-tab-content active" id="details-tab">
                    <h3>About This Donation Request</h3>
                    <p><?php echo $data['request']->Description; ?></p>
                    
                    <?php if($data['request']->RequestType == 'NonMonetary' && isset($data['itemDetails'])): ?>
                    <div class="donation-location-info">
                        <h4>Drop-off Information</h4>
                        <p><strong>Location:</strong> <?php echo $data['itemDetails']->DropOffLocation; ?></p>
                        <p><strong>Province:</strong> <?php echo $data['itemDetails']->Province; ?></p>
                        <p><strong>Date & Time:</strong> <?php echo date('M d, Y h:i A', strtotime($data['itemDetails']->DropOffTime)); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="donation-tab-content" id="impact-tab">
                    <h3>Your Impact</h3>
                    <p>By supporting this cause, you are directly contributing to making a positive change in someone's life. Every donation, regardless of size, adds up to create meaningful impact.</p>
                    
                    <div class="impact-cards">
                        <div class="impact-card">
                            <div class="impact-icon">
                                <i class="fas fa-hands-helping"></i>
                            </div>
                            <h4>Support</h4>
                            <p>Your donation provides essential support to individuals and communities in need.</p>
                        </div>
                        <div class="impact-card">
                            <div class="impact-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h4>Compassion</h4>
                            <p>Your generosity shows compassion and creates hope for a better future.</p>
                        </div>
                        <div class="impact-card">
                            <div class="impact-icon">
                                <i class="fas fa-globe-asia"></i>
                            </div>
                            <h4>Community</h4>
                            <p>Your contribution helps build stronger, more resilient communities.</p>
                        </div>
                    </div>
                </div>
                
                <div class="donation-tab-content" id="updates-tab">
                    <h3>Updates on This Request</h3>
                    <?php if(isset($data['updates']) && !empty($data['updates'])): ?>
                        <div class="updates-timeline">
                            <?php foreach($data['updates'] as $update): ?>
                                <div class="update-item">
                                    <div class="update-date"><?php echo date('M d, Y', strtotime($update->UpdateDate)); ?></div>
                                    <div class="update-content">
                                        <h4><?php echo $update->UpdateTitle; ?></h4>
                                        <p><?php echo $update->UpdateContent; ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No updates available for this request yet. Check back later for progress updates.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Similar Donation Requests -->
            <?php if(isset($data['similarRequests']) && !empty($data['similarRequests'])): ?>
            <div class="similar-donations">
                <h2 class="section-title">Similar Donation Requests</h2>
                <div class="requests-container">
                    <?php foreach($data['similarRequests'] as $request): ?>
                        <div class="request-card">
                            <div class="request-image">
                                <?php
                                // Try common image extensions
                                $extensions = ['jpg', 'jpeg', 'png', 'gif'];
                                $imageFound = false;
                                $imagePath = URLROOT . '/img/placeholder.jpg'; // Default to placeholder
                                
                                foreach($extensions as $ext) {
                                    $testPath = APPROOT . '/../public/uploads/requests/' . $request->RequestID . '.' . $ext;
                                    if(file_exists($testPath)) {
                                        $imagePath = URLROOT . '/uploads/requests/' . $request->RequestID . '.' . $ext;
                                        $imageFound = true;
                                        break;
                                    }
                                }
                                ?>
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo $request->Title; ?>">
                            </div>
                            <div class="request-details">
                                <h3><?php echo $request->Title; ?></h3>
                                <span class="request-category"><?php echo $request->Category; ?></span>
                                <div class="request-recipient">
                                    <small>Requested by: <?php echo isset($request->RecipientName) ? $request->RecipientName : 'Anonymous'; ?></small>
                                </div>
                                <div class="request-meta">
                                    <?php if($request->RequestType == 'Monetary') : ?>
                                        <div class="progress-container">
                                            <?php 
                                                $percentage = 0;
                                                if(isset($request->CurrentAmount) && isset($request->TargetAmount) && $request->TargetAmount > 0) {
                                                    $percentage = ($request->CurrentAmount / $request->TargetAmount) * 100;
                                                }
                                            ?>
                                            <div class="progress-bar">
                                                <div class="progress" style="width: <?php echo $percentage; ?>%"></div>
                                            </div>
                                            <div class="progress-info">
                                                <span>Rs. <?php echo number_format($request->CurrentAmount ?? 0); ?> of Rs. <?php echo number_format($request->TargetAmount ?? 0); ?></span>
                                                <span><?php echo round($percentage); ?>%</span>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div class="progress-container">
                                            <?php 
                                                $percentage = 0;
                                                if(isset($request->QuantityReceived) && isset($request->QuantityNeeded) && $request->QuantityNeeded > 0) {
                                                    $percentage = ($request->QuantityReceived / $request->QuantityNeeded) * 100;
                                                }
                                            ?>
                                            <div class="progress-bar">
                                                <div class="progress" style="width: <?php echo $percentage; ?>%"></div>
                                            </div>
                                            <div class="progress-info">
                                                <span><?php echo ($request->QuantityReceived ?? 0); ?> of <?php echo ($request->QuantityNeeded ?? 0); ?> items</span>
                                                <span><?php echo round($percentage); ?>%</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <p class="deadline">Deadline: <?php echo date('M d, Y', strtotime($request->Deadline)); ?></p>
                                </div>
                                
                                <div class="request-actions">
                                    <a href="<?php echo URLROOT; ?>/donations/donate/<?php echo $request->RequestID; ?>" class="donate-btn">Donate Now</a>
                                    <a href="<?php echo URLROOT; ?>/donors/details/<?php echo $request->RequestID; ?>" class="view-details-btn">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-request">
                <h2>Donation Request Not Found</h2>
                <p>The donation request you're looking for may have been removed or is no longer available.</p>
                <a href="<?php echo URLROOT; ?>/donors/dashboard" class="back-btn">Back to Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Proof Document Modal -->
<div id="proofModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeProofModal()">&times;</span>
        <div class="modal-header">
            <h2 id="proofModalTitle">Request Documentation</h2>
        </div>
        <div class="modal-body">
            <iframe id="proofDocument" src="" frameborder="0"></iframe>
        </div>
    </div>
</div>

<!-- JavaScript for the modal and tabs -->
<script>
// Get the modal
var modal = document.getElementById("proofModal");
var proofModalTitle = document.getElementById("proofModalTitle");

// Function to open the modal
function openProofModal(requestId, requestTitle, documentExists) {
    if (documentExists === true || documentExists === 'true') {
        // Instead of showing in modal, directly check if document exists and open in new tab
        fetch(`${URLROOT}/donations/viewProof/${requestId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Open the PDF in a new tab
                    window.open(data.url, '_blank');
                } else {
                    // If document doesn't exist, show error modal
                    showErrorModal(requestTitle, data.message || 'Document preview not available. Please contact the administrator for more information.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal(requestTitle, 'Error loading document. Please try again later.');
            });
    } else {
        // Show message when document doesn't exist
        showErrorModal(requestTitle, 'Document preview not available. Please contact the administrator for more information.');
    }
}

// Function to show error modal
function showErrorModal(requestTitle, errorMessage) {
    modal.style.display = "block";
    proofModalTitle.textContent = "Documentation for: " + requestTitle;
    
    document.querySelector(".modal-body").innerHTML = `
        <div class="no-document-message" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; text-align: center;">
            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #e74c3c; margin-bottom: 20px;"></i>
            <p style="font-size: 1.2rem; color: #666;">${errorMessage}</p>
        </div>`;
}

// Function to close the modal
function closeProofModal() {
    modal.style.display = "none";
    document.querySelector(".modal-body").innerHTML = '<div id="proofDocument"></div>';
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    if (event.target == modal) {
        closeProofModal();
    }
}

// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.donation-tab-btn');
    const tabContents = document.querySelectorAll('.donation-tab-content');
    
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
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>