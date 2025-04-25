<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>

<section class="mp-orders">
    <div class="container">
        <h1 class="mp-orders__title">Your Orders</h1>
        
        <?php flash('order_message'); ?>
        
        <?php if(empty($data['orders'])): ?>
            <div class="mp-orders__empty">
                <i class="fas fa-box-open fa-3x"></i>
                <h2>No orders yet</h2>
                <p>You haven't made any purchases yet. Start shopping to support artisans with disabilities.</p>
                <a href="<?php echo URLROOT; ?>/marketplace" class="mp-orders__shop-btn">Shop Now</a>
            </div>
        <?php else: ?>
            <div class="mp-orders__filter">
                <div class="mp-orders__filter-group">
                    <label for="order-status">Filter by Status:</label>
                    <select id="order-status" onchange="filterOrders()">
                        <option value="all">All Orders</option>
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="mp-orders__filter-group">
                    <label for="order-date">Sort by:</label>
                    <select id="order-date" onchange="sortOrders()">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>
            </div>
            
            <div class="mp-orders__list" id="orders-container">
                <?php foreach($data['orders'] as $order): ?>
                    <div class="mp-orders__card" data-status="<?php echo $order->Status; ?>" data-date="<?php echo strtotime($order->OrderDate); ?>">
                        <div class="mp-orders__header">
                            <div class="mp-orders__id">
                                <h3>Order #<?php echo $order->OrderID; ?></h3>
                                <span class="mp-orders__date"><?php echo date('F j, Y', strtotime($order->OrderDate)); ?></span>
                            </div>
                            <div class="mp-orders__status">
                                <span class="mp-orders__status-badge mp-orders__status-<?php echo strtolower($order->Status); ?>">
                                    <?php echo $order->Status; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="mp-orders__summary">
                            <div class="mp-orders__amount">
                                <span class="mp-orders__amount-label">Total:</span>
                                <span class="mp-orders__amount-value">Rs. <?php echo number_format($order->TotalAmount, 2); ?></span>
                            </div>
                        </div>
                        
                        <div class="mp-orders__actions">
                            <a href="<?php echo URLROOT; ?>/marketplace/orderDetails/<?php echo $order->OrderID; ?>" class="mp-orders__details-btn">
                                View Details
                            </a>

                            <?php if($order->Status == 'Pending' || $order->Status == 'Payment Cancelled'): ?>
                                <?php if($order->PaymentMethod == 'payhere'): ?>
                                    <a href="<?php echo URLROOT; ?>/marketplace/retryPayment/<?php echo $order->OrderID; ?>" class="mp-orders__pay-btn">
                                        <i class="fas fa-credit-card"></i> Pay Now
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>


                            
                            <?php if($order->Status == 'Delivered'): ?>
                                <button class="mp-orders__review-btn" onclick="leaveReview('<?php echo $order->OrderID; ?>')">
                                    Leave Review
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    function filterOrders() {
        const status = document.getElementById('order-status').value;
        const orders = document.querySelectorAll('.mp-orders__card');
        
        orders.forEach(order => {
            if (status === 'all' || order.dataset.status === status) {
                order.style.display = 'block';
            } else {
                order.style.display = 'none';
            }
        });
    }
    
    function sortOrders() {
        const sortOption = document.getElementById('order-date').value;
        const ordersContainer = document.getElementById('orders-container');
        const orders = Array.from(document.querySelectorAll('.mp-orders__card'));
        
        orders.sort((a, b) => {
            const dateA = parseInt(a.dataset.date);
            const dateB = parseInt(b.dataset.date);
            
            if (sortOption === 'newest') {
                return dateB - dateA;
            } else {
                return dateA - dateB;
            }
        });
        
        // Clear container and append sorted orders
        ordersContainer.innerHTML = '';
        orders.forEach(order => {
            ordersContainer.appendChild(order);
        });
    }
    
    function leaveReview(orderId) {
        // In a real application, you would redirect to a review form or open a modal
        alert('Review functionality would be implemented here for order: ' + orderId);
    }
</script>

<style>
    .mp-orders__pay-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 16px;
    background-color: #28a745;
    color: #fff;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    margin-left: 10px;
    transition: background-color 0.3s ease;
}

.mp-orders__pay-btn:hover {
    background-color: #218838;
    color: #fff;
    text-decoration: none;
}

.mp-orders__pay-btn i {
    margin-right: 5px;
}
</style>

<?php require APPROOT . '/views/includes/footer.php'; ?>