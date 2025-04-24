<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-products">
    <?php flash('product_message'); ?>
    
    <div class="products-header">
        <div class="actions">
            <a href="<?php echo URLROOT; ?>/sellers/addProduct" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>
        
        <div class="product-filters">
            <div class="search-container">
                <input type="text" id="productSearch" placeholder="Search products..." class="search-input">
                <i class="fas fa-search"></i>
            </div>
            <div class="filter-container">
                <select id="categoryFilter" class="filter-select">
                    <option value="">All Categories</option>
                    <option value="Handicrafts">Handicrafts</option>
                    <option value="Jewelry">Jewelry</option>
                    <option value="Textiles">Textiles</option>
                    <option value="Home">Home Decor</option>
                    <option value="Art">Art</option>
                </select>
                <select id="statusFilter" class="filter-select">
                    <option value="">All Status</option>
                    <option value="Available">Available</option>
                    <option value="OutOfStock">Out of Stock</option>
                </select>
            </div>
        </div>
    </div>
    
    <?php if(empty($data['products'])): ?>
        <div class="no-products">
            <div class="no-data-message">
                <i class="fas fa-box-open"></i>
                <h3>No Products Found</h3>
                <p>You haven't added any products yet. Click the button above to add your first product.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach($data['products'] as $product): ?>
                <div class="product-card" data-category="<?php echo $product->Category; ?>" data-status="<?php echo $product->Status; ?>">
                    <!-- In your products.php view file, update the image display section -->
<div class="product-image">
    <?php 
    // Check for image with pattern ProductID.*
    $productId = $product->ProductID;
    $imageFound = false;
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    foreach($imageExtensions as $ext) {
        $imagePath = "/uploads/products/{$productId}.{$ext}";
        $fullPath = ROOT_PATH . "/public" . $imagePath;
        
        if(file_exists($fullPath)) {
            $imageFound = true;
            ?>
            <img src="<?php echo URLROOT . $imagePath; ?>" alt="<?php echo $product->ProductName; ?>">
            <?php
            break;
        }
    }
    
    // If no image found with ProductID.extension pattern, check for old format
    if(!$imageFound && !empty($product->ProductImage)) {
        ?>
        <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $product->ProductImage; ?>" alt="<?php echo $product->ProductName; ?>">
        <?php
    } elseif(!$imageFound) {
        // No image found at all
        ?>
        <img src="<?php echo URLROOT; ?>/images/placeholder-product.jpg" alt="Product image placeholder">
        <?php
    }
    ?>
    
    <div class="product-status <?php echo strtolower($product->Status); ?>">
        <?php echo $product->Status == 'Available' ? 'In Stock' : 'Out of Stock'; ?>
    </div>
</div>
                    
                    <div class="product-details">
                        <h3 class="product-name"><?php echo $product->ProductName; ?></h3>
                        <p class="product-category"><?php echo $product->Category; ?></p>
                        <div class="product-price-stock">
                            <span class="product-price">Rs. <?php echo number_format($product->Price, 2); ?></span>
                            <span class="product-stock"><?php echo $product->StockQuantity; ?> in stock</span>
                        </div>
                        <div class="product-actions">
                            <a href="<?php echo URLROOT; ?>/sellers/editProduct/<?php echo $product->ProductID; ?>" class="btn-sm btn-secondary">Edit</a>
                            <button class="btn-sm btn-danger delete-product-btn" data-product-id="<?php echo $product->ProductID; ?>" data-product-name="<?php echo $product->ProductName; ?>">Delete</button>
<<<<<<< HEAD
                            <a href="<?php echo URLROOT; ?>/marketplace/product/<?php echo $product->ProductID; ?>" class="btn-sm btn-primary" target="_blank">View</a>
=======
                            <a href="<?php echo URLROOT; ?>/marketplace/product/<?php echo $product->ProductID; ?>" class="btn-sm btn-primary view-btn" target="_blank">View</a>
>>>>>>> cd92d372a120695a1d0602d06341b236d9fb1dd0
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Delete Product Modal -->
    <div id="deleteProductModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete the product <span id="deleteProductName"></span>?</p>
            <p class="text-danger">This action cannot be undone.</p>
            <form id="deleteProductForm" action="" method="POST">
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary cancel-delete">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Product search functionality
    const productSearch = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const productCards = document.querySelectorAll('.product-card');
    
    function filterProducts() {
        const searchTerm = productSearch.value.toLowerCase();
        const categoryValue = categoryFilter.value;
        const statusValue = statusFilter.value;
        
        productCards.forEach(card => {
            const productName = card.querySelector('.product-name').innerText.toLowerCase();
            const category = card.getAttribute('data-category');
            const status = card.getAttribute('data-status');
            
            const matchesSearch = productName.includes(searchTerm);
            const matchesCategory = categoryValue === '' || category === categoryValue;
            const matchesStatus = statusValue === '' || status === statusValue;
            
            if (matchesSearch && matchesCategory && matchesStatus) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    productSearch.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);
    statusFilter.addEventListener('change', filterProducts);
    
    // Delete product modal functionality
    const deleteProductModal = document.getElementById('deleteProductModal');
    const deleteProductBtns = document.querySelectorAll('.delete-product-btn');
    const deleteProductCloseBtn = deleteProductModal.querySelector('.close');
    const cancelDeleteBtn = deleteProductModal.querySelector('.cancel-delete');
    const deleteProductForm = document.getElementById('deleteProductForm');
    const deleteProductNameSpan = document.getElementById('deleteProductName');
    
    deleteProductBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            
            deleteProductForm.action = `<?php echo URLROOT; ?>/sellers/deleteProduct/${productId}`;
            deleteProductNameSpan.textContent = productName;
            
            deleteProductModal.style.display = 'block';
        });
    });
    
    deleteProductCloseBtn.addEventListener('click', function() {
        deleteProductModal.style.display = 'none';
    });
    
    cancelDeleteBtn.addEventListener('click', function() {
        deleteProductModal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target == deleteProductModal) {
            deleteProductModal.style.display = 'none';
        }
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>