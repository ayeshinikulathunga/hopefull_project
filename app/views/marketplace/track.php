<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>

<section class="mp-track">
    <div class="container mp-track__container">
        <h1 class="mp-track__title">Track Your Order</h1>
        
        <?php flash('track_error'); ?>
        
        <div class="mp-track__form">
            <h2 class="mp-track__form-title">Enter Your Order Number</h2>
            <form action="<?php echo URLROOT; ?>/marketplace/track" method="POST">
                <div class="mp-track__input-group">
                    <input type="text" name="order_number" placeholder="e.g. ORD12345" class="mp-track__input" required>
                    <button type="submit" class="mp-track__btn">Track Order</button>
                </div>
                <p class="small text-muted">Your order number can be found in your order confirmation email or in your order history.</p>
            </form>
        </div>
        
        <?php 
        // Get user's recent orders for quick tracking
        // In a real application, you would fetch this from the database
        $recentOrders = isset($data['recent_orders']) ? $data['recent_orders'] : [];
        
        if (!empty($recentOrders)):
        ?>
            <div class="mp-track__recent">
                <h3 class="mp-track__recent-title">Your Recent Orders</h3>
                <div class="mp-track__recent-orders">
                    <?php foreach($recentOrders as $order): ?>
                        <div class="mp-track__order-card">
                            <div class="mp-track__order-info">
                                <h4>Order #<?php echo $order->OrderID; ?></h4>
                                <p class="mp-track__order-date"><?php echo date('F j, Y', strtotime($order->OrderDate)); ?></p>
                            </div>
                            <a href="<?php echo URLROOT; ?>/marketplace/orderDetails/<?php echo $order->OrderID; ?>" class="mp-track__track-btn">
                                Track Order
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-5">
            <p>Having trouble finding your order?</p>
            <p>Contact our customer support at <a href="mailto:support@hopefull.org">support@hopefull.org</a> for assistance.</p>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/includes/footer.php'; ?>