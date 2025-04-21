<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<div class="donor-dashboard">
    <!-- 1. Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Welcome, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Donor'; ?>!</h1>
                <p>Thank you for your generosity and support. Together, we can make a difference.</p>
            </div>
        </div>
    </section>

    <!-- 2. Recent Requests Section -->
    <section class="recent-requests">
        <div class="container">
        <div class="section-header">
            <h2 class="section-title">Recent Donation Requests</h2>
            <a href="<?php echo URLROOT; ?>/donors/allRequests" class="view-all-btn">View All Requests <i class="fas fa-arrow-right"></i></a>
        </div>
            <div class="requests-container">
                <?php if(isset($data['recentRequests']) && !empty($data['recentRequests'])) : ?>
                    <?php foreach($data['recentRequests'] as $request) : ?>
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
                                <p class="request-description"><?php echo substr($request->Description, 0, 100); ?>...</p>
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
                <?php else : ?>
                    <div class="no-requests">
                        <p>No donation requests available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </section>

    <!-- 3. Services Cards Section -->
    <section class="services-section">
        <div class="container">
            <h2 class="section-title">Ways You Can Help</h2>
            <div class="services-container">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>Make a Donation</h3>
                    <p>Support causes that matter with monetary donations. Every contribution counts towards creating meaningful impact in the lives of those in need.</p>
                    <a href="<?php echo URLROOT; ?>/donations/requests" class="service-btn">Donate Now</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3>Donate Items</h3>
                    <p>Contribute non-monetary items like books, clothing, and supplies to those in need. Your items can make a significant difference in someone's life.</p>
                    <a href="<?php echo URLROOT; ?>/donations/items" class="service-btn">Donate Items</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Volunteer</h3>
                    <p>Offer your time and skills to help others. Join volunteer opportunities and make a direct impact in your community through personal engagement.</p>
                    <a href="<?php echo URLROOT; ?>/volunteers/opportunities" class="service-btn">Volunteer</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Marketplace Section -->
    <section class="marketplace-section">
        <div class="container">
            <div class="marketplace-content">
                <div class="marketplace-info">
                    <h2>Shop at Our Marketplace</h2>
                    <p>Discover unique handcrafted products made by individuals with disabilities. Every purchase supports their independence and creativity while bringing beautiful artisanal items into your life.</p>
                    <a href="<?php echo URLROOT; ?>/marketplace" class="marketplace-btn">Shop Now</a>
                </div>
                <div class="marketplace-image">
                    <img src="<?php echo URLROOT; ?>/images/marketplace-hero.jpg" alt="Hopefull Marketplace">
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Stats Section -->
    <section class="stats-section">
        <div class="container">
            <h2 class="section-title">Your Impact</h2>
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Donations</h3>
                        <p class="stat-value">Rs. <?php echo number_format($data['donorStats']->TotalDonations ?? 0); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Donations Made</h3>
                        <p class="stat-value"><?php echo $data['donorStats']->DonationCount ?? 0; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Badges Earned</h3>
                        <p class="stat-value"><?php echo $data['badgeCount'] ?? 0; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Lives Impacted</h3>
                        <p class="stat-value"><?php echo $data['impactCount'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
</div>

<!-- JavaScript for the Proof Modal -->
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

// Alternative function that gives download option
function openProofModalWithOptions(requestId, requestTitle, documentExists) {
    modal.style.display = "block";
    proofModalTitle.textContent = "Documentation for: " + requestTitle;
    
    if (documentExists === true || documentExists === 'true') {
        fetch(`${URLROOT}/donations/viewProof/${requestId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show PDF options
                    document.querySelector(".modal-body").innerHTML = `
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; text-align: center;">
                            <i class="fas fa-file-pdf" style="font-size: 4rem; color: #e74c3c; margin-bottom: 20px;"></i>
                            <p style="font-size: 1.2rem; color: #666; margin-bottom: 30px;">Please choose how to view this document:</p>
                            <div style="display: flex; gap: 20px;">
                                <a href="${data.url}" target="_blank" class="btn donate-btn">
                                    <i class="fas fa-external-link-alt"></i> Open in New Tab
                                </a>
                                <a href="${data.url}" download="proof_document_${requestId}.pdf" class="btn view-proof-btn">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                            </div>
                        </div>`;
                } else {
                    // Show error message
                    document.querySelector(".modal-body").innerHTML = `
                        <div class="no-document-message" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; text-align: center;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #e74c3c; margin-bottom: 20px;"></i>
                            <p style="font-size: 1.2rem; color: #666;">${data.message || 'Document preview not available. Please contact the administrator for more information.'}</p>
                        </div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.querySelector(".modal-body").innerHTML = `
                    <div class="no-document-message" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; text-align: center;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #e74c3c; margin-bottom: 20px;"></i>
                        <p style="font-size: 1.2rem; color: #666;">Error loading document. Please try again later.</p>
                    </div>`;
            });
    } else {
        // Show message when document doesn't exist
        document.querySelector(".modal-body").innerHTML = `
            <div class="no-document-message" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; text-align: center;">
                <i class="fas fa-file-excel" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2rem; color: #666;">Document preview not available. Please contact the administrator for more information.</p>
            </div>`;
    }
}

// Function to close the modal
function closeProofModal() {
    modal.style.display = "none";
    document.querySelector(".modal-body").innerHTML = '<div id="proofDocument"></div>';
}

// Close the modal when clicking outside of it or on the X
window.onclick = function(event) {
    if (event.target == modal) {
        closeProofModal();
    }
}

document.querySelector('.close-modal').addEventListener('click', closeProofModal); 
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>