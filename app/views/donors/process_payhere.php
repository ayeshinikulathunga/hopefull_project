<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<div class="container mt-5" style="margin-top: 80px !important;" >
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Processing Donation Payment</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <p>Please wait while we redirect you to PayHere to complete your donation...</p>
                        <div class="spinner-border text-primary mt-3" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    
                    <!-- PayHere Form (Hidden) -->
                    <form id="payhere-payment-form" method="post" action="<?php echo $data['sandbox'] ? 'https://sandbox.payhere.lk/pay/checkout' : 'https://www.payhere.lk/pay/checkout'; ?>">
                        <!-- Required PayHere Parameters -->
                        <input type="hidden" name="merchant_id" value="<?php echo $data['merchant_id']; ?>">
                        <input type="hidden" name="return_url" value="<?php echo $data['return_url'] . '/' . $data['donation']->DonationID; ?>">
                        <input type="hidden" name="cancel_url" value="<?php echo $data['cancel_url'] . '/' . $data['donation']->DonationID; ?>">
                        <input type="hidden" name="notify_url" value="<?php echo $data['notify_url']; ?>">
                        
                        <!-- Order Details -->
                        <input type="hidden" name="order_id" value="<?php echo $data['donation']->DonationID; ?>">
                        <input type="hidden" name="items" value="Donation: <?php echo $data['request']->Title; ?>">
                        <input type="hidden" name="currency" value="LKR">
                        <input type="hidden" name="amount" value="<?php echo $data['donation']->Amount; ?>">
                        
                        <!-- Customer Details -->
                        <input type="hidden" name="first_name" value="<?php echo $data['user']->FirstName ?? 'Anonymous'; ?>">
                        <input type="hidden" name="last_name" value="<?php echo $data['user']->LastName ?? 'Donor'; ?>">
                        <input type="hidden" name="email" value="<?php echo $data['user']->Email; ?>">
                        <input type="hidden" name="phone" value="<?php echo $data['user']->Phone ?? ''; ?>">
                        <input type="hidden" name="address" value="<?php echo $data['user']->Address ?? 'N/A'; ?>">
                        <input type="hidden" name="city" value="<?php echo 'N/A'; ?>">
                        <input type="hidden" name="country" value="Sri Lanka">
                        
                        <!-- Custom Parameters -->
                        <input type="hidden" name="custom_1" value="<?php echo $data['donation']->DonationID; ?>">
                        <input type="hidden" name="custom_2" value="<?php echo $data['request']->RequestID; ?>">
                    </form>
                </div>
            </div>
            
            <!-- Back button -->
            <div class="text-center mt-4">
                <a href="<?php echo URLROOT; ?>/donations/donationDetails/<?php echo $data['donation']->DonationID; ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel and Go Back
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-submit the form after a short delay
    document.addEventListener('DOMContentLoaded', function() {
        // Store the donation ID in session storage
        <?php $_SESSION['payhere_donation_id'] = $data['donation']->DonationID; ?>
        
        // Submit the form after 2 seconds
        setTimeout(function() {
            document.getElementById('payhere-payment-form').submit();
        }, 2000);
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>