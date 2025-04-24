<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/marketplace.css">
<section class="mp-product-detail">
    <div class="container">
        <div class="mp-breadcrumb">
            <a href="<?php echo URLROOT; ?>/marketplace">Marketplace</a> &gt; 
            <span><?php echo $data['product']->ProductName; ?></span>
        </div>
        
        <div class="mp-product-detail__container">
            <!-- Left column: Product image -->
            <div class="mp-product-detail__images">
                <?php if(!empty($data['product']->ProductImage)): ?>
                    <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $data['product']->ProductImage; ?>" 
                        alt="<?php echo $data['product']->ProductName; ?>" class="mp-product-detail__main-image">
                <?php else: ?>
                    <img src="<?php echo URLROOT; ?>/images/placeholder-product.jpg" 
                        alt="Product image placeholder" class="mp-product-detail__main-image">
                <?php endif; ?>
            </div>
            
            <!-- Right column: Product info -->
            <div class="mp-product-detail__info">
                <!-- Product title and description -->
                <h1><?php echo $data['product']->ProductName; ?></h1>
                
                <div class="mp-product-detail__description">
                <p><?php echo $data['product']->Description; ?></p>
                </div>
                
                <div class="mp-product-detail__meta">
                    <div class="mp-product-detail__seller">
                        Created by <strong>Hopefull Marketplace</strong>
                    </div>
                    <span class="mp-product-detail__category">Handicrafts</span>
                </div>
                
                <!-- Price and availability -->
                <div class="mp-product-detail__pricing">
                    <h2 class="mp-product-detail__price">Rs. <?php echo number_format($data['product']->Price, 2); ?></h2>
                    
                    <div class="mp-product-detail__stock">
                        <?php if($data['product']->StockQuantity > 0): ?>
                            <span class="mp-stock-status mp-in-stock">In Stock (<?php echo $data['product']->StockQuantity; ?> available)</span>
                        <?php else: ?>
                            <span class="mp-stock-status mp-out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quantity selector and add to cart -->
                <?php if(isset($_SESSION['user_id']) && $data['product']->StockQuantity > 0): ?>
                    <form action="<?php echo URLROOT; ?>/marketplace/addToCart" method="POST" class="mp-purchase-form">
                        <input type="hidden" name="product_id" value="<?php echo $data['product']->ProductID; ?>">
                        
                        <div class="mp-purchase-options">
                            <div class="mp-quantity-selector">
                                <label for="quantity">Quantity:</label>
                                <div class="mp-quantity-control">
                                    <button type="button" class="mp-quantity-btn" onclick="decrementQuantity()">-</button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $data['product']->StockQuantity; ?>">
                                    <button type="button" class="mp-quantity-btn" onclick="incrementQuantity()">+</button>
                                </div>
                            </div>
                            
                            <button type="submit" class="mp-add-cart">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <a href="<?php echo URLROOT; ?>/marketplace/addToWishlist/<?php echo $data['product']->ProductID; ?>" 
                                    class="mp-wishlist-btn">
                                        <i class="far fa-heart"></i> Add to Wishlist
                                    </a>
                                <?php endif; ?>
                            </div>
                    </form>
                <?php elseif(!isset($_SESSION['user_id'])): ?>
                    <div class="mp-login-prompt">
                        <p>Please <a href="<?php echo URLROOT; ?>/users/login">sign in</a> to add items to your cart.</p>
                    </div>
                <?php endif; ?>
                
                <!-- Benefits section -->
                <div class="mp-product-benefits">
                    <div class="mp-benefit">
                        <i class="fas fa-heart"></i>
                        <p>Your purchase directly supports artisans with disabilities.</p>
                    </div>
                    <div class="mp-benefit">
                        <i class="fas fa-truck"></i>
                        <p>Delivery available across Sri Lanka</p>
                    </div>
                </div>
            </div>
        </div>
        
       <!-- Product tabs section -->
<div class="mp-tabs">
    <div class="mp-tab-buttons">
        <button class="mp-tab-btn active" onclick="openTab(event, 'description')">Description</button>
        <button class="mp-tab-btn" onclick="openTab(event, 'shipping')">Shipping & Returns</button>
    </div>
    
    <div id="description" class="mp-tab-content active">
        <h3>Product Description</h3>
        <p><?php echo $data['product']->Description; ?></p>
    </div>
    
    <div id="shipping" class="mp-tab-content">
        <h3>Shipping & Returns</h3>
        <p>Delivery is available throughout Sri Lanka. Items are typically shipped within 2-3 business days.</p>
        <p>Shipping costs are calculated based on your location and will be displayed at checkout.</p>
        <p>If you're not completely satisfied with your purchase, you may return it within 7 days for a full refund. Please contact us for return shipping instructions.</p>
    </div>
</div>

<script>
    function incrementQuantity() {
        const quantityInput = document.getElementById('quantity');
        const max = parseInt(quantityInput.getAttribute('max'));
        const currentValue = parseInt(quantityInput.value);
        
        if (currentValue < max) {
            quantityInput.value = currentValue + 1;
        }
    }
    
    function decrementQuantity() {
        const quantityInput = document.getElementById('quantity');
        const currentValue = parseInt(quantityInput.value);
        
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    }
    
    function openTab(evt, tabName) {
        // Hide all tab content
        const tabContents = document.getElementsByClassName('mp-tab-content');
        for (let i = 0; i < tabContents.length; i++) {
            tabContents[i].classList.remove('active');
        }
        
        // Remove 'active' class from all tab buttons
        const tabButtons = document.getElementsByClassName('mp-tab-btn');
        for (let i = 0; i < tabButtons.length; i++) {
            tabButtons[i].classList.remove('active');
        }
        
        // Show the selected tab content and add 'active' class to the button
        document.getElementById(tabName).classList.add('active');
        evt.currentTarget.classList.add('active');
    }
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>