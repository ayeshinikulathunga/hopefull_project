<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>

<section class="mp-order-details">
    <div class="container">
        <div class="mp-order-details__header">
            <h1>Order Details</h1>
            <a href="<?php echo URLROOT; ?>/marketplace/orders" class="mp-order-details__back-btn">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
        
        <div class="mp-order-details__order-info">
            <div class="mp-order-details__info-header">
                <div>
                    <h2>Order #<?php echo $data['order']->OrderID; ?></h2>
                    <p class="mp-order-details__date">Placed on <?php echo date('F j, Y, g:i a', strtotime($data['order']->OrderDate)); ?></p>
                </div>
                <span class="mp-order-details__status mp-order-details__status-<?php echo strtolower($data['order']->Status); ?>">
                    <?php echo $data['order']->Status; ?>
                </span>
            </div>
            
            <!-- Order Summary Card - Added prominent order summary -->
            <div class="mp-order-details__summary-card">
                <div class="mp-order-details__summary-header">
                    <h3>Order Summary</h3>
                    <span><?php echo count($data['order_items']); ?> item<?php echo count($data['order_items']) > 1 ? 's' : ''; ?></span>
                </div>
                
                <div class="mp-order-details__summary-items">
                    <?php if(!empty($data['order_items'])): ?>
                        <?php foreach($data['order_items'] as $item): ?>
                        <div class="mp-order-details__summary-item">
                            <div class="mp-order-details__summary-item-info">
                                <div class="mp-order-details__summary-item-name"><?php echo $item->ProductName; ?></div>
                                <div class="mp-order-details__summary-item-qty">Qty: <?php echo $item->Quantity; ?></div>
                            </div>
                            <div class="mp-order-details__summary-item-price">Rs. <?php echo number_format($item->Price * $item->Quantity, 2); ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="mp-order-details__summary-item mp-order-details__no-items">
                            <div class="mp-order-details__summary-item-info">
                                <div class="mp-order-details__summary-item-name">No items found for this order.</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="mp-order-details__summary-totals">
                    <div class="mp-order-details__summary-subtotal">
                        <span>Subtotal</span>
                        <span>Rs. <?php echo number_format($data['order']->TotalAmount - 350, 2); ?></span>
                    </div>
                    <div class="mp-order-details__summary-shipping">
                        <span>Shipping</span>
                        <span>Rs. 350.00</span>
                    </div>
                    <div class="mp-order-details__summary-total">
                        <span>Total</span>
                        <span>Rs. <?php echo number_format($data['order']->TotalAmount, 2); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="mp-order-details__sections">
                <div class="mp-order-details__section">
                    <h3>Shipping Address</h3>
                    <p><?php echo nl2br($data['order']->ShippingAddress ?? 'No shipping address provided'); ?></p>
                </div>
                
                <div class="mp-order-details__section">
                    <h3>Shipping Address</h3>
                    <p><?php echo !empty($data['order']->ShippingAddress) ? nl2br(htmlspecialchars($data['order']->ShippingAddress)) : 'No shipping address provided'; ?></p>
                </div>
                
                <div class="mp-order-details__section">
                    <h3>Payment Method</h3>
                    <p>
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
                    </p>
                </div>
                
                <div class="mp-order-details__section">
                    <h3>Order Updates</h3>
                    <div class="mp-order-details__tracking">
                        <ul class="mp-order-details__timeline">
                            <?php
                            $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered'];
                            $currentStatusIndex = array_search($data['order']->Status, $statuses);
                            
                            foreach ($statuses as $index => $status):
                                $isCompleted = $index <= $currentStatusIndex;
                                $isActive = $index === $currentStatusIndex;
                            ?>
                                <li class="mp-order-details__timeline-item <?php echo $isCompleted ? 'mp-order-details__completed' : ''; ?> <?php echo $isActive ? 'mp-order-details__active' : ''; ?>">
                                    <div class="mp-order-details__timeline-marker"></div>
                                    <div class="mp-order-details__timeline-content">
                                        <h4><?php echo $status; ?></h4>
                                        <?php if ($isCompleted): ?>
                                            <p><?php echo $status === 'Pending' ? 'Order placed' : ($status === 'Processing' ? 'Order is being prepared' : ($status === 'Shipped' ? 'Order has been shipped' : 'Order has been delivered')); ?></p>
                                            <span class="mp-order-details__timeline-date">
                                                <?php
                                                // In a real application, you would have timestamps for each status change
                                                if ($index === 0) {
                                                    echo date('F j, Y', strtotime($data['order']->OrderDate));
                                                } else {
                                                    echo 'Status updated';
                                                }
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mp-order-details__items">
            <h3>Order Items</h3>
            <table class="mp-order-details__table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['order_items'])): ?>
                        <?php foreach($data['order_items'] as $item): ?>
                            <tr>
                                <td class="mp-order-details__product">
                                    <?php if(!empty($item->ProductImage)): ?>
                                        <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $item->ProductImage; ?>" 
                                            alt="<?php echo $item->ProductName; ?>" class="mp-order-details__product-image">
                                    <?php else: ?>
                                        <img src="<?php echo URLROOT; ?>/images/placeholder-product.jpg" 
                                            alt="Product image placeholder" class="mp-order-details__product-image">
                                    <?php endif; ?>
                                    <div>
                                        <h4><?php echo $item->ProductName; ?></h4>
                                        <a href="<?php echo URLROOT; ?>/marketplace/product/<?php echo $item->ProductID; ?>" class="mp-order-details__view-product">
                                            View Product
                                        </a>
                                    </div>
                                </td>
                                <td>Rs. <?php echo number_format($item->Price, 2); ?></td>
                                <td class="mp-order-details__quantity"><?php echo $item->Quantity; ?></td>
                                <td>Rs. <?php echo number_format($item->Price * $item->Quantity, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="mp-order-details__no-items">No items found for this order.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="mp-order-details__text-right">Subtotal:</td>
                        <td>Rs. <?php echo number_format($data['order']->TotalAmount - 350, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="mp-order-details__text-right">Shipping:</td>
                        <td>Rs. 350.00</td>
                    </tr>
                    <tr class="mp-order-details__total-row">
                        <td colspan="3" class="mp-order-details__text-right">Total:</td>
                        <td>Rs. <?php echo number_format($data['order']->TotalAmount, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php if($data['order']->Status === 'Delivered'): ?>
            <div class="mp-order-details__review-section">
                <h3>Leave a Review</h3>
                <p>Your feedback helps our artisans improve their products and gives other customers valuable information.</p>
                <form class="mp-order-details__review-form">
                    <div class="mp-order-details__rating">
                        <label>Rating:</label>
                        <div class="mp-order-details__stars">
                            <i class="far fa-star" data-rating="1"></i>
                            <i class="far fa-star" data-rating="2"></i>
                            <i class="far fa-star" data-rating="3"></i>
                            <i class="far fa-star" data-rating="4"></i>
                            <i class="far fa-star" data-rating="5"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="review">Your Review:</label>
                        <textarea id="review" name="review" rows="4" class="form-control" placeholder="Share your experience with the product and artisan..."></textarea>
                    </div>
                    <button type="button" id="submit-review" class="mp-order-details__submit-review">Submit Review</button>
                </form>
            </div>
        <?php endif; ?>
        
        <div class="mp-order-details__actions">
            <?php if($data['order']->Status === 'Pending'): ?>
                <button class="mp-order-details__cancel-btn">Cancel Order</button>
            <?php endif; ?>
            <button class="mp-order-details__print-btn" id="printReceiptBtn">
             <i class="fas fa-print"></i> Print Receipt
    </button>
            <a href="<?php echo URLROOT; ?>/marketplace/orders" class="mp-order-details__back-to-orders">Back to Orders</a>
        </div>
        <div class="mp-receipt print-only">
    <div class="mp-receipt__header">
        <div class="mp-receipt__logo">
            <!-- Replace with your actual logo path -->
            <img src="<?php echo URLROOT; ?>/images/logo.png" alt="Logo">
        </div>
        <div class="mp-receipt__title">
            <h1>Receipt</h1>
            <p>Order #<?php echo $data['order']->OrderID; ?></p>
        </div>
    </div>
    
    <div class="mp-receipt__info">
        <div class="mp-receipt__order-details">
            <h2>Order Information</h2>
            <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($data['order']->OrderDate)); ?></p>
            <p><strong>Status:</strong> <?php echo $data['order']->Status; ?></p>
            <p><strong>Payment Method:</strong> Cash on Delivery</p>
        </div>
        <div class="mp-receipt__customer">
            <h2>Shipping Address</h2>
            <p><?php echo nl2br($data['order']->ShippingAddress ?? 'No shipping address provided'); ?></p>
        </div>
    </div>
    
    <div class="mp-receipt__items">
        <h2>Items Purchased</h2>
        <table class="mp-receipt__table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['order_items'])): ?>
                    <?php foreach($data['order_items'] as $item): ?>
                        <tr>
                            <td><?php echo $item->ProductName; ?></td>
                            <td>Rs. <?php echo number_format($item->Price, 2); ?></td>
                            <td><?php echo $item->Quantity; ?></td>
                            <td>Rs. <?php echo number_format($item->Price * $item->Quantity, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No items found for this order.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Subtotal:</td>
                    <td>Rs. <?php echo number_format($data['order']->TotalAmount - 350, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3">Shipping:</td>
                    <td>Rs. 350.00</td>
                </tr>
                <tr class="mp-receipt__total">
                    <td colspan="3">Total:</td>
                    <td>Rs. <?php echo number_format($data['order']->TotalAmount, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="mp-receipt__footer">
        <p>Thank you for your purchase!</p>
        <p>If you have any questions, please contact our customer support.</p>
        <p>© <?php echo date('Y'); ?> Your Company Name</p>
    </div>
</div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Star rating functionality
        const stars = document.querySelectorAll('.mp-order-details__stars i');
        let selectedRating = 0;
        
        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                const rating = this.getAttribute('data-rating');
                
                // Highlight stars on hover
                stars.forEach(s => {
                    if (s.getAttribute('data-rating') <= rating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });
            
            star.addEventListener('mouseout', function() {
                // Reset stars on mouseout (except selected)
                stars.forEach(s => {
                    if (s.getAttribute('data-rating') <= selectedRating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });
            
            star.addEventListener('click', function() {
                selectedRating = this.getAttribute('data-rating');
                
                // Set selected stars
                stars.forEach(s => {
                    if (s.getAttribute('data-rating') <= selectedRating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });
        });
        
        // Submit review button
        const submitReviewBtn = document.getElementById('submit-review');
        if (submitReviewBtn) {
            submitReviewBtn.addEventListener('click', function() {
                const reviewText = document.getElementById('review').value;
                
                if (selectedRating === 0) {
                    alert('Please select a rating');
                    return;
                }
                
                if (reviewText.trim() === '') {
                    alert('Please write a review');
                    return;
                }
                
                // In a real application, you would submit this to the server
                alert(`Thank you for your ${selectedRating}-star review!`);
                
                // Reset form
                selectedRating = 0;
                document.getElementById('review').value = '';
                stars.forEach(s => {
                    s.classList.remove('fas');
                    s.classList.add('far');
                });
            });
        }
        
        // Cancel order button
        const cancelOrderBtn = document.querySelector('.mp-order-details__cancel-btn');
        if (cancelOrderBtn) {
            cancelOrderBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to cancel this order?')) {
                    // In a real application, you would submit this to the server
                    alert('Order cancellation request has been submitted.');
                }
            });
        }
    });

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