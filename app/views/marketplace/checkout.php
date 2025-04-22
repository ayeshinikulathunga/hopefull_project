<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>

<section class="mp-checkout">
    <div class="container">
        <h1 class="mp-checkout__title">Checkout</h1>
        
        <div class="mp-checkout__container">
            <div class="mp-checkout__form">
                <form action="<?php echo URLROOT; ?>/marketplace/checkout" method="POST" enctype="multipart/form-data">
                    <div class="mp-checkout__section">
                        <h2>Personal Information</h2>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="first_name">First Name<span class="required">*</span></label>
                                <input type="text" id="first_name" name="first_name" class="form-control <?php echo (!empty($data['first_name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['first_name'] ?? ''; ?>">
                                <span class="invalid-feedback"><?php echo $data['first_name_err'] ?? ''; ?></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="last_name">Last Name<span class="required">*</span></label>
                                <input type="text" id="last_name" name="last_name" class="form-control <?php echo (!empty($data['last_name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['last_name'] ?? ''; ?>">
                                <span class="invalid-feedback"><?php echo $data['last_name_err'] ?? ''; ?></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email<span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email'] ?? ''; ?>">
                            <span class="invalid-feedback"><?php echo $data['email_err'] ?? ''; ?></span>
                            <small>Order confirmation will be sent to this email address</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_phone">Contact Phone for Delivery<span class="required">*</span></label>
                            <input type="tel" id="contact_phone" name="contact_phone" class="form-control <?php echo (!empty($data['contact_phone_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['contact_phone']; ?>">
                            <span class="invalid-feedback"><?php echo $data['contact_phone_err']; ?></span>
                        </div>
                    </div>
                    
                    <div class="mp-checkout__section">
                        <h2>Shipping Address</h2>
                        <div class="form-group">
                            <label for="street_address">Street Address<span class="required">*</span></label>
                            <input type="text" id="street_address" name="street_address" class="form-control <?php echo (!empty($data['street_address_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['street_address'] ?? ''; ?>" placeholder="House number and street name">
                            <span class="invalid-feedback"><?php echo $data['street_address_err'] ?? ''; ?></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="apartment">Apartment/Suite/Unit (Optional)</label>
                            <input type="text" id="apartment" name="apartment" class="form-control" value="<?php echo $data['apartment'] ?? ''; ?>" placeholder="Apartment, suite, unit, etc.">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="city">City<span class="required">*</span></label>
                                <input type="text" id="city" name="city" class="form-control <?php echo (!empty($data['city_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['city'] ?? ''; ?>">
                                <span class="invalid-feedback"><?php echo $data['city_err'] ?? ''; ?></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="postal_code">Postal Code<span class="required">*</span></label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control <?php echo (!empty($data['postal_code_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['postal_code'] ?? ''; ?>">
                                <span class="invalid-feedback"><?php echo $data['postal_code_err'] ?? ''; ?></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="province">Province<span class="required">*</span></label>
                            <select id="province" name="province" class="form-control <?php echo (!empty($data['province_err'])) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Province</option>
                                <option value="Western" <?php echo (isset($data['province']) && $data['province'] == 'Western') ? 'selected' : ''; ?>>Western</option>
                                <option value="Central" <?php echo (isset($data['province']) && $data['province'] == 'Central') ? 'selected' : ''; ?>>Central</option>
                                <option value="Southern" <?php echo (isset($data['province']) && $data['province'] == 'Southern') ? 'selected' : ''; ?>>Southern</option>
                                <option value="Northern" <?php echo (isset($data['province']) && $data['province'] == 'Northern') ? 'selected' : ''; ?>>Northern</option>
                                <option value="Eastern" <?php echo (isset($data['province']) && $data['province'] == 'Eastern') ? 'selected' : ''; ?>>Eastern</option>
                                <option value="North-Western" <?php echo (isset($data['province']) && $data['province'] == 'North-Western') ? 'selected' : ''; ?>>North-Western</option>
                                <option value="North-Central" <?php echo (isset($data['province']) && $data['province'] == 'North-Central') ? 'selected' : ''; ?>>North-Central</option>
                                <option value="Uva" <?php echo (isset($data['province']) && $data['province'] == 'Uva') ? 'selected' : ''; ?>>Uva</option>
                                <option value="Sabaragamuwa" <?php echo (isset($data['province']) && $data['province'] == 'Sabaragamuwa') ? 'selected' : ''; ?>>Sabaragamuwa</option>
                            </select>
                            <span class="invalid-feedback"><?php echo $data['province_err'] ?? ''; ?></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="shipping_notes">Delivery Notes (Optional)</label>
                            <textarea id="shipping_notes" name="shipping_notes" rows="2" class="form-control"><?php echo $data['shipping_notes']; ?></textarea>
                            <small>Include any special instructions for the delivery person.</small>
                        </div>
                        
                        <!-- Hidden field to store the compiled shipping address -->
                        <input type="hidden" id="shipping_address" name="shipping_address" value="">
                    </div>
                    
                    <div class="mp-checkout__section">
                        <h2>Payment Method</h2>
                        <div class="form-group">
                            <div class="mp-checkout__payment-options <?php echo (!empty($data['payment_method_err'])) ? 'is-invalid' : ''; ?>">
                                <!-- PayHere Payment Option -->
                                <div class="mp-checkout__payment-option">
                                    <input type="radio" id="payhere" name="payment_method" value="payhere" <?php echo ($data['payment_method'] == 'payhere') ? 'checked' : ''; ?>>
                                    <label for="payhere">
                                        <i class="fas fa-credit-card"></i>
                                        Pay with PayHere (Credit/Debit Cards, Mobile Wallets)
                                    </label>
                                </div>
                                
                                <div class="mp-checkout__payment-option">
                                    <input type="radio" id="cash_on_delivery" name="payment_method" value="cash_on_delivery" <?php echo ($data['payment_method'] == 'cash_on_delivery' || empty($data['payment_method'])) ? 'checked' : ''; ?>>
                                    <label for="cash_on_delivery">
                                        <i class="fas fa-money-bill-wave"></i>
                                        Cash on Delivery
                                    </label>
                                </div>
                                
                                <div class="mp-checkout__payment-option">
                                    <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" <?php echo ($data['payment_method'] == 'bank_transfer') ? 'checked' : ''; ?>>
                                    <label for="bank_transfer">
                                        <i class="fas fa-university"></i>
                                        Bank Transfer
                                    </label>
                                </div>
                            </div>
                            <span class="invalid-feedback"><?php echo $data['payment_method_err']; ?></span>
                            
                            <!-- Bank transfer receipt upload section - shows only when bank_transfer is selected -->
                            <div id="bank_transfer_section" class="mt-3" style="display: <?php echo ($data['payment_method'] == 'bank_transfer') ? 'block' : 'none'; ?>;">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Bank Transfer Details</h5>
                                        <p class="card-text">Please transfer the total amount to the following bank account:</p>
                                        <div class="bank-details">
                                            <p><strong>Bank Name:</strong> Sample Bank</p>
                                            <p><strong>Account Name:</strong> Hopefull Organization</p>
                                            <p><strong>Account Number:</strong> 1234567890</p>
                                            <p><strong>Branch:</strong> Main Branch</p>
                                            <p><strong>Reference:</strong> Your Name + Phone Number</p>
                                        </div>
                                        
                                        <div class="form-group mt-3">
                                            <label for="bank_receipt">Upload Receipt<span class="required">*</span></label>
                                            <input type="file" id="bank_receipt" name="bank_receipt" class="form-control-file <?php echo (!empty($data['bank_receipt_err'])) ? 'is-invalid' : ''; ?>">
                                            <small>Please upload a screenshot or photo of your payment receipt</small>
                                            <span class="invalid-feedback"><?php echo $data['bank_receipt_err'] ?? ''; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mp-checkout__section mp-checkout__terms">
                        <div class="mp-checkout__terms-agreement">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">I agree to the <a href="#" class="mp-checkout__terms-link">Terms and Conditions</a> and <a href="#" class="mp-checkout__terms-link">Privacy Policy</a></label>
                        </div>
                    </div>
                    
                    <div class="mp-checkout__actions">
                        <a href="<?php echo URLROOT; ?>/marketplace/cart" class="mp-checkout__back">Back to Cart</a>
                        <button type="submit" class="mp-checkout__submit">Place Order</button>
                    </div>
                </form>
            </div>
            
            <div class="mp-checkout__summary">
                <h2>Order Summary</h2>
                
                <div class="mp-checkout__items">
                    <?php foreach($data['cart_items'] as $item): ?>
                        <div class="mp-checkout__item">
                            <div class="mp-checkout__item-info">
                                <span class="mp-checkout__item-quantity"><?php echo $item['quantity']; ?> x</span>
                                <span class="mp-checkout__item-name"><?php echo $item['name']; ?></span>
                            </div>
                            <span class="mp-checkout__item-price">Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mp-checkout__totals">
                    <div class="mp-checkout__subtotal">
                        <span>Subtotal</span>
                        <span>Rs. <?php echo number_format($data['total'], 2); ?></span>
                    </div>
                    
                    <div class="mp-checkout__shipping">
                        <span>Shipping</span>
                        <span>Rs. 350.00</span>
                    </div>
                    
                    <div class="mp-checkout__total">
                        <span>Total</span>
                        <span>Rs. <?php echo number_format($data['total'] + 350, 2); ?></span>
                    </div>
                </div>
                
                <div class="mp-checkout__payment-icons">
                    <h4>Secure Payment Options</h4>
                    <div class="mp-checkout__payment-methods">
                        <img src="<?php echo URLROOT; ?>/images/visa.png" alt="Visa">
                        <img src="<?php echo URLROOT; ?>/images/mastercard.png" alt="Mastercard">
                        <img src="<?php echo URLROOT; ?>/images/payhere.png" alt="PayHere">
                    </div>
                </div>
                
                <div class="mp-checkout__notes">
                    <p><i class="fas fa-shield-alt"></i> Your personal data will be used to process your order, support your experience, and for other purposes described in our privacy policy.</p>
                    <p><i class="fas fa-heart"></i> Your purchase directly supports artisans with disabilities.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Script to combine the address fields into the hidden shipping_address field before form submission
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutForm = document.querySelector('.mp-checkout__form form');
        
        // Handle bank transfer section visibility
        const bankTransferRadio = document.getElementById('bank_transfer');
        const payhereRadio = document.getElementById('payhere');
        const cashOnDeliveryRadio = document.getElementById('cash_on_delivery');
        const bankTransferSection = document.getElementById('bank_transfer_section');
        
        // Function to toggle bank transfer section
        function toggleBankTransferSection() {
            if (bankTransferRadio.checked) {
                bankTransferSection.style.display = 'block';
            } else {
                bankTransferSection.style.display = 'none';
            }
        }
        
        // Add event listeners to payment method radio buttons
        bankTransferRadio.addEventListener('change', toggleBankTransferSection);
        payhereRadio.addEventListener('change', toggleBankTransferSection);
        cashOnDeliveryRadio.addEventListener('change', toggleBankTransferSection);
        
        checkoutForm.addEventListener('submit', function(e) {
            // Validate bank receipt upload if bank transfer is selected
            if (bankTransferRadio.checked) {
                const bankReceipt = document.getElementById('bank_receipt');
                if (bankReceipt.files.length === 0) {
                    e.preventDefault();
                    alert('Please upload your bank transfer receipt');
                    return false;
                }
            }
            
            // Get address components
            const streetAddress = document.getElementById('street_address').value.trim();
            const apartment = document.getElementById('apartment').value.trim();
            const city = document.getElementById('city').value.trim();
            const postalCode = document.getElementById('postal_code').value.trim();
            const province = document.getElementById('province').value.trim();
            
            // Build the complete address
            let fullAddress = streetAddress;
            
            if (apartment) {
                fullAddress += ', ' + apartment;
            }
            
            if (city) {
                fullAddress += '\n' + city;
            }
            
            if (postalCode) {
                fullAddress += ', ' + postalCode;
            }
            
            if (province) {
                fullAddress += '\n' + province + ' Province';
            }
            
            // Set the compiled address to the hidden field
            document.getElementById('shipping_address').value = fullAddress;
        });
    });
</script>

<style>
    /* PayHere payment method styling */
    .mp-checkout__payment-option {
        margin-bottom: 15px;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .mp-checkout__payment-option:hover {
        border-color: #6c757d;
    }
    
    .mp-checkout__payment-option input[type="radio"] {
        margin-right: 10px;
    }
    
    .mp-checkout__payment-option input[type="radio"]:checked + label {
        font-weight: bold;
    }
    
    .mp-checkout__payment-option:has(input[type="radio"]:checked) {
        border-color: #007bff;
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .mp-checkout__payment-icons {
        margin-top: 20px;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        background-color: #f8f9fa;
    }
    
    .mp-checkout__payment-methods {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        justify-content: center;
    }
    
    .mp-checkout__payment-methods img {
        height: 30px;
        object-fit: contain;
    }
    
    #bank_transfer_section {
        transition: all 0.3s ease;
    }
    
    .bank-details {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin: 15px 0;
    }
    
    .bank-details p {
        margin-bottom: 8px;
    }
</style>

<?php require APPROOT . '/views/includes/footer.php'; ?>