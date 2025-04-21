<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<div class="donation-confirmation">
    <div class="container">
        <div class="donation-confirmation__icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="donation-confirmation__title">Thank You for Your Donation!</h1>
        
        <div class="donation-confirmation__message">
            <?php if($data['donation']->DonationType == 'Monetary'): ?>
                Your monetary donation has been successfully processed. Your generosity will help make a real difference in someone's life. A confirmation has been sent to your email.
            <?php else: ?>
                Your non-monetary donation has been scheduled. Thank you for your generosity and support. A confirmation with drop-off details has been sent to your email.
            <?php endif; ?>
        </div>
        
        <div class="donation-confirmation__details">
            <div class="donation-confirmation__reference">
                Donation Reference: <?php echo $data['donation']->DonationID; ?>
            </div>
            
            <div class="donation-confirmation__info-grid">
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Donation Date</div>
                    <div class="donation-confirmation__info-value"><?php echo date('F j, Y', strtotime($data['donation']->DonationDate)); ?></div>
                </div>
                
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Donation Type</div>
                    <div class="donation-confirmation__info-value"><?php echo $data['donation']->DonationType; ?></div>
                </div>
                
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Request Title</div>
                    <div class="donation-confirmation__info-value"><?php echo $data['request']->Title; ?></div>
                </div>
                
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Recipient</div>
                    <div class="donation-confirmation__info-value"><?php echo $data['request']->RecipientName; ?></div>
                </div>
                
                <?php if($data['donation']->DonationType == 'Monetary'): ?>
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Amount</div>
                    <div class="donation-confirmation__info-value">Rs. <?php echo number_format($data['donation']->Amount, 2); ?></div>
                </div>
                
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Payment Method</div>
                    <div class="donation-confirmation__info-value"><?php echo $data['donation']->PaymentMethod ?? 'Credit/Debit Card'; ?></div>
                </div>
                <?php else: ?>
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Item</div>
                    <div class="donation-confirmation__info-value"><?php echo isset($data['itemDetails']) ? $data['itemDetails']->ItemName : 'Items'; ?></div>
                </div>
                
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Quantity</div>
                    <div class="donation-confirmation__info-value"><?php echo $data['donation']->QuantityDonated; ?></div>
                </div>
                
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Drop-off Date</div>
                    <div class="donation-confirmation__info-value"><?php echo isset($data['scheduleDetails']) ? date('F j, Y', strtotime($data['scheduleDetails']->DropOffDate)) : 'Not scheduled'; ?></div>
                </div>
                
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Drop-off Time</div>
                    <div class="donation-confirmation__info-value"><?php echo isset($data['scheduleDetails']) ? date('g:i A', strtotime($data['scheduleDetails']->DropOffTime)) : 'Not scheduled'; ?></div>
                </div>
                
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Drop-off Location</div>
                    <div class="donation-confirmation__info-value"><?php echo isset($data['itemDetails']) ? $data['itemDetails']->DropOffLocation : 'N/A'; ?></div>
                </div>
                <?php endif; ?>
                
                <div class="donation-confirmation__info-block">
                    <div class="donation-confirmation__info-label">Status</div>
                    <div class="donation-confirmation__info-value"><?php echo $data['donation']->Status; ?></div>
                </div>
            </div>
        </div>
        
        <div class="donation-confirmation__actions">
            <a href="<?php echo URLROOT; ?>/donors/donations" class="donation-confirmation__btn donation-confirmation__secondary-btn">
                <i class="fas fa-history"></i> View My Donations
            </a>
            
            <a href="<?php echo URLROOT; ?>/donors/allRequests" class="donation-confirmation__btn donation-confirmation__primary-btn">
                <i class="fas fa-heart"></i> Explore More Causes
            </a>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>