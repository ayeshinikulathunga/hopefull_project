<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>

<section class="mp-cart">
    <div class="container">
        <h1 class="mp-cart__title">Your Shopping Cart</h1>
        
        <?php flash('cart_message'); ?>
        
        <?php if(empty($data['cart_items'])): ?>
            <div class="mp-cart__empty">
                <i class="fas fa-shopping-cart fa-3x"></i>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="<?php echo URLROOT; ?>/marketplace/allProducts" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="mp-cart__container">
                <div class="mp-cart__items">
                    <form action="<?php echo URLROOT; ?>/marketplace/updateCart" method="POST">
                        <table class="mp-cart__table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['cart_items'] as $item): ?>
                                    <tr>
                                        <td class="mp-cart__product">
                                            <?php if(!empty($item['image'])): ?>
                                                <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $item['image']; ?>" 
                                                    alt="<?php echo $item['name']; ?>" class="mp-cart__image">
                                            <?php else: ?>
                                                <img src="<?php echo URLROOT; ?>/images/placeholder-product.jpg" 
                                                    alt="Product image placeholder" class="mp-cart__image">
                                            <?php endif; ?>
                                            <div class="mp-cart__details">
                                                <h3><?php echo $item['name']; ?></h3>
                                            </div>
                                        </td>
                                        <td class="mp-cart__price">Rs. <?php echo number_format($item['price'], 2); ?></td>
                                        <td class="mp-cart__quantity">
                                            <input type="number" name="quantities[<?php echo $item['product_id']; ?>]" 
                                                value="<?php echo $item['quantity']; ?>" min="1" class="mp-cart__quantity-input">
                                        </td>
                                        <td class="mp-cart__total">Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        <td class="mp-cart__action">
                                            <a href="<?php echo URLROOT; ?>/marketplace/removeFromCart/<?php echo $item['product_id']; ?>" 
                                                class="mp-cart__remove">
                                                <i class="fas fa-trash"></i> Remove
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div class="mp-cart__actions">
                            <a href="<?php echo URLROOT; ?>/marketplace/allProducts" class="mp-cart__continue">Continue Shopping</a>
                            <button type="submit" class="mp-cart__update">Update Cart</button>
                        </div>
                    </form>
                </div>
                
                <div class="mp-cart__summary">
                    <h2>Order Summary</h2>
                    
                    <div class="mp-cart__summary-item">
                        <span>Subtotal</span>
                        <span>Rs. <?php echo number_format($data['total'], 2); ?></span>
                    </div>
                    
                    <div class="mp-cart__summary-item">
                        <span>Shipping</span>
                        <span>Calculated at checkout</span>
                    </div>
                    
                    <div class="mp-cart__summary-total">
                        <span>Total</span>
                        <span>Rs. <?php echo number_format($data['total'], 2); ?></span>
                    </div>
                    
                    <div class="mp-cart__checkout">
                        <a href="<?php echo URLROOT; ?>/marketplace/checkout" class="mp-cart__checkout-btn">
                            Proceed to Checkout
                        </a>
                    </div>
                    
                    <div class="mp-cart__notes">
                        <p><i class="fas fa-shield-alt"></i> Secure checkout</p>
                        <p><i class="fas fa-heart"></i> Your purchase supports artisans with disabilities</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require APPROOT . '/views/includes/footer.php'; ?>