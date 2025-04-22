<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>
<!-- Link to the external CSS file -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/wishlist.css">

<section class="wishlist-container">
    <div class="container">
        <h1 class="wishlist-title">Your Wishlist</h1>
        
        <?php flash('wishlist_message'); ?>
        
        <?php 
        // Use the wishlistItems data passed from the controller
        $wishlistItems = isset($data['wishlistItems']) ? $data['wishlistItems'] : [];
        
        if (empty($wishlistItems)):
        ?>
            <div class="wishlist-empty">
                <div class="wishlist-empty-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h2 class="wishlist-empty-title">Your wishlist is empty</h2>
                <p class="wishlist-empty-message">Looks like you haven't added any items to your wishlist yet.</p>
                <a href="<?php echo URLROOT; ?>/marketplace/allProducts" class="wishlist-browse-btn">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="wishlist-actions">
                <div class="wishlist-count"><?php echo count($wishlistItems); ?> item<?php echo count($wishlistItems) > 1 ? 's' : ''; ?></div>
                <a href="<?php echo URLROOT; ?>/marketplace/allProducts" class="wishlist-continue-shopping">
                    <i class="fas fa-long-arrow-alt-left"></i> Continue Shopping
                </a>
            </div>
            
            <div class="wishlist-list">
                <?php foreach($wishlistItems as $item): ?>
                    <div class="wishlist-item">
                        <div class="wishlist-item-image">
                            <?php if(!empty($item->ProductImage)): ?>
                                <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $item->ProductImage; ?>" 
                                    alt="<?php echo $item->ProductName; ?>">
                            <?php else: ?>
                                <img src="<?php echo URLROOT; ?>/images/placeholder-product.jpg" 
                                    alt="Product image placeholder">
                            <?php endif; ?>
                        </div>
                        <div class="wishlist-item-details">
                            <h3 class="wishlist-item-name"><?php echo $item->ProductName; ?></h3>
                            <div class="wishlist-item-seller">By: <?php echo $item->FirstName . ' ' . $item->LastName; ?></div>
                            
                            <?php if($item->StockQuantity <= 0 || $item->Status == 'OutOfStock'): ?>
                                <div class="wishlist-stock-status out-of-stock">
                                    <i class="fas fa-times-circle"></i> Out of Stock
                                </div>
                            <?php else: ?>
                                <div class="wishlist-stock-status in-stock">
                                    <i class="fas fa-check-circle"></i> In Stock (<?php echo $item->StockQuantity; ?>)
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="wishlist-item-price">
                            Rs. <?php echo number_format($item->Price, 2); ?>
                        </div>
                        <div class="wishlist-item-actions">
                            <a href="<?php echo URLROOT; ?>/marketplace/product/<?php echo $item->ProductID; ?>" 
                               class="wishlist-view-btn">
                                <i class="fas fa-eye"></i> View
                            </a>
                            
                            <?php if($item->StockQuantity > 0 && $item->Status != 'OutOfStock'): ?>
                                <form action="<?php echo URLROOT; ?>/marketplace/addToCart" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $item->ProductID; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="wishlist-cart-btn">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="wishlist-cart-btn disabled" disabled>
                                    <i class="fas fa-cart-plus"></i> Out of Stock
                                </button>
                            <?php endif; ?>
                            
                            <a href="<?php echo URLROOT; ?>/marketplace/removeFromWishlist/<?php echo $item->ProductID; ?>" 
                               class="wishlist-remove-btn" 
                               onclick="return confirm('Are you sure you want to remove this item from your wishlist?');">
                                <i class="fas fa-trash"></i> Remove
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require APPROOT . '/views/includes/footer.php'; ?>