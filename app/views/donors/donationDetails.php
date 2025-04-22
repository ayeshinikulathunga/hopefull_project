<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<section class="dn-donation-details">
    <div class="container">
        <!-- Breadcrumb Navigation -->
        <div class="dn-donation-breadcrumb">
            <a href="<?php echo URLROOT; ?>">Home</a> &gt;
            <a href="<?php echo URLROOT; ?>/donors/dashboard">Dashboard</a> &gt;
            <a href="<?php echo URLROOT; ?>/donors/donations">My Donations</a> &gt;
            <span>Donation Details</span>
        </div>
        
        <div class="dn-donation-details__header">
            <h1>Donation Details</h1>
            <a href="<?php echo URLROOT; ?>/donors/donations" class="dn-donation-details__back-btn">
                <i class="fas fa-arrow-left"></i> Back to Donations
            </a>
        </div>
        
        <?php if(isset($data['donation']) && $data['donation']): ?>
            <div class="dn-donation-details__order-info">
                <div class="dn-donation-details__info-header">
                    <div>
                        <h2>Donation #<?php echo $data['donation']->DonationID; ?></h2>
                        <p class="dn-donation-details__date">Made on <?php echo date('F j, Y, g:i a', strtotime($data['donation']->DonationDate)); ?></p>
                    </div>
                    <div class="dn-donation-details__status-type">
                        <span class="dn-donation-details__type dn-donation-details__type-<?php echo strtolower($data['donation']->DonationType); ?>">
                            <?php echo $data['donation']->DonationType; ?>
                        </span>
                        <span class="dn-donation-details__status dn-donation-details__status-<?php echo strtolower($data['donation']->Status); ?>">
                            <?php echo $data['donation']->Status; ?>
                        </span>
                    </div>
                </div>
                
                <!-- Donation Summary Card -->
                <div class="dn-donation-details__summary-card">
                    <div class="dn-donation-details__summary-header">
                        <h3>Donation Summary</h3>
                    </div>
                    
                    <div class="dn-donation-details__summary-content">
                        <!-- Request Information -->
                        <div class="dn-donation-details__request-info">
                            <h4>Request Information</h4>
                            <div class="dn-donation-details__request-card">
                                <?php
                                // Try common image extensions
                                $extensions = ['jpg', 'jpeg', 'png', 'gif'];
                                $imageFound = false;
                                $imagePath = URLROOT . '/img/placeholder.jpg'; // Default to placeholder
                                
                                if(isset($data['request'])) {
                                    foreach($extensions as $ext) {
                                        $testPath = APPROOT . '/../public/uploads/requests/' . $data['request']->RequestID . '.' . $ext;
                                        if(file_exists($testPath)) {
                                            $imagePath = URLROOT . '/uploads/requests/' . $data['request']->RequestID . '.' . $ext;
                                            $imageFound = true;
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <div class="dn-donation-details__request-image">
                                    <img src="<?php echo $imagePath; ?>" alt="<?php echo isset($data['request']) ? $data['request']->Title : 'Request Image'; ?>">
                                </div>
                                <div class="dn-donation-details__request-content">
                                    <h5><?php echo isset($data['request']) ? $data['request']->Title : 'Request Title'; ?></h5>
                                    <span class="dn-donation-details__request-category">
                                        <?php echo isset($data['request']) ? $data['request']->Category : 'Category'; ?>
                                    </span>
                                    <p class="dn-donation-details__request-recipient">
                                        Requested by: <?php echo isset($data['request']) && isset($data['request']->RecipientName) ? $data['request']->RecipientName : 'Recipient'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Donation Details -->
                        <div class="dn-donation-details__donation-info">
                            <h4>Donation Details</h4>
                            <table class="dn-donation-details__info-table">
                                <tr>
                                    <th>Donation ID:</th>
                                    <td><?php echo $data['donation']->DonationID; ?></td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td><?php echo date('F j, Y', strtotime($data['donation']->DonationDate)); ?></td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td><?php echo $data['donation']->DonationType; ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td><span class="dn-donation-details__status-text dn-donation-details__status-text-<?php echo strtolower($data['donation']->Status); ?>"><?php echo $data['donation']->Status; ?></span></td>
                                </tr>
                                <tr>
                                    <th>Anonymous:</th>
                                    <td><?php echo ($data['donation']->IsAnonymous) ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <?php if($data['donation']->DonationType == 'Monetary'): ?>
                                    <tr>
                                        <th>Amount:</th>
                                        <td class="dn-donation-details__amount">Rs. <?php echo number_format($data['donation']->Amount, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method:</th>
                                        <td><?php echo isset($data['donation']->PaymentMethod) ? $data['donation']->PaymentMethod : 'Credit/Debit Card'; ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <th>Item:</th>
                                        <td><?php echo isset($data['itemDetails']) ? $data['itemDetails']->ItemName : 'Items'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Quantity:</th>
                                        <td><?php echo $data['donation']->QuantityDonated; ?></td>
                                    </tr>
                                    <?php if(isset($data['scheduleDetails'])): ?>
                                        <tr>
                                            <th>Drop-off Date:</th>
                                            <td><?php echo date('F j, Y', strtotime($data['scheduleDetails']->DropOffDate)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Drop-off Time:</th>
                                            <td><?php echo date('g:i A', strtotime($data['scheduleDetails']->DropOffTime)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Drop-off Location:</th>
                                            <td><?php echo isset($data['itemDetails']) ? $data['itemDetails']->DropOffLocation : 'N/A'; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                    
                    <?php if(!empty($data['donation']->Notes)): ?>
                        <div class="dn-donation-details__notes">
                            <h4>Additional Notes</h4>
                            <p><?php echo $data['donation']->Notes; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Progress Section -->
                <?php if(isset($data['request'])): ?>
                    <div class="dn-donation-details__progress-section">
                        <h3>Request Progress</h3>
                        <?php if($data['request']->RequestType == 'Monetary'): ?>
                            <div class="dn-donation-details__progress">
                                <?php 
                                    $percentage = 0;
                                    if(isset($data['request']->CurrentAmount) && isset($data['request']->TargetAmount) && $data['request']->TargetAmount > 0) {
                                        $percentage = ($data['request']->CurrentAmount / $data['request']->TargetAmount) * 100;
                                    }
                                ?>
                                <div class="dn-donation-details__progress-bar">
                                    <div class="dn-donation-details__progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <div class="dn-donation-details__progress-stats">
                                    <span>Rs. <?php echo number_format($data['request']->CurrentAmount ?? 0); ?> raised</span>
                                    <span>of Rs. <?php echo number_format($data['request']->TargetAmount ?? 0); ?> goal</span>
                                    <span><?php echo round($percentage); ?>%</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="dn-donation-details__progress">
                                <?php 
                                    $percentage = 0;
                                    if(isset($data['request']->QuantityReceived) && isset($data['request']->QuantityNeeded) && $data['request']->QuantityNeeded > 0) {
                                        $percentage = ($data['request']->QuantityReceived / $data['request']->QuantityNeeded) * 100;
                                    }
                                ?>
                                <div class="dn-donation-details__progress-bar">
                                    <div class="dn-donation-details__progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <div class="dn-donation-details__progress-stats">
                                    <span><?php echo ($data['request']->QuantityReceived ?? 0); ?> received</span>
                                    <span>of <?php echo ($data['request']->QuantityNeeded ?? 0); ?> goal</span>
                                    <span><?php echo round($percentage); ?>%</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="dn-donation-details__actions">
                    <?php if($data['donation']->Status == 'Pending' && $data['donation']->DonationType == 'NonMonetary'): ?>
                        <button class="dn-donation-details__cancel-btn" onclick="cancelDonation('<?php echo $data['donation']->DonationID; ?>')">
                            <i class="fas fa-times-circle"></i> Cancel Donation
                        </button>
                    <?php endif; ?>
                    
                    <button class="dn-donation-details__print-btn" id="printReceiptBtn">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    
                    <a href="<?php echo URLROOT; ?>/donors/donations" class="dn-donation-details__back-to-donations">
                        <i class="fas fa-list"></i> Back to Donations
                    </a>
                </div>
            </div>
            
            <!-- Printable Receipt Section (hidden on screen, visible when printing) -->
            <div class="dn-receipt print-only">
                <div class="dn-receipt__header">
                    <div class="dn-receipt__logo">
                        <!-- Replace with your actual logo path -->
                        <img src="<?php echo URLROOT; ?>/images/logo.png" alt="Logo">
                    </div>
                    <div class="dn-receipt__title">
                        <h1>Donation Receipt</h1>
                        <p>Donation #<?php echo $data['donation']->DonationID; ?></p>
                    </div>
                </div>
                
                <div class="dn-receipt__info">
                    <div class="dn-receipt__donation-details">
                        <h2>Donation Information</h2>
                        <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($data['donation']->DonationDate)); ?></p>
                        <p><strong>Type:</strong> <?php echo $data['donation']->DonationType; ?></p>
                        <p><strong>Status:</strong> <?php echo $data['donation']->Status; ?></p>
                        <?php if($data['donation']->DonationType == 'Monetary'): ?>
                            <p><strong>Amount:</strong> Rs. <?php echo number_format($data['donation']->Amount, 2); ?></p>
                            <p><strong>Payment Method:</strong> <?php echo isset($data['donation']->PaymentMethod) ? $data['donation']->PaymentMethod : 'Credit/Debit Card'; ?></p>
                        <?php else: ?>
                            <p><strong>Item:</strong> <?php echo isset($data['itemDetails']) ? $data['itemDetails']->ItemName : 'Items'; ?></p>
                            <p><strong>Quantity:</strong> <?php echo $data['donation']->QuantityDonated; ?></p>
                            <?php if(isset($data['scheduleDetails'])): ?>
                                <p><strong>Drop-off Details:</strong> <?php echo date('F j, Y', strtotime($data['scheduleDetails']->DropOffDate)); ?> at <?php echo date('g:i A', strtotime($data['scheduleDetails']->DropOffTime)); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="dn-receipt__request">
                        <h2>Request Information</h2>
                        <p><strong>Title:</strong> <?php echo isset($data['request']) ? $data['request']->Title : 'Request Title'; ?></p>
                        <p><strong>Category:</strong> <?php echo isset($data['request']) ? $data['request']->Category : 'Category'; ?></p>
                        <p><strong>Recipient:</strong> <?php echo isset($data['request']) && isset($data['request']->RecipientName) ? $data['request']->RecipientName : 'Recipient'; ?></p>
                    </div>
                </div>
                
                <?php if($data['donation']->DonationType == 'Monetary'): ?>
                <div class="dn-receipt__amount">
                    <h2>Donation Amount</h2>
                    <table class="dn-receipt__table">
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                        <tr>
                            <td>Donation to: <?php echo isset($data['request']) ? $data['request']->Title : 'Request Title'; ?></td>
                            <td>Rs. <?php echo number_format($data['donation']->Amount, 2); ?></td>
                        </tr>
                        <tr class="dn-receipt__total">
                            <td>Total Donation:</td>
                            <td>Rs. <?php echo number_format($data['donation']->Amount, 2); ?></td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>
                
                <div class="dn-receipt__footer">
                    <p>Thank you for your generosity!</p>
                    <p>Your donation is making a meaningful difference in someone's life.</p>
                    <p>© <?php echo date('Y'); ?> Hopefull Foundation</p>
                </div>
            </div>
            
        <?php else: ?>
            <div class="dn-donation-details__not-found">
                <i class="fas fa-exclamation-circle"></i>
                <h2>Donation Not Found</h2>
                <p>Sorry, we couldn't find the donation details you're looking for.</p>
                <a href="<?php echo URLROOT; ?>/donors/donations" class="dn-donation-details__btn">
                    Go Back to Donations
                </a>
            </div>
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

<script>
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
    
    // Print receipt functionality
    const printReceiptBtn = document.getElementById('printReceiptBtn');
    if (printReceiptBtn) {
        printReceiptBtn.addEventListener('click', function() {
            // Small delay to ensure all styles are applied
            setTimeout(function() {
                window.print();
            }, 100);
        });
    }
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>