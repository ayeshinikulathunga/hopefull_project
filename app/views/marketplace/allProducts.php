<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>

<!-- Filters Section -->
<section class="mp-filters">
    <div class="container">
        <div class="mp-filter-bar">
            <div class="mp-search-box">
                <form action="<?php echo URLROOT; ?>/marketplace/search" method="GET">
                    <input type="text" name="term" placeholder="Search products..." class="mp-search-input">
                    <button type="submit" class="mp-btn-search"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="mp-category-filter">
                <select id="category-select" onchange="filterByCategory(this.value)">
                    <option value="">All Categories</option>
                    <option value="Handicrafts" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Handicrafts') ? 'selected' : ''; ?>>Handicrafts</option>
                    <option value="Jewelry" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Jewelry') ? 'selected' : ''; ?>>Jewelry</option>
                    <option value="Textiles" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Textiles') ? 'selected' : ''; ?>>Textiles</option>
                    <option value="Home" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Home') ? 'selected' : ''; ?>>Home Decor</option>
                    <option value="Art" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Art') ? 'selected' : ''; ?>>Art</option>
                </select>
            </div>
            <div class="mp-sort-options">
                <select id="sort-select" onchange="sortProducts(this.value)">
                    <option value="newest">Newest First</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Main Products Section -->
<section class="mp-products">
    <div class="container">
        <?php if(isset($_GET['category'])): ?>
            <h1 class="mb-4"><?php echo htmlspecialchars($_GET['category']); ?> Products</h1>
        <?php else: ?>
            <h1 class="mb-4">All Products</h1>
        <?php endif; ?>
        
        <?php flash('cart_message'); ?>
        
        <div class="mp-products__grid" id="products-container">
            <?php if(!empty($data['products'])): ?>
                <?php foreach($data['products'] as $product): ?>
                    <div class="mp-product-card" data-category="<?php echo $product->Category; ?>" data-price="<?php echo $product->Price; ?>">
                        <div class="mp-product-card__image-container">
                            <?php if(!empty($product->ProductImage)): ?>
                                <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $product->ProductImage; ?>" 
                                    alt="<?php echo $product->ProductName; ?>" class="mp-product-card__image">
                            <?php else: ?>
                                <img src="<?php echo URLROOT; ?>/images/placeholder-product.jpg" 
                                    alt="Product image placeholder" class="mp-product-card__image">
                            <?php endif; ?>
                            
                            <!-- Add wishlist button -->
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="<?php echo URLROOT; ?>/marketplace/addToWishlist/<?php echo $product->ProductID; ?>" 
                                   class="mp-wishlist-icon">
                                    <i class="far fa-heart"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="mp-product-card__content">
                            <h3 class="mp-product-card__title"><?php echo $product->ProductName; ?></h3>
                            
                            <!-- Stock status indicator -->
                            <?php if($product->StockQuantity <= 0 || $product->Status == 'OutOfStock'): ?>
                                <span class="mp-stock-status mp-out-of-stock">Out of Stock</span>
                            <?php else: ?>
                                <span class="mp-stock-status mp-in-stock">In Stock (<?php echo $product->StockQuantity; ?>)</span>
                            <?php endif; ?>
                            
                            <p class="mp-product-card__seller">By <?php echo $product->FirstName . ' ' . $product->LastName; ?></p>
                            <div class="mp-product-card__footer">
                                <span class="mp-product-card__price">Rs. <?php echo number_format($product->Price, 2); ?></span>
                                
                                <!-- Add to cart button -->
                                <?php if($product->StockQuantity > 0 && $product->Status != 'OutOfStock'): ?>
                                    <form action="<?php echo URLROOT; ?>/marketplace/addToCart" method="POST" class="mp-quick-cart-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product->ProductID; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="mp-quick-add-cart">
                                            <i class="fas fa-cart-plus"></i> Add
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="mp-out-of-stock-badge">Out of Stock</span>
                                <?php endif; ?>
                                
                                <a href="<?php echo URLROOT; ?>/marketplace/product/<?php echo $product->ProductID; ?>" 
                                   class="mp-product-card__button">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="mp-no-products">
                    <p>No products available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if(!empty($data['pagination'])): ?>
            <div class="mp-pagination">
                <?php if($data['pagination']['currentPage'] > 1): ?>
                    <a href="<?php echo URLROOT; ?>/marketplace/allProducts?page=<?php echo $data['pagination']['currentPage'] - 1; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $data['pagination']['totalPages']; $i++): ?>
                    <a href="<?php echo URLROOT; ?>/marketplace/allProducts?page=<?php echo $i; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>" 
                       class="<?php echo $i == $data['pagination']['currentPage'] ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if($data['pagination']['currentPage'] < $data['pagination']['totalPages']): ?>
                    <a href="<?php echo URLROOT; ?>/marketplace/allProducts?page=<?php echo $data['pagination']['currentPage'] + 1; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    function filterByCategory(category) {
        if (category === '') {
            window.location.href = '<?php echo URLROOT; ?>/marketplace/allProducts';
        } else {
            window.location.href = '<?php echo URLROOT; ?>/marketplace/allProducts?category=' + encodeURIComponent(category);
        }
    }
    
    function sortProducts(sortOption) {
        const productsContainer = document.getElementById('products-container');
        const products = Array.from(document.querySelectorAll('.mp-product-card'));
        
        products.sort((a, b) => {
            if (sortOption === 'price-low') {
                return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
            } else if (sortOption === 'price-high') {
                return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
            }
            // Default is newest first (already sorted by the database)
            return 0;
        });
        
        // Clear container and append sorted products
        productsContainer.innerHTML = '';
        products.forEach(product => {
            productsContainer.appendChild(product);
        });
    }
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>