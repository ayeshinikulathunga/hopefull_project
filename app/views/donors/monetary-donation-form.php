<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<div class="donation-checkout">
    <div class="container">
        <!-- Breadcrumb Navigation -->
        <div class="donation-breadcrumb">
            <a href="<?php echo URLROOT; ?>">Home</a> &gt;
            <a href="<?php echo URLROOT; ?>/donors/allRequests">All Requests</a> &gt;
            <a href="<?php echo URLROOT; ?>/donors/details/<?php echo $data['request']->RequestID; ?>"><?php echo $data['request']->Title; ?></a> &gt;
            <span>Donate</span>
        </div>

        <h1 class="donation-checkout__title">Make a Donation</h1>

        <div class="donation-checkout__container">
            <!-- Left Column - Checkout Form -->
            <div class="donation-checkout__form">
                <form id="donationForm" action="<?php echo URLROOT; ?>/donations/donate/<?php echo $data['request']->RequestID; ?>" method="POST">
                    <input type="hidden" name="request_id" value="<?php echo $data['request']->RequestID; ?>">
                    <input type="hidden" name="donation_type" value="monetary">

                    <!-- Donation Amount Section -->
                    <div class="donation-checkout__section">
                        <h2>Donation Amount</h2>
                        
                        <div class="donation-presets">
                            <button type="button" class="donation-preset-btn" data-amount="1000">Rs. 1,000</button>
                            <button type="button" class="donation-preset-btn" data-amount="2500">Rs. 2,500</button>
                            <button type="button" class="donation-preset-btn" data-amount="5000">Rs. 5,000</button>
                            <button type="button" class="donation-preset-btn" data-amount="10000">Rs. 10,000</button>
                        </div>
                        
                        <div class="form-group">
                            <label for="amount">Enter Amount (LKR)</label>
                            <div class="donation-amount-wrapper">
                                <span class="donation-currency">Rs.</span>
                                <input type="number" id="amount" name="amount" class="form-control donation-amount-input" min="100" required value="<?php echo $data['amount'] ?? ''; ?>">
                            </div>
                            <?php if(isset($data['amount_err'])): ?>
                                <span class="form-error"><?php echo $data['amount_err']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Payment Method Section -->
                    <div class="donation-checkout__section">
                        <h2>Payment Method</h2>
                        
                        <div class="donation-checkout__payment-options">
                        <div class="donation-checkout__payment-option">
                            <input type="radio" id="payment_payhere" name="payment_method" value="payhere" checked>
                            <label for="payment_payhere">
                                <i class="fas fa-credit-card"></i>
                                Pay Online (PayHere)
                            </label>
                        </div>
                            
                            <div class="donation-checkout__payment-option">
                                <input type="radio" id="payment_bank" name="payment_method" value="bank_transfer">
                                <label for="payment_bank">
                                    <i class="fas fa-university"></i>
                                    Bank Transfer
                                </label>
                            </div>
                            
                            <div class="donation-checkout__payment-option">
                                <input type="radio" id="payment_mobile" name="payment_method" value="mobile_payment">
                                <label for="payment_mobile">
                                    <i class="fas fa-mobile-alt"></i>
                                    Mobile Payment
                                </label>
                            </div>
                        </div>
                        
                        <!-- Credit Card Form (displayed by default) -->
                        <!--<div id="creditCardForm" class="credit-card-form">
                            <div class="form-group">
                                <label for="card_name">Cardholder Name</label>
                                <input type="text" id="card_name" name="card_name" class="form-control" placeholder="Name on card">
                            </div>
                            
                            <div class="form-group">
                                <label for="card_number">Card Number</label>
                                <input type="text" id="card_number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456">
                            </div>
                            
                            <div class="card-row">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date</label>
                                    <input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="MM/YY">
                                </div>
                                
                                <div class="form-group card-cvv">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123">
                                </div>
                            </div>
                        </div>-->
                        
                        <!-- Bank Transfer Info (hidden by default) -->
                        <div id="bankTransferInfo" class="payment-info" style="display: none;">
                            <div class="dropoff-details">
                                <p>Please transfer the donation amount to the following bank account:</p>
                                <p><strong>Bank:</strong> People's Bank</p>
                                <p><strong>Account Name:</strong> Hopefull Donation Foundation</p>
                                <p><strong>Account Number:</strong> 1234-5678-9012-3456</p>
                                <p><strong>Branch:</strong> Main Branch, Colombo</p>
                                <p><strong>Reference:</strong> <?php echo $data['request']->RequestID; ?></p>
                                <p class="mt-3">Please upload your payment receipt after completing the transaction.</p>
                                <div class="form-group mt-3">
                                    <label for="payment_receipt">Upload Payment Receipt</label>
                                    <input type="file" id="payment_receipt" name="payment_receipt" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile Payment Info (hidden by default) -->
                        <div id="mobilePaymentInfo" class="payment-info" style="display: none;">
                            <div class="dropoff-details">
                                <p>Please send the donation amount to the following mobile number:</p>
                                <p><strong>Mobile Number:</strong> 077-123-4567</p>
                                <p><strong>Provider:</strong> Dialog, Mobitel, or Hutch</p>
                                <p><strong>Reference:</strong> <?php echo $data['request']->RequestID; ?></p>
                                <p class="mt-3">Please upload your payment confirmation after completing the transaction.</p>
                                <div class="form-group mt-3">
                                    <label for="mobile_receipt">Upload Payment Confirmation</label>
                                    <input type="file" id="mobile_receipt" name="mobile_receipt" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Donor Information Section -->
                    <div class="donation-checkout__section">
                        <h2>Donor Information</h2>
                        
                        <div class="anonymous-option">
                            <input type="checkbox" id="anonymous" name="anonymous" value="1">
                            <label for="anonymous">Make this donation anonymous</label>
                        </div>
                        
                        <div class="donor-info">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo $_SESSION['user_name'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control" value="<?php echo $_SESSION['user_email'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes Section -->
                    <div class="donation-checkout__section notes-section">
                        <h2>Additional Notes</h2>
                        
                        <div class="form-group">
                            <label for="notes">Add a message (optional)</label>
                            <textarea id="notes" name="notes" class="form-control" placeholder="Add a personal message or any specific instructions"><?php echo $data['notes'] ?? ''; ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Terms Agreement -->
                    <div class="donation-checkout__terms-agreement">
                        <input type="checkbox" id="terms_agreement" name="terms_agreement" required>
                        <label for="terms_agreement">
                            I agree to the <a href="#" class="donation-checkout__terms-link">Terms and Conditions</a> and <a href="#" class="donation-checkout__terms-link">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="donation-checkout__actions">
                        <a href="<?php echo URLROOT; ?>/donors/details/<?php echo $data['request']->RequestID; ?>" class="donation-checkout__back">
                            <i class="fas fa-arrow-left"></i> Back to Details
                        </a>
                        
                        <button type="submit" class="donation-checkout__submit">
                            Complete Donation <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Right Column - Donation Summary -->
            <div class="donation-checkout__summary">
                <h2>Donation Summary</h2>
                
                <!-- Request Details -->
                <div class="donation-request-details">
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
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $data['request']->Title; ?>" class="donation-request-image">
                    
                    <div class="donation-request-title"><?php echo $data['request']->Title; ?></div>
                    <span class="donation-request-category"><?php echo $data['request']->Category; ?></span>
                    <div class="donation-request-recipient">
                        Requested by: <?php echo isset($data['request']->RecipientName) ? $data['request']->RecipientName : 'Anonymous'; ?>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="donation-checkout__progress">
                        <?php 
                            $percentage = 0;
                            if(isset($data['request']->CurrentAmount) && isset($data['request']->TargetAmount) && $data['request']->TargetAmount > 0) {
                                $percentage = ($data['request']->CurrentAmount / $data['request']->TargetAmount) * 100;
                            }
                        ?>
                        <div class="donation-checkout__progress-bar">
                            <div class="donation-checkout__progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                        <div class="donation-checkout__progress-stats">
                            <span>Rs. <?php echo number_format($data['request']->CurrentAmount ?? 0); ?> raised</span>
                            <span><?php echo round($percentage); ?>% of goal</span>
                        </div>
                    </div>
                </div>
                
                <!-- Donation Summary (will update via JavaScript) -->
                <div class="donation-checkout__summary-item">
                    <span>Amount</span>
                    <span id="summaryAmount">Rs. 0</span>
                </div>
                
                <div class="donation-checkout__summary-item">
                    <span>Payment Method</span>
                    <span id="summaryPaymentMethod">Credit/Debit Card</span>
                </div>
                
                <div class="donation-checkout__summary-total">
                    <span>Total</span>
                    <span id="summaryTotal">Rs. 0</span>
                </div>
                
                <!-- Info Notes -->
                <div class="donation-checkout__notes">
                    <p><i class="fas fa-info-circle"></i> All donations are secure and encrypted.</p>
                    <p><i class="fas fa-exclamation-circle"></i> You will receive a confirmation email with your donation details.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for dynamic behavior -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Amount input and preset buttons
        const amountInput = document.getElementById('amount');
        const presetButtons = document.querySelectorAll('.donation-preset-btn');
        const summaryAmount = document.getElementById('summaryAmount');
        const summaryTotal = document.getElementById('summaryTotal');
        
        // Update summary when amount changes
        amountInput.addEventListener('input', updateSummary);
        
        // Preset button functionality
        presetButtons.forEach(button => {
            button.addEventListener('click', function() {
                const amount = this.dataset.amount;
                amountInput.value = amount;
                updateSummary();
                
                // Highlight selected preset
                presetButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Payment method switching
        const paymentPayhereRadio = document.getElementById('payment_payhere');
        const paymentBankRadio = document.getElementById('payment_bank');
        const paymentMobileRadio = document.getElementById('payment_mobile');
        //const creditCardForm = document.getElementById('creditCardForm');
        const bankTransferInfo = document.getElementById('bankTransferInfo');
        const mobilePaymentInfo = document.getElementById('mobilePaymentInfo');
        const summaryPaymentMethod = document.getElementById('summaryPaymentMethod');
        
        paymentCardRadio.addEventListener('change', function() {
            if(this.checked) {
                creditCardForm.style.display = 'block';
                bankTransferInfo.style.display = 'none';
                mobilePaymentInfo.style.display = 'none';
                summaryPaymentMethod.textContent = 'Credit/Debit Card';
            }
        });

        paymentPayhereRadio.addEventListener('change', function() {
        if(this.checked) {
            bankTransferInfo.style.display = 'none';
            summaryPaymentMethod.textContent = 'Pay Online (PayHere)';
        }
    });
    
        
        paymentBankRadio.addEventListener('change', function() {
            if(this.checked) {
                creditCardForm.style.display = 'none';
                bankTransferInfo.style.display = 'block';
                mobilePaymentInfo.style.display = 'none';
                summaryPaymentMethod.textContent = 'Bank Transfer';
            }
        });
        
        paymentMobileRadio.addEventListener('change', function() {
            if(this.checked) {
                creditCardForm.style.display = 'none';
                bankTransferInfo.style.display = 'none';
                mobilePaymentInfo.style.display = 'block';
                summaryPaymentMethod.textContent = 'Mobile Payment';
            }
        });
        
        // Anonymous donation toggle
        const anonymousCheckbox = document.getElementById('anonymous');
        const donorInfoFields = document.querySelectorAll('.donor-info input');
        
        anonymousCheckbox.addEventListener('change', function() {
            const isDisabled = this.checked;
            
            donorInfoFields.forEach(field => {
                if (isDisabled) {
                    field.setAttribute('disabled', 'disabled');
                } else {
                    field.removeAttribute('disabled');
                }
            });
        });
        
        // Function to update summary
        function updateSummary() {
            const amount = amountInput.value || 0;
            const formattedAmount = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'LKR',
                minimumFractionDigits: 2,
                currencyDisplay: 'narrowSymbol'
            }).format(amount).replace('LKR', 'Rs.');
            
            summaryAmount.textContent = formattedAmount;
            summaryTotal.textContent = formattedAmount;
        }
        
        // Initialize summary with default amount
        updateSummary();
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>