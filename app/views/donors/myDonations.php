<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<section class="dn-donations">
    <div class="container">
        <h1 class="dn-donations__title">Your Donations</h1>
        
        <?php flash('donation_message'); ?>
        
        <?php if(empty($data['donations'])): ?>
            <div class="dn-donations__empty">
                <i class="fas fa-hand-holding-heart fa-3x"></i>
                <h2>No donations yet</h2>
                <p>You haven't made any donations yet. Start making a difference by supporting causes that matter.</p>
                <a href="<?php echo URLROOT; ?>/donors/allRequests" class="dn-donations__donate-btn">Donate Now</a>
            </div>
        <?php else: ?>
            <div class="dn-donations__filter">
                <div class="dn-donations__filter-group">
                    <label for="donation-type">Filter by Type:</label>
                    <select id="donation-type" onchange="filterDonations()">
                        <option value="all">All Donations</option>
                        <option value="Monetary">Monetary</option>
                        <option value="NonMonetary">Non-Monetary</option>
                    </select>
                </div>
                
                <div class="dn-donations__filter-group">
                    <label for="donation-status">Filter by Status:</label>
                    <select id="donation-status" onchange="filterDonations()">
                        <option value="all">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="dn-donations__filter-group">
                    <label for="donation-date">Sort by:</label>
                    <select id="donation-date" onchange="sortDonations()">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>
            </div>
            
            <div class="dn-donations__list" id="donations-container">
                <?php foreach($data['donations'] as $donation): ?>
                    <div class="dn-donations__card" data-type="<?php echo $donation->DonationType; ?>" data-status="<?php echo $donation->Status; ?>" data-date="<?php echo strtotime($donation->DonationDate); ?>">
                        <div class="dn-donations__header">
                            <div class="dn-donations__id">
                                <h3>Donation #<?php echo $donation->DonationID; ?></h3>
                                <span class="dn-donations__date"><?php echo date('F j, Y', strtotime($donation->DonationDate)); ?></span>
                            </div>
                            <div class="dn-donations__type-status">
                                <span class="dn-donations__type dn-donations__type-<?php echo strtolower($donation->DonationType); ?>">
                                    <?php echo $donation->DonationType; ?>
                                </span>
                                <span class="dn-donations__status dn-donations__status-<?php echo strtolower($donation->Status); ?>">
                                    <?php echo $donation->Status; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="dn-donations__request">
                            <span class="dn-donations__request-category"><?php echo $donation->Category; ?></span>
                            <h4 class="dn-donations__request-title"><?php echo $donation->Title; ?></h4>
                        </div>
                        
                        <div class="dn-donations__details">
                            <?php if($donation->DonationType == 'Monetary'): ?>
                                <div class="dn-donations__amount">
                                    <span class="dn-donations__amount-label">Amount:</span>
                                    <span class="dn-donations__amount-value">Rs. <?php echo number_format($donation->Amount, 2); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="dn-donations__item">
                                    <span class="dn-donations__item-label">Item:</span>
                                    <span class="dn-donations__item-name"><?php echo isset($donation->ItemName) ? $donation->ItemName : 'Items'; ?></span>
                                    <span class="dn-donations__item-quantity">(Qty: <?php echo $donation->QuantityDonated; ?>)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="dn-donations__actions">
                            <a href="<?php echo URLROOT; ?>/donations/donationDetails/<?php echo $donation->DonationID; ?>" class="dn-donations__details-btn">
                                <i class="fas fa-file-alt"></i> View Details
                            </a>
                            
                            <?php if($donation->DonationType == 'NonMonetary' && $donation->Status == 'Pending'): ?>
                                <button class="dn-donations__cancel-btn" onclick="cancelDonation('<?php echo $donation->DonationID; ?>')">
                                    <i class="fas fa-times-circle"></i> Cancel
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination (if needed) -->
            <?php if(isset($data['totalPages']) && $data['totalPages'] > 1): ?>
                <div class="dn-donations__pagination">
                    <?php for($i = 1; $i <= $data['totalPages']; $i++): ?>
                        <a href="<?php echo URLROOT; ?>/donors/donations/<?php echo $i; ?>" class="dn-donations__pagination-link <?php echo ($i == $data['currentPage']) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Cancel Donation Confirmation Modal -->
<div id="cancelDonationModal" class="dn-modal">
    <div class="dn-modal__content">
        <div class="dn-modal__header">
            <h2>Cancel Donation</h2>
            <button class="dn-modal__close" onclick="closeModal()">&times;</button>
        </div>
        <div class="dn-modal__body">
            <p>Are you sure you want to cancel this donation?</p>
            <p class="dn-modal__warning"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>
            
            <form id="cancelDonationForm" action="<?php echo URLROOT; ?>/donors/cancelDonation" method="POST">
                <input type="hidden" id="donationId" name="donation_id" value="">
                <input type="hidden" name="redirect_page" value="myDonations">
                
                <div class="dn-modal__reason">
                    <label for="cancel_reason">Reason for cancellation:</label>
                    <select id="cancel_reason" name="cancel_reason" class="form-control" required>
                        <option value="">Select a reason</option>
                        <option value="Changed mind">I changed my mind</option>
                        <option value="Can't attend drop-off">I can't attend the scheduled drop-off</option>
                        <option value="Found another cause">I found another cause to support</option>
                        <option value="Other">Other reason</option>
                    </select>
                </div>
                
                <div id="otherReasonContainer" class="dn-modal__other-reason" style="display: none;">
                    <label for="other_reason">Please specify:</label>
                    <textarea id="other_reason" name="other_reason" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="dn-modal__actions">
                    <button type="button" class="dn-modal__btn-secondary" onclick="closeModal()">
                        No, Keep Donation
                    </button>
                    <button type="submit" class="dn-modal__btn-danger">
                        Yes, Cancel Donation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Filtering and sorting functionality
    function filterDonations() {
        const type = document.getElementById('donation-type').value;
        const status = document.getElementById('donation-status').value;
        const donations = document.querySelectorAll('.dn-donations__card');
        
        donations.forEach(donation => {
            const donationType = donation.dataset.type;
            const donationStatus = donation.dataset.status;
            
            const typeMatch = type === 'all' || donationType === type;
            const statusMatch = status === 'all' || donationStatus === status;
            
            if (typeMatch && statusMatch) {
                donation.style.display = 'block';
            } else {
                donation.style.display = 'none';
            }
        });
    }
    
    function sortDonations() {
        const sortOption = document.getElementById('donation-date').value;
        const donationsContainer = document.getElementById('donations-container');
        const donations = Array.from(document.querySelectorAll('.dn-donations__card'));
        
        donations.sort((a, b) => {
            const dateA = parseInt(a.dataset.date);
            const dateB = parseInt(b.dataset.date);
            
            if (sortOption === 'newest') {
                return dateB - dateA;
            } else {
                return dateA - dateB;
            }
        });
        
        // Clear container and append sorted donations
        donationsContainer.innerHTML = '';
        donations.forEach(donation => {
            donationsContainer.appendChild(donation);
        });
    }
    
    // Modal functionality
    function cancelDonation(donationId) {
        document.getElementById('donationId').value = donationId;
        document.getElementById('cancelDonationModal').style.display = 'block';
    }
    
    function closeModal() {
        document.getElementById('cancelDonationModal').style.display = 'none';
        document.getElementById('cancel_reason').value = '';
        document.getElementById('other_reason').value = '';
        document.getElementById('otherReasonContainer').style.display = 'none';
    }
    
    // Show/hide "other reason" field based on selection
    document.getElementById('cancel_reason').addEventListener('change', function() {
        if (this.value === 'Other') {
            document.getElementById('otherReasonContainer').style.display = 'block';
        } else {
            document.getElementById('otherReasonContainer').style.display = 'none';
        }
    });
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('cancelDonationModal');
        if (event.target === modal) {
            closeModal();
        }
    };
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>