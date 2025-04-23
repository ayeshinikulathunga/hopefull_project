<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>

<section class="mp-payhere">
    <div class="container">
        <h1 class="mp-payhere__title">Processing Your Payment</h1>
        
        <div class="mp-payhere__content">
            <p>You are being redirected to PayHere to complete your payment. Please do not close this window.</p>
            
            <div class="mp-payhere__spinner">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            
            <!-- PayHere Payment Form - This will be auto-submitted -->
            <form method="post" action="<?php echo $data['sandbox'] ? 'https://sandbox.payhere.lk/pay/checkout' : 'https://www.payhere.lk/pay/checkout'; ?>" id="payhereForm">   
                <!-- Essential Parameters -->
                <input type="hidden" name="merchant_id" value="<?php echo $data['merchant_id']; ?>">
                <input type="hidden" name="return_url" value="<?php echo $data['return_url']; ?>">
                <input type="hidden" name="cancel_url" value="<?php echo $data['cancel_url']; ?>">
                <input type="hidden" name="notify_url" value="<?php echo $data['notify_url']; ?>">
                
                <!-- Order Parameters -->
                <input type="hidden" name="order_id" value="<?php echo $data['order']->OrderID; ?>">
                <input type="hidden" name="items" value="Order #<?php echo $data['order']->OrderID; ?>">
                <input type="hidden" name="currency" value="LKR">
                <input type="hidden" name="amount" value="<?php echo $data['order']->TotalAmount; ?>">

                <?php
                    $hash = strtoupper(
                        md5(
                            $data['merchant_id'] . 
                            $data['order']->OrderID . 
                            number_format($data['order']->TotalAmount, 2, '.', '') . 
                            'LKR' . 
                            strtoupper(md5(PAYHERE_MERCHANT_SECRET))
                        )
                    );
                    ?>
    <input type="hidden" name="hash" value="<?php echo $hash; ?>">
                
                <!-- Customer Parameters -->
                <input type="hidden" name="first_name" value="<?php echo $data['user']->Username; ?>">
                <input type="hidden" name="last_name" value="">
                <input type="hidden" name="email" value="<?php echo $data['user']->Email; ?>">
                <input type="hidden" name="phone" value="<?php echo $data['shipping']->ContactPhone; ?>">
                <input type="hidden" name="address" value="<?php echo $data['shipping']->ShippingAddress; ?>">
                <input type="hidden" name="city" value="">
                <input type="hidden" name="country" value="Sri Lanka">
                
                <!-- Optional Parameters -->
                <input type="hidden" name="delivery_address" value="<?php echo $data['shipping']->ShippingAddress; ?>">
                <input type="hidden" name="delivery_city" value="">
                <input type="hidden" name="delivery_country" value="Sri Lanka">
                <input type="hidden" name="custom_1" value="<?php echo $_SESSION['user_id']; ?>">
                <input type="hidden" name="custom_2" value="<?php echo $data['shipping']->ShippingID; ?>">
                
                <!-- Submit button (hidden but available as fallback) -->
                <button type="submit" id="payhereSubmitBtn" style="display: none;">Pay Now</button>
            </form>
            
            <div class="mp-payhere__manual">
                <p>If you are not automatically redirected, please click the button below:</p>
                <button type="button" id="manualSubmitBtn" class="btn btn-primary" onclick="document.getElementById('payhereForm').submit();">Continue to Payment</button>
            </div>
        </div>
    </div>
</section>

<script>
    // Auto-submit the form after a short delay
    window.onload = function() {
        setTimeout(function() {
            document.getElementById('payhereForm').submit();
        }, 1500); // 1.5 seconds delay to show the loading spinner
    };
</script>

<style>
    .mp-payhere {
        padding: 50px 0;
    }
    
    .mp-payhere__title {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .mp-payhere__content {
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
        background: #f9f9f9;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .mp-payhere__spinner {
        margin: 30px 0;
        font-size: 40px;
        color: #007bff;
    }
    
    .mp-payhere__manual {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
    }
    
    .fa-spin {
        animation: fa-spin 1s infinite linear;
    }
    
    @keyframes fa-spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>

<?php require APPROOT . '/views/includes/footer.php'; ?>