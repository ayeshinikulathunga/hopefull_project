<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>
<!-- Add link to the new CSS file -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/market_home.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components.css">

<!-- Call to Action Section -->
<section class="mp-home-cta">
    <div class="container">
        <?php if(isset($_SESSION['user_id']) && isset($_SESSION['user_name'])): ?>
            <h2 class="mp-home-cta__title">Hi <?php echo $_SESSION['user_name']; ?>,</h2>
            <h2 class="mp-home-cta__title">Make a Difference Today</h2>
        <?php else: ?>
            <h2 class="mp-home-cta__title">Make a Difference Today</h2>
        <?php endif; ?>
        <p class="mp-home-cta__description">Your purchase empowers artisans with disabilities by providing them with sustainable income and recognition for their skills.</p>
        <a href="<?php echo URLROOT; ?>/marketplace/allProducts" class="mp-home-cta__button">Shop Now</a>
    </div>
</section>



<!-- Featured Categories Section -->
<section class="mp-home-categories">
    <div class="container">
        <h2 class="mp-home-section-title">Browse by Category</h2>
        <div class="mp-home-categories__grid">
            <div class="mp-home-category-card">
                <img src="<?php echo URLROOT; ?>/images/categories/handicrafts.jpg" alt="Handicrafts" class="mp-home-category-card__image">
                <div class="mp-home-category-card__content">
                    <h3 class="mp-home-category-card__title">Handicrafts</h3>
                    <p class="mp-home-category-card__count">15 Products</p>
                    <a href="<?php echo URLROOT; ?>/marketplace/allProducts?category=Handicrafts" class="mp-home-category-card__button">View Products</a>
                </div>
            </div>
            <div class="mp-home-category-card">
                <img src="<?php echo URLROOT; ?>/images/categories/jewelry.jpg" alt="Jewelry" class="mp-home-category-card__image">
                <div class="mp-home-category-card__content">
                    <h3 class="mp-home-category-card__title">Jewelry</h3>
                    <p class="mp-home-category-card__count">8 Products</p>
                    <a href="<?php echo URLROOT; ?>/marketplace/allProducts?category=Jewelry" class="mp-home-category-card__button">View Products</a>
                </div>
            </div>
            <div class="mp-home-category-card">
                <img src="<?php echo URLROOT; ?>/images/categories/textiles.jpg" alt="Textiles" class="mp-home-category-card__image">
                <div class="mp-home-category-card__content">
                    <h3 class="mp-home-category-card__title">Textiles</h3>
                    <p class="mp-home-category-card__count">12 Products</p>
                    <a href="<?php echo URLROOT; ?>/marketplace/allProducts?category=Textiles" class="mp-home-category-card__button">View Products</a>
                </div>
            </div>
            <div class="mp-home-category-card">
                <img src="<?php echo URLROOT; ?>/images/categories/home-decor.jpg" alt="Home Decor" class="mp-home-category-card__image">
                <div class="mp-home-category-card__content">
                    <h3 class="mp-home-category-card__title">Home Decor</h3>
                    <p class="mp-home-category-card__count">10 Products</p>
                    <a href="<?php echo URLROOT; ?>/marketplace/allProducts?category=Home" class="mp-home-category-card__button">View Products</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Products Section -->
<section class="mp-home-products">
    <div class="container">
        <div class="mp-home-products__header">
            <h2 class="mp-home-products__title">Recent Products</h2>
            <a href="<?php echo URLROOT; ?>/marketplace/allProducts" class="mp-home-products__link">
                View All Products <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="mp-home-products__grid">
            <?php if(!empty($data['recentProducts']) && count($data['recentProducts']) > 0): ?>
                <?php foreach(array_slice($data['recentProducts'], 0, 3) as $product): ?>
                    <div class="mp-home-product-card">
                        <div class="mp-home-product-card__image-container">
                            <?php if(!empty($product->ProductImage)): ?>
                                <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $product->ProductImage; ?>" 
                                    alt="<?php echo $product->ProductName; ?>" class="mp-home-product-card__image">
                            <?php else: ?>
                                <img src="<?php echo URLROOT; ?>/images/placeholder-product.jpg" 
                                    alt="Product image placeholder" class="mp-home-product-card__image">
                            <?php endif; ?>
                            
                            <!-- Add wishlist button -->
                            <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="<?php echo URLROOT; ?>/marketplace/addToWishlist/<?php echo $product->ProductID; ?>" 
                               class="mp-wishlist-icon">
                                <i class="far fa-heart"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        <div class="mp-home-product-card__content">
                            <h3 class="mp-home-product-card__title"><?php echo $product->ProductName; ?></h3>
                            
                            <!-- Stock status indicator -->
                            <?php if($product->StockQuantity <= 0 || $product->Status == 'OutOfStock'): ?>
                                <span class="mp-home-out-of-stock">Out of Stock</span>
                            <?php endif; ?>
                            
                            <p class="mp-home-product-card__seller">By <?php echo $product->FirstName . ' ' . $product->LastName; ?></p>
                            <div class="mp-home-product-card__footer">
                                <span class="mp-home-product-card__price">Rs. <?php echo number_format($product->Price, 2); ?></span>
                                
                                <!-- Add to cart button -->
                                <?php if($product->StockQuantity > 0 && $product->Status != 'OutOfStock'): ?>
                                    <form action="<?php echo URLROOT; ?>/marketplace/addToCart" method="POST" class="mp-quick-cart-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product->ProductID; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="mp-home-product-card__cart-btn">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <a href="<?php echo URLROOT; ?>/marketplace/product/<?php echo $product->ProductID; ?>" 
                                   class="mp-home-product-card__button">View Details</a>
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
    </div>
</section>



<!-- Artisan Stories Section -->
<section class="mp-home-artisans" id="artisan-stories">
    <div class="container">
        <h2 class="mp-home-section-title">Artisan Stories</h2>
        <div class="mp-home-artisans__container">
            <?php if(!empty($data['recentProducts']) && count($data['recentProducts']) > 0): ?>
                <?php foreach(array_slice($data['recentProducts'], 0, 2) as $product): ?>
                    <div class="mp-home-artisan-story">
                        <div class="mp-home-artisan-story__content">
                            <div class="mp-home-artisan-story__quote-wrapper">
                                <p class="mp-home-artisan-story__quote">
                                    <?php 
                                    // Show full product description or fallback to default text
                                    echo !empty($product->Description) 
                                        ? $product->Description
                                        : 'Being able to sell my handcrafted products through Hopefull has given me financial independence and a sense of purpose. Through this platform, I\'ve been able to showcase my talents and connect with customers who appreciate handmade crafts. The support I\'ve received has been tremendous and has transformed my life.';
                                    ?>
                                </p>
                                <div class="mp-home-artisan-story__gradient"></div>
                            </div>
                            <div class="mp-home-artisan-story__read-more">
                                Read More <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="mp-home-artisan-story__info">
                                <h4 class="mp-home-artisan-story__name">
                                    <?php echo $product->FirstName . ' ' . $product->LastName; ?>
                                </h4>
                                <p class="mp-home-artisan-story__profession">
                                    Artisan - <?php echo $product->Category; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback content if no products are available -->
                <div class="mp-home-artisan-story">
                    <div class="mp-home-artisan-story__content">
                        <div class="mp-home-artisan-story__quote-wrapper">
                            <p class="mp-home-artisan-story__quote">
                                Being able to sell my handcrafted products through Hopefull has given me financial independence and a sense of purpose. Through this platform, I've been able to showcase my talents and connect with customers who appreciate handmade crafts. The support I've received has been tremendous and has transformed my life.
                            </p>
                            <div class="mp-home-artisan-story__gradient"></div>
                        </div>
                        <div class="mp-home-artisan-story__read-more">
                            Read More <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="mp-home-artisan-story__info">
                            <h4 class="mp-home-artisan-story__name">Ranjith Kumar</h4>
                            <p class="mp-home-artisan-story__profession">Basket weaver with mobility impairment</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>



<!-- How It Works Section -->
<section class="mp-home-how-it-works">
    <div class="container">
        <h2 class="mp-home-section-title">How It Works</h2>
        <div class="mp-home-how-it-works__grid">
            <div class="mp-home-how-it-works__item">
                <div class="mp-home-how-it-works__icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 class="mp-home-how-it-works__title">Shop Products</h3>
                <p class="mp-home-how-it-works__description">Browse and purchase unique handcrafted products made by artisans with disabilities</p>
            </div>
            <div class="mp-home-how-it-works__item">
                <div class="mp-home-how-it-works__icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3 class="mp-home-how-it-works__title">Fast Delivery</h3>
                <p class="mp-home-how-it-works__description">We deliver your purchases directly to your doorstep anywhere in Sri Lanka</p>
            </div>
            <div class="mp-home-how-it-works__item">
                <div class="mp-home-how-it-works__icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h3 class="mp-home-how-it-works__title">Support Artisans</h3>
                <p class="mp-home-how-it-works__description">Your purchase directly supports artisans with disabilities, helping them achieve financial independence</p>
            </div>
        </div>
    </div>
</section>


<!-- Impact Statistics Section -->
 <!--<section class="mp-home-impact">
    <div class="container">
        <h2 class="mp-home-section-title">Our Impact</h2>
        <div class="mp-home-impact__grid">
            <div class="mp-home-impact-stat">
                <span class="mp-home-impact-stat__number">25+</span>
                <h3 class="mp-home-impact-stat__title">Artisans Supported</h3>
                <p class="mp-home-impact-stat__description">Across Sri Lanka</p>
            </div>
            <div class="mp-home-impact-stat">
                <span class="mp-home-impact-stat__number">500+</span>
                <h3 class="mp-home-impact-stat__title">Products Sold</h3>
                <p class="mp-home-impact-stat__description">Handcrafted with care</p>
            </div>
            <div class="mp-home-impact-stat">
                <span class="mp-home-impact-stat__number">Rs 1M+</span>
                <h3 class="mp-home-impact-stat__title">Income Generated</h3>
                <p class="mp-home-impact-stat__description">For artisans and their families</p>
            </div>
            <div class="mp-home-impact-stat">
                <span class="mp-home-impact-stat__number">100%</span>
                <h3 class="mp-home-impact-stat__title">Fair Trade</h3>
                <p class="mp-home-impact-stat__description">Ethical practices guaranteed</p>
            </div>
        </div>
    </div>
</section>-->

<!-- Impact Statistics Section -->
<section class="mp-home-impact">
    <div class="container">
        <h2 class="mp-home-section-title">Our Impact</h2>
        <div class="mp-home-impact__grid">
            <div class="mp-home-impact-stat">
                <span class="mp-home-impact-stat__number"><?php echo $data['totalArtisans'] ?? '200'; ?>+</span>
                <h3 class="mp-home-impact-stat__title">Artisans Supported</h3>
                <p class="mp-home-impact-stat__description">Across Sri Lanka</p>
            </div>
            <div class="mp-home-impact-stat">
                <span class="mp-home-impact-stat__number"><?php echo $data['totalProducts'] ?? '500'; ?>+</span>
                <h3 class="mp-home-impact-stat__title">Products Available</h3>
                <p class="mp-home-impact-stat__description">Handcrafted with care</p>
            </div>
            <div class="mp-home-impact-stat">
                <span class="mp-home-impact-stat__number">Rs <?php echo number_format($data['totalRevenue'] ?? 1000000); ?>+</span>
                <h3 class="mp-home-impact-stat__title">Income Generated</h3>
                <p class="mp-home-impact-stat__description">For artisans and their families</p>
            </div>
            <div class="mp-home-impact-stat">
                <span class="mp-home-impact-stat__number">100%</span>
                <h3 class="mp-home-impact-stat__title">Fair Trade</h3>
                <p class="mp-home-impact-stat__description">Ethical practices guaranteed</p>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80, // Adjust for header height
                        behavior: 'smooth'
                    });
                }
            });
        });

    });


    const readMoreButtons = document.querySelectorAll('.mp-home-artisan-story__read-more');
    
    readMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const quoteWrapper = this.previousElementSibling;
            quoteWrapper.classList.toggle('expanded');
            
            if (quoteWrapper.classList.contains('expanded')) {
                this.innerHTML = 'Read Less <i class="fas fa-chevron-up"></i>';
            } else {
                this.innerHTML = 'Read More <i class="fas fa-chevron-down"></i>';
            }
        });
    });

</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>