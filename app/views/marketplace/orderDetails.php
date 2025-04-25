<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>

<section class="mp-order-details">
    <div class="container">
        <div class="mp-order-details__header">
            <h1>Order Details</h1>
            <a href="<?php echo URLROOT; ?>/marketplace/orders" class="mp-order-details__back-btn">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
        
        <?php flash('order_message'); ?>
        <?php flash('order_error'); ?>
        
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
            
            <!-- Order Summary Card -->
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
                                case 'bank':
                                    echo 'Bank Transfer';
                                    break;
                                case 'online_payment':
                                case 'payhere':
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

                <!-- Bank Payment Section -->
                <?php if(isset($data['order']->PaymentMethod) && ($data['order']->PaymentMethod == 'bank' || $data['order']->PaymentMethod == 'bank_transfer')): ?>
                    <div class="mp-order-details__section mp-order-details__bank-payment">
                        <h3>Bank Payment Details</h3>
                        <?php if(isset($data['bank_payment']) && $data['bank_payment']): ?>
                            <div class="mp-order-details__payment-status">
                                <div class="mp-order-details__status-indicator <?php echo strtolower($data['bank_payment']->Status); ?>">
                                    <span class="mp-order-details__status-icon">
                                        <?php if($data['bank_payment']->Status === 'Pending'): ?>
                                            <i class="fas fa-clock"></i>
                                        <?php elseif($data['bank_payment']->Status === 'Verified'): ?>
                                            <i class="fas fa-check-circle"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times-circle"></i>
                                        <?php endif; ?>
                                    </span>
                                    <div class="mp-order-details__status-text">
                                        <h4>Payment Status: 
                                            <span class="mp-order-details__status-value <?php echo strtolower($data['bank_payment']->Status); ?>">
                                                <?php echo $data['bank_payment']->Status; ?>
                                            </span>
                                        </h4>
                                        <?php if($data['bank_payment']->Status === 'Pending'): ?>
                                            <p>Your payment is being verified by our team. This usually takes 1-2 business days.</p>
                                        <?php elseif($data['bank_payment']->Status === 'Verified'): ?>
                                            <p>Your payment has been verified and your order is being processed.</p>
                                        <?php else: ?>
                                            <p>Your payment has been rejected. Please contact customer support for more information.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mp-order-details__bank-payment-info">
                                <p><strong>Upload Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($data['bank_payment']->UploadDate)); ?></p>
                                <?php if($data['bank_payment']->Status !== 'Pending'): ?>
                                    <p><strong>Verification Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($data['bank_payment']->VerificationDate)); ?></p>
                                <?php endif; ?>
                                
                                <?php if(!empty($data['bank_payment']->Notes)): ?>
                                    <div class="mp-order-details__payment-notes">
                                        <h4>Notes:</h4>
                                        <p><?php echo $data['bank_payment']->Notes; ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mp-order-details__slip-image">
                                    <h4>Payment Slip</h4>
                                    <?php 
                                    $fileExt = pathinfo($data['bank_payment']->SlipFile, PATHINFO_EXTENSION);
                                    if(in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                        <a href="<?php echo URLROOT; ?>/uploads/slips/<?php echo $data['bank_payment']->SlipFile; ?>" target="_blank">
                                            <img src="<?php echo URLROOT; ?>/uploads/slips/<?php echo $data['bank_payment']->SlipFile; ?>" 
                                                alt="Payment Slip" class="mp-order-details__payment-slip">
                                            <div class="mp-order-details__image-zoom">
                                                <i class="fas fa-search-plus"></i> Click to enlarge
                                            </div>
                                        </a>
                                    <?php elseif(strtolower($fileExt) === 'pdf'): ?>
                                        <p>Payment slip uploaded as PDF. <a href="<?php echo URLROOT; ?>/uploads/slips/<?php echo $data['bank_payment']->SlipFile; ?>" target="_blank">Click here to view.</a></p>
                                    <?php else: ?>
                                        <p>Payment slip uploaded. <a href="<?php echo URLROOT; ?>/uploads/slips/<?php echo $data['bank_payment']->SlipFile; ?>" target="_blank">Click here to view.</a></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mp-order-details__no-payment">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>No payment slip has been uploaded yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="mp-order-details__section">
                    <h3>Order Updates</h3>
                    <div class="mp-order-details__tracking">
                        <ul class="mp-order-details__timeline">
                            <?php
                            $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered'];
                            $currentStatusIndex = array_search($data['order']->Status, $statuses);
                            
                            // Handle Cancelled status
                            if ($data['order']->Status === 'Cancelled') {
                                $statuses = ['Pending', 'Cancelled'];
                                $currentStatusIndex = 1;
                            }
                            
                            foreach ($statuses as $index => $status):
                                $isCompleted = $index <= $currentStatusIndex;
                                $isActive = $index === $currentStatusIndex;
                            ?>
                                <li class="mp-order-details__timeline-item <?php echo $isCompleted ? 'mp-order-details__completed' : ''; ?> <?php echo $isActive ? 'mp-order-details__active' : ''; ?>">
                                    <div class="mp-order-details__timeline-marker"></div>
                                    <div class="mp-order-details__timeline-content">
                                        <h4><?php echo $status; ?></h4>
                                        <?php if ($isCompleted): ?>
                                            <p><?php echo $status === 'Pending' ? 'Order placed' : ($status === 'Processing' ? 'Order is being prepared' : ($status === 'Shipped' ? 'Order has been shipped' : ($status === 'Delivered' ? 'Order has been delivered' : 'Order has been cancelled'))); ?></p>
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
        
        <!-- Display cancellation request status if exists -->
        <?php if(isset($data['cancellation_request']) && $data['cancellation_request']): ?>
            <div class="mp-order-details__cancellation-status">
                <h4>Cancellation Request</h4>
                <div class="mp-order-details__status-info">
                    <p><strong>Status:</strong> 
                        <span class="badge badge-<?php echo $data['cancellation_request']->Status === 'Pending' ? 'warning' : ($data['cancellation_request']->Status === 'Approved' ? 'success' : 'danger'); ?>">
                            <?php echo $data['cancellation_request']->Status; ?>
                        </span>
                    </p>
                    <p><strong>Reason:</strong> <?php echo $data['cancellation_request']->Reason; ?></p>
                    <p><strong>Requested On:</strong> <?php echo date('F j, Y - g:i A', strtotime($data['cancellation_request']->RequestDate)); ?></p>
                    
                    <?php if($data['cancellation_request']->Status !== 'Pending'): ?>
                        <p><strong>Processed On:</strong> <?php echo date('F j, Y - g:i A', strtotime($data['cancellation_request']->ProcessedDate)); ?></p>
                        <?php if(!empty($data['cancellation_request']->Notes)): ?>
                            <p><strong>Notes:</strong> <?php echo $data['cancellation_request']->Notes; ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-info">Your cancellation request is being reviewed by the seller. We'll notify you once it's processed.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
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
                                <td><?php echo $item->Quantity; ?></td>
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
            <?php if(isset($data['cancellation_allowed']) && $data['cancellation_allowed']): ?>
                <?php if(isset($data['cancellation_request']) && $data['cancellation_request']): ?>
                    <!-- Already has a cancellation request - no action needed -->
                <?php else: ?>
                    <!-- Show cancel button if there's no existing request and cancellation is allowed -->
                    <button class="mp-order-details__cancel-btn" id="requestCancellationBtn">
                        <i class="fas fa-times-circle"></i> Request Cancellation
                    </button>
                <?php endif; ?>
            <?php endif; ?>
            <button class="mp-order-details__print-btn" id="printReceiptBtn">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="<?php echo URLROOT; ?>/marketplace/orders" class="mp-order-details__back-to-orders">Back to Orders</a>
        </div>
        
       <!--  Cancellation Request Modal -->
<div id="cancellationModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        <div class="cancellation-header">
            <div class="cancel-icon">
                <i class="fas fa-times"></i>
            </div>
            <h2>Request Order Cancellation</h2>
        </div>
        
        <form action="<?php echo URLROOT; ?>/marketplace/requestCancellation" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $data['order']->OrderID; ?>">
            
            <div class="form-group">
                <label for="reason">Reason for Cancellation:</label>
                <textarea id="reason" name="reason" class="form-control" rows="4" required placeholder="Please explain why you need to cancel this order..."><?php echo isset($data['cancellation']) ? $data['cancellation']['reason'] : ''; ?></textarea>
                <?php if(isset($data['cancellation']) && !empty($data['cancellation']['reason_err'])): ?>
                    <span class="invalid-feedback"><?php echo $data['cancellation']['reason_err']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="cancellation-notice">
                <p><strong>Note:</strong> Cancellation requests can only be processed if the order is still in the "Pending" or "Processing" stage.</p>
                <p>Once an order has been shipped, it cannot be cancelled.</p>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="cancelCancellationBtn">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
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
                    <p><strong>Payment Method:</strong> 
                        <?php 
                        if (!empty($data['order']->PaymentMethod)) {
                            switch($data['order']->PaymentMethod) {
                                case 'cash_on_delivery':
                                    echo 'Cash on Delivery';
                                    break;
                                case 'bank_transfer':
                                case 'bank':
                                    echo 'Bank Transfer';
                                    break;
                                case 'online_payment':
                                case 'payhere':
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
                <p>© <?php echo date('Y'); ?> Hopefull Marketplace</p>
            </div>
        </div>
    </div>
</section>

<style>
/* Bank Payment Verification Status Styles */
.mp-order-details__bank-payment {
    margin-top: 20px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.mp-order-details__bank-payment h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #343a40;
    font-weight: 600;
}

.mp-order-details__payment-status {
    margin-bottom: 20px;
}

.mp-order-details__status-indicator {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    border-radius: 8px;
}

.mp-order-details__status-indicator.pending {
    background-color: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.mp-order-details__status-indicator.verified {
    background-color: rgba(40, 167, 69, 0.1);
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.mp-order-details__status-indicator.rejected {
    background-color: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.3);
}

.mp-order-details__status-icon {
    font-size: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px;
    color: #6c757d;
    text-align: center;
}

.mp-order-details__no-payment i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #adb5bd;
}
</style>

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
        
        // Cancellation modal functionality
        const cancellationModal = document.getElementById('cancellationModal');
        const requestCancellationBtn = document.getElementById('requestCancellationBtn');
        const cancelCancellationBtn = document.getElementById('cancelCancellationBtn');
        const closeBtn = cancellationModal ? cancellationModal.querySelector('.close') : null;
        
        // Open modal when request cancellation button is clicked
        if (requestCancellationBtn && cancellationModal) {
            requestCancellationBtn.addEventListener('click', function() {
                cancellationModal.style.display = 'block';
            });
        }
        
        // Close modal when close button is clicked
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                cancellationModal.style.display = 'none';
            });
        }
        
        // Close modal when cancel button is clicked
        if (cancelCancellationBtn) {
            cancelCancellationBtn.addEventListener('click', function() {
                cancellationModal.style.display = 'none';
            });
        }
        
        // Close modal when clicking outside of it
        window.addEventListener('click', function(event) {
            if (event.target == cancellationModal) {
                cancellationModal.style.display = 'none';
            }
        });
        
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
    });

    // Modal handling JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const cancellationModal = document.getElementById('cancellationModal');
    const requestCancellationBtn = document.getElementById('requestCancellationBtn');
    const cancelCancellationBtn = document.getElementById('cancelCancellationBtn');
    const closeBtn = cancellationModal ? cancellationModal.querySelector('.close') : null;
    const reasonTextarea = document.getElementById('reason');
    
    // Open modal when request cancellation button is clicked
    if (requestCancellationBtn && cancellationModal) {
        requestCancellationBtn.addEventListener('click', function() {
            // Display the modal
            cancellationModal.style.display = 'block';
            
            // Focus on the reason textarea
            if (reasonTextarea) {
                setTimeout(() => {
                    reasonTextarea.focus();
                }, 300);
            }
            
            // Prevent page scrolling when modal is open
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close modal functions
    function closeModal() {
        if (cancellationModal) {
            cancellationModal.style.display = 'none';
            
            // Re-enable page scrolling
            document.body.style.overflow = '';
            
            // Reset form if needed
            if (reasonTextarea) {
                reasonTextarea.value = '';
            }
        }
    }
    
    // Close modal when close button is clicked
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    // Close modal when cancel button is clicked
    if (cancelCancellationBtn) {
        cancelCancellationBtn.addEventListener('click', closeModal);
    }
    
    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target == cancellationModal) {
            closeModal();
        }
    });
    
    // Close modal when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && cancellationModal.style.display === 'block') {
            closeModal();
        }
    });
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>
