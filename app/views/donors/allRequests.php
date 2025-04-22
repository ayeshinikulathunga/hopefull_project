<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>



<!-- Filter Section -->
<section class="filter-section">
    <div class="container">
        <form action="<?php echo URLROOT; ?>/donors/allRequests" method="GET" class="filter-container">
            <div class="filter-search-box">
                <input type="text" name="search" placeholder="Search requests..." class="filter-search-input" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="filter-btn-search"><i class="fas fa-search"></i></button>
            </div>
            
            <div class="filter-category">
                <select name="category" id="category-filter">
                    <option value="">All Categories</option>
                    <option value="Healthcare" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Healthcare') ? 'selected' : ''; ?>>Healthcare</option>
                    <option value="Education" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Education') ? 'selected' : ''; ?>>Education</option>
                    <option value="Community" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Community') ? 'selected' : ''; ?>>Community</option>
                    <option value="Sports" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Sports') ? 'selected' : ''; ?>>Sports</option>
                    <option value="MakeAWish" <?php echo (isset($_GET['category']) && $_GET['category'] == 'MakeAWish') ? 'selected' : ''; ?>>Make A Wish</option>
                </select>
            </div>
            
            <div class="filter-type">
                <select name="type" id="type-filter">
                    <option value="">All Types</option>
                    <option value="Monetary" <?php echo (isset($_GET['type']) && $_GET['type'] == 'Monetary') ? 'selected' : ''; ?>>Monetary</option>
                    <option value="NonMonetary" <?php echo (isset($_GET['type']) && $_GET['type'] == 'NonMonetary') ? 'selected' : ''; ?>>Non-Monetary</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <?php if(isset($_GET['search']) || isset($_GET['category']) || isset($_GET['type'])): ?>
                    <a href="<?php echo URLROOT; ?>/donors/allRequests" class="clear-btn">Clear Filters</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<!-- All Requests Section -->
<section class="requests-section">
    <div class="container">
        <?php flash('donation_message'); ?>
        
        <?php if(isset($data['totalRequests']) && $data['totalRequests'] > 0): ?>
            <div class="request-summary">
                <p>Showing <?php echo count($data['requests']); ?> of <?php echo $data['totalRequests']; ?> requests</p>
            </div>
            
            <div class="requests-grid">
                <?php foreach($data['requests'] as $request): ?>
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
                                <?php if($request->RequestType == 'Monetary'): ?>
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
                                <?php else: ?>
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
                                            <span><?php echo ($request->QuantityReceived ?? 0); ?> of <?php echo ($request->QuantityNeeded ?? 0); ?> <?php echo isset($request->ItemName) ? $request->ItemName : 'items'; ?></span>
                                            <span><?php echo round($percentage); ?>%</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <p class="deadline">
                                    <?php
                                    $deadline = strtotime($request->Deadline);
                                    $now = time();
                                    $daysLeft = ceil(($deadline - $now) / (60 * 60 * 24));
                                    
                                    if ($daysLeft > 0) {
                                        echo "Deadline: " . date('M d, Y', $deadline) . " (" . $daysLeft . " days left)";
                                    } else {
                                        echo "Deadline: <span class='expired'>Expired</span>";
                                    }
                                    ?>
                                </p>
                            </div>
                            
                            <div class="request-actions">
                                <a href="<?php echo URLROOT; ?>/donations/donate/<?php echo $request->RequestID; ?>" class="donate-btn">Donate Now</a>
                                <a href="<?php echo URLROOT; ?>/donors/details/<?php echo $request->RequestID; ?>" class="view-details-btn">View Details</a>
                                
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if(isset($data['totalPages']) && $data['totalPages'] > 1): ?>
                <div class="pagination">
                    <?php if($data['currentPage'] > 1): ?>
                        <a href="<?php echo URLROOT; ?>/donors/allRequests?page=<?php echo $data['currentPage'] - 1; ?><?php echo $data['queryString']; ?>" class="pagination-arrow">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $data['totalPages']; $i++): ?>
                        <a href="<?php echo URLROOT; ?>/donors/allRequests?page=<?php echo $i; ?><?php echo $data['queryString']; ?>" 
                           class="pagination-link <?php echo $i == $data['currentPage'] ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if($data['currentPage'] < $data['totalPages']): ?>
                        <a href="<?php echo URLROOT; ?>/donors/allRequests?page=<?php echo $data['currentPage'] + 1; ?><?php echo $data['queryString']; ?>" class="pagination-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-requests">
                <i class="fas fa-search mb-3"></i>
                <h3>No donation requests found</h3>
                <p>No donation requests match your search criteria. Try adjusting your filters or check back later for new requests.</p>
            </div>
        <?php endif; ?>
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

    // Function to close the modal
    function closeProofModal() {
        modal.style.display = "none";
        document.querySelector(".modal-body").innerHTML = '<iframe id="proofDocument" src="" frameborder="0"></iframe>';
    }

    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            closeProofModal();
        }
    }

    // Filter change handlers
    document.getElementById('category-filter').addEventListener('change', function() {
        this.form.submit();
    });

    document.getElementById('type-filter').addEventListener('change', function() {
        this.form.submit();
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>