<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<section class="dn-pending-donations">
    <div class="container">
      
        
        <?php flash('donation_message'); ?>
        
        <?php if(empty($data['pendingDonations'])): ?>
            <div class="dn-pending-donations__empty">
                <i class="fas fa-calendar-check fa-3x"></i>
                <h2>No pending donations</h2>
                <p>You don't have any pending non-monetary donations scheduled at the moment.</p>
                <a href="<?php echo URLROOT; ?>/donors/allRequests" class="dn-pending-donations__donate-btn">Browse Donation Requests</a>
            </div>
        <?php else: ?>
            <div class="dn-pending-donations__header">
                <p class="dn-pending-donations__subtitle">These are your scheduled non-monetary donations that need to be dropped off. Be sure to check the dates and location details.</p>
                
                <div class="dn-pending-donations__filter">
                    <div class="dn-pending-donations__filter-group">
                        <label for="dropoff-date">Sort by:</label>
                        <select id="dropoff-date" onchange="sortPendingDonations()">
                            <option value="soonest">Soonest Drop-off First</option>
                            <option value="latest">Latest Drop-off First</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="dn-pending-donations__list" id="pending-donations-container">
                <?php foreach($data['pendingDonations'] as $donation): ?>
                    <?php 
                        // Calculate days remaining until drop-off
                        $today = new DateTime();
                        $dropOffDate = new DateTime($donation->DropOffDate);
                        $daysRemaining = $today->diff($dropOffDate)->days;
                        $isPastDate = $today > $dropOffDate;
                        
                        // Determine urgency level for styling
                        $urgencyClass = '';
                        $urgencyLabel = '';
                        
                        if ($isPastDate) {
                            $urgencyClass = 'overdue';
                            $urgencyLabel = 'Overdue';
                        } elseif ($daysRemaining <= 1) {
                            $urgencyClass = 'urgent';
                            $urgencyLabel = 'Drop-off Today!';
                        } elseif ($daysRemaining <= 3) {
                            $urgencyClass = 'soon';
                            $urgencyLabel = 'Drop-off Soon';
                        }
                    ?>
                    <div class="dn-pending-donations__card <?php echo $urgencyClass ? 'dn-pending-donations__card--' . $urgencyClass : ''; ?>" data-date="<?php echo strtotime($donation->DropOffDate); ?>">
                        <?php if($urgencyLabel): ?>
                            <div class="dn-pending-donations__urgency-badge dn-pending-donations__urgency-badge--<?php echo $urgencyClass; ?>">
                                <i class="fas <?php echo $isPastDate ? 'fa-exclamation-triangle' : 'fa-clock'; ?>"></i> <?php echo $urgencyLabel; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="dn-pending-donations__header">
                            <div class="dn-pending-donations__id">
                                <h3>Donation #<?php echo $donation->DonationID; ?></h3>
                                <span class="dn-pending-donations__date"><?php echo date('F j, Y', strtotime($donation->DonationDate)); ?></span>
                            </div>
                        </div>
                        
                        <div class="dn-pending-donations__request">
                            <span class="dn-pending-donations__request-category"><?php echo $donation->Category; ?></span>
                            <h4 class="dn-pending-donations__request-title"><?php echo $donation->Title; ?></h4>
                        </div>
                        
                        <div class="dn-pending-donations__details">
                            <div class="dn-pending-donations__item">
                                <span class="dn-pending-donations__item-label">Item:</span>
                                <span class="dn-pending-donations__item-name"><?php echo isset($donation->ItemName) ? $donation->ItemName : 'Items'; ?></span>
                                <span class="dn-pending-donations__item-quantity">(Qty: <?php echo $donation->QuantityDonated; ?>)</span>
                            </div>
                            
                            <div class="dn-pending-donations__schedule">
                                <div class="dn-pending-donations__schedule-date">
                                    <i class="fas fa-calendar-day"></i>
                                    <?php echo date('l, F j, Y', strtotime($donation->DropOffDate)); ?>
                                </div>
                                <div class="dn-pending-donations__schedule-time">
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('g:i A', strtotime($donation->DropOffTime)); ?>
                                </div>
                                <div class="dn-pending-donations__countdown">
                                    <?php if($isPastDate): ?>
                                        <span class="dn-pending-donations__overdue">
                                            <i class="fas fa-exclamation-circle"></i> 
                                            <?php echo abs($daysRemaining); ?> day<?php echo abs($daysRemaining) != 1 ? 's' : ''; ?> overdue
                                        </span>
                                    <?php else: ?>
                                        <i class="fas fa-hourglass-half"></i> 
                                        <?php echo $daysRemaining; ?> day<?php echo $daysRemaining != 1 ? 's' : ''; ?> remaining
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="dn-pending-donations__location">
                                <h5><i class="fas fa-map-marker-alt"></i> Drop-off Location</h5>
                                <p><?php echo $donation->DropOffLocation; ?></p>
                                <p><?php echo $donation->Province; ?></p>
                            </div>
                        </div>
                        
                        <div class="dn-pending-donations__actions">
                            <a href="<?php echo URLROOT; ?>/donations/donationDetails/<?php echo $donation->DonationID; ?>" class="dn-pending-donations__details-btn">
                                <i class="fas fa-file-alt"></i> View Details
                            </a>
                            
                            <?php if(!$isPastDate): ?>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($donation->DropOffLocation); ?>" target="_blank" class="dn-pending-donations__directions-btn">
                                    <i class="fas fa-directions"></i> Get Directions
                                </a>
                                
                                <button class="dn-pending-donations__calendar-btn" onclick="addToCalendar('<?php echo $donation->Title; ?>', '<?php echo $donation->DropOffLocation; ?>', '<?php echo $donation->DropOffDate; ?>', '<?php echo $donation->DropOffTime; ?>')">
                                    <i class="fas fa-calendar-plus"></i> Add to Calendar
                                </button>
                            <?php endif; ?>
                            
                            <button class="dn-pending-donations__cancel-btn" onclick="cancelDonation('<?php echo $donation->DonationID; ?>')">
                                <i class="fas fa-times-circle"></i> Cancel
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination (if needed) -->
            <?php if(isset($data['totalPages']) && $data['totalPages'] > 1): ?>
                <div class="dn-pending-donations__pagination">
                    <?php for($i = 1; $i <= $data['totalPages']; $i++): ?>
                        <a href="<?php echo URLROOT; ?>/donors/pendingDonations/<?php echo $i; ?>" class="dn-pending-donations__pagination-link <?php echo ($i == $data['currentPage']) ? 'active' : ''; ?>">
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
            
            <form id="cancelDonationForm" action="<?php echo URLROOT; ?>/donations/cancelDonation" method="POST">
                <input type="hidden" id="donationId" name="donation_id" value="">
                <input type="hidden" name="redirect_page" value="pendingDonations">
                
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

<!-- Google Calendar Integration -->
<script>
    function addToCalendar(title, location, date, time) {
        // Format the start and end times (assuming 1 hour duration)
        const startDate = new Date(date + 'T' + time);
        const endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // Add 1 hour
        
        const formattedStart = startDate.toISOString().replace(/-|:|\.\d+/g, '');
        const formattedEnd = endDate.toISOString().replace(/-|:|\.\d+/g, '');
        
        // Create Google Calendar URL
        const googleCalUrl = 'https://calendar.google.com/calendar/render?' +
            'action=TEMPLATE' +
            '&text=' + encodeURIComponent('Donation Drop-off: ' + title) +
            '&dates=' + formattedStart + '/' + formattedEnd +
            '&details=' + encodeURIComponent('Drop-off for your scheduled donation.') +
            '&location=' + encodeURIComponent(location);
        
        // Open in a new tab
        window.open(googleCalUrl, '_blank');
    }
    
    // Sorting functionality
    function sortPendingDonations() {
        const sortOption = document.getElementById('dropoff-date').value;
        const donationsContainer = document.getElementById('pending-donations-container');
        const donations = Array.from(document.querySelectorAll('.dn-pending-donations__card'));
        
        donations.sort((a, b) => {
            const dateA = parseInt(a.dataset.date);
            const dateB = parseInt(b.dataset.date);
            
            if (sortOption === 'soonest') {
                return dateA - dateB;
            } else {
                return dateB - dateA;
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

<!-- CSS for Pending Donations Page -->
<style>
    .dn-pending-donations__title {
        margin-bottom: 1.5rem;
        color: #333;
        font-size: 2rem;
    }
    
    .dn-pending-donations__subtitle {
        color: #666;
        margin-bottom: 1.5rem;
    }
    
    .dn-pending-donations__header {
        margin-top:40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 2rem;
    }
    
    .dn-pending-donations__filter {
        display: flex;
        gap: 1rem;
        margin: 1rem 0;
    }
    
    .dn-pending-donations__filter-group {
        display: flex;
        align-items: center;
    }
    
    .dn-pending-donations__filter-group label {
        margin-right: 0.5rem;
        font-weight: 500;
    }
    
    .dn-pending-donations__filter-group select {
        padding: 0.5rem;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    
    .dn-pending-donations__empty {
        margin-top: 100px;
        text-align: center;
        padding: 3rem 1rem;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .dn-pending-donations__empty i {
        color: #ccc;
        margin-bottom: 1rem;
    }
    
    .dn-pending-donations__empty h2 {
        color: #555;
        margin-bottom: 0.5rem;
    }
    
    .dn-pending-donations__empty p {
        color: #777;
        margin-bottom: 1.5rem;
    }
    
    .dn-pending-donations__donate-btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background-color: #4CAF50;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s;
    }
    
    .dn-pending-donations__donate-btn:hover {
        background-color: #3e8e41;
        text-decoration: none;
        color: white;
    }
    
    .dn-pending-donations__list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .dn-pending-donations__card {
        position: relative;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        background-color: white;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        border-top: 5px solid #4CAF50;
    }
    
    .dn-pending-donations__card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }
    
    /* Urgency styles */
    .dn-pending-donations__card--urgent {
        border-top-color: #ff4d4d;
    }
    
    .dn-pending-donations__card--soon {
        border-top-color: #ff9933;
    }
    
    .dn-pending-donations__card--overdue {
        border-top-color: #990000;
        background-color: #fff5f5;
    }
    
    .dn-pending-donations__urgency-badge {
        position: absolute;
        top: 0;
        right: 0;
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        font-weight: bold;
        color: white;
        border-bottom-left-radius: 8px;
    }
    
    .dn-pending-donations__urgency-badge--urgent {
        background-color: #ff4d4d;
    }
    
    .dn-pending-donations__urgency-badge--soon {
        background-color: #ff9933;
    }
    
    .dn-pending-donations__urgency-badge--overdue {
        background-color: #990000;
    }
    
    .dn-pending-donations__header {
        padding: 1.25rem 1.25rem 0.75rem;
        border-bottom: 1px solid #eee;
    }
    
    .dn-pending-donations__id h3 {
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
        color: #444;
    }
    
    .dn-pending-donations__date {
        font-size: 0.9rem;
        color: #888;
    }
    
    .dn-pending-donations__request {
        padding: 1rem 1.25rem;
    }
    
    .dn-pending-donations__request-category {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background-color: #e9f5f9;
        color: #3498db;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
    }
    
    .dn-pending-donations__request-title {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: #333;
    }
    
    .dn-pending-donations__details {
        padding: 1rem 1.25rem;
        background-color: #f9f9f9;
    }
    
    .dn-pending-donations__item {
        margin-bottom: 1rem;
    }
    
    .dn-pending-donations__item-label {
        font-weight: 500;
        color: #555;
    }
    
    .dn-pending-donations__item-name {
        font-weight: 600;
        color: #333;
    }
    
    .dn-pending-donations__item-quantity {
        color: #777;
        font-size: 0.9rem;
    }
    
    .dn-pending-donations__schedule {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
    }
    
    .dn-pending-donations__schedule i {
        width: 20px;
        color: #4CAF50;
    }
    
    .dn-pending-donations__schedule-date,
    .dn-pending-donations__schedule-time {
        font-weight: 500;
    }
    
    .dn-pending-donations__countdown {
        color: #4CAF50;
        font-weight: 600;
    }
    
    .dn-pending-donations__overdue {
        color: #990000;
        font-weight: 600;
    }
    
    .dn-pending-donations__location {
        margin-top: 1rem;
    }
    
    .dn-pending-donations__location h5 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        color: #444;
    }
    
    .dn-pending-donations__location p {
        color: #666;
        margin-bottom: 0.25rem;
    }
    
    .dn-pending-donations__actions {
        padding: 1rem 1.25rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        border-top: 1px solid #eee;
    }
    
    .dn-pending-donations__details-btn,
    .dn-pending-donations__directions-btn,
    .dn-pending-donations__calendar-btn,
    .dn-pending-donations__cancel-btn {
        padding: 0.5rem 0.75rem;
        border-radius: 5px;
        font-size: 0.9rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    
    .dn-pending-donations__details-btn {
        background-color: #f0f0f0;
        color: #444;
    }
    
    .dn-pending-donations__details-btn:hover {
        background-color: #e0e0e0;
        text-decoration: none;
        color: #444;
    }
    
    .dn-pending-donations__directions-btn {
        background-color: #3498db;
        color: white;
    }
    
    .dn-pending-donations__directions-btn:hover {
        background-color: #2980b9;
        text-decoration: none;
        color: white;
    }
    
    .dn-pending-donations__calendar-btn {
        background-color: #9b59b6;
        color: white;
    }
    
    .dn-pending-donations__calendar-btn:hover {
        background-color: #8e44ad;
        color: white;
    }
    
    .dn-pending-donations__cancel-btn {
        background-color: #e74c3c;
        color: white;
    }
    
    .dn-pending-donations__cancel-btn:hover {
        background-color: #c0392b;
        color: white;
    }
    
    /* Pagination Styles */
    .dn-pending-donations__pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin: 2rem 0;
    }
    
    .dn-pending-donations__pagination-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 5px;
        background-color: #f0f0f0;
        color: #444;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .dn-pending-donations__pagination-link:hover {
        background-color: #ddd;
        text-decoration: none;
        color: #333;
    }
    
    .dn-pending-donations__pagination-link.active {
        background-color: #4CAF50;
        color: white;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dn-pending-donations__list {
            grid-template-columns: 1fr;
        }
        
        .dn-pending-donations__header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .dn-pending-donations__filter {
            width: 100%;
            flex-direction: column;
        }
    }
</style>

<?php require APPROOT . '/views/includes/footer.php'; ?>