<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>

<section class="mp-confirmation">
    <div class="container">
        <div class="mp-confirmation__header">
            <i class="fas fa-check-circle mp-confirmation__icon"></i>
            <h1>Thank You for Your Order!</h1>
            <p>Your order has been placed successfully.</p>
        </div>
        
        <div class="mp-confirmation__details">
            <div class="mp-confirmation__order-info">
                <h2>Order Information</h2>
                <div class="mp-confirmation__info-grid">
                    <div class="mp-confirmation__info-item">
                        <span class="mp-confirmation__info-label">Order Number:</span>
                        <span class="mp-confirmation__info-value"><?php echo $data['order']->OrderID; ?></span>
                    </div>
                    
                    <div class="mp-confirmation__info-item">
                        <span class="mp-confirmation__info-label">Date:</span>
                        <span class="mp-confirmation__info-value"><?php echo date('F j, Y, g:i a', strtotime($data['order']->OrderDate)); ?></span>
                    </div>
                    
                    <div class="mp-confirmation__info-item">
                        <span class="mp-confirmation__info-label">Total Amount:</span>
                        <span class="mp-confirmation__info-value">Rs. <?php echo number_format($data['order']->TotalAmount, 2); ?></span>
                    </div>
                    
                    <div class="mp-confirmation__info-item">
                        <span class="mp-confirmation__info-label">Payment Method:</span>
                        <span class="mp-confirmation__info-value">
                            <?php 
                            // Display the actual payment method from database
                            if (!empty($data['order']->PaymentMethod)) {
                                switch($data['order']->PaymentMethod) {
                                    case 'cash_on_delivery':
                                        echo 'Cash on Delivery';
                                        break;
                                    case 'bank_transfer':
                                        echo 'Bank Transfer';
                                        break;
                                    case 'online_payment':
                                        echo 'Online Payment';
                                        break;
                                    default:
                                        echo ucwords(str_replace('_', ' ', $data['order']->PaymentMethod));
                                }
                            } else {
                                echo 'Not specified';
                            }
                            ?>
                        </span>
                    </div>
                    
                    <div class="mp-confirmation__info-item">
                        <span class="mp-confirmation__info-label">Status:</span>
                        <span class="mp-confirmation__info-value mp-confirmation__status-<?php echo strtolower($data['order']->Status); ?>">
                            <?php echo $data['order']->Status; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="mp-confirmation__ordered-items">
                <h2>Ordered Items</h2>
                <table class="mp-confirmation__items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['order_items'] as $item): ?>
                            <tr>
                                <td class="mp-confirmation__product-info">
                                    <?php if(isset($item->ProductImage) && !empty($item->ProductImage)): ?>
                                        <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $item->ProductImage; ?>" 
                                            alt="<?php echo $item->ProductName; ?>" class="mp-confirmation__item-image">
                                    <?php else: ?>
                                        <img src="<?php echo URLROOT; ?>/images/placeholder-product.jpg" 
                                            alt="Product image placeholder" class="mp-confirmation__item-image">
                                    <?php endif; ?>

                                    <span class="mp-confirmation__product-name"><?php echo $item->ProductName; ?></span>
                                </td>
                                <td><?php echo $item->Quantity; ?></td>
                                <td>Rs. <?php echo number_format($item->Price, 2); ?></td>
                                <td>Rs. <?php echo number_format($item->Price * $item->Quantity, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="mp-confirmation__text-right">Subtotal:</td>
                            <td>Rs. <?php echo number_format($data['order']->TotalAmount - 350, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="mp-confirmation__text-right">Shipping:</td>
                            <td>Rs. 350.00</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="mp-confirmation__text-right">Total:</td>
                            <td>Rs. <?php echo number_format($data['order']->TotalAmount, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <div class="mp-confirmation__message">
            <p>We've sent a confirmation email to <strong><?php echo $data['order']->Email; ?></strong> with all the details of your order.</p>
            <p>Your support helps artisans with disabilities showcase their skills and achieve financial independence.</p>
        </div>
        
        <div class="mp-confirmation__actions">
            <a href="<?php echo URLROOT; ?>/marketplace/orders" class="mp-confirmation__secondary-btn">View My Orders</a>
            <a href="<?php echo URLROOT; ?>/marketplace" class="mp-confirmation__primary-btn">Continue Shopping</a>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/includes/footer.php'; ?>