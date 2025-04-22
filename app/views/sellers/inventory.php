<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-inventory">
    <?php flash('inventory_message'); ?>
    
    <div class="inventory-summary">
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="summary-details">
                <h3>Total Products</h3>
                <p class="summary-value"><?php echo $data['inventory']->totalProducts; ?></p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="summary-details">
                <h3>Available Products</h3>
                <p class="summary-value"><?php echo $data['inventory']->availableProducts; ?></p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="summary-details">
                <h3>Out of Stock</h3>
                <p class="summary-value"><?php echo $data['inventory']->outOfStockProducts; ?></p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <div class="summary-details">
                <h3>Total Stock</h3>
                <p class="summary-value"><?php echo $data['inventory']->totalStock; ?> units</p>
            </div>
        </div>
    </div>
    
    <div class="inventory-grid">
        <!-- Top Selling Products -->
        <div class="inventory-card">
            <h3><i class="fas fa-crown"></i> Top Selling Products</h3>
            <?php if(empty($data['top_products'])): ?>
                <p class="no-data">No sales data available yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Sold</th>
                                <th>Current Stock</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['top_products'] as $product): ?>
                                <tr>
                                    <td><?php echo $product->ProductName; ?></td>
                                    <td><?php echo $product->totalSold; ?> units</td>
                                    <td><?php echo $product->StockQuantity; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($product->Status); ?>">
                                            <?php echo $product->Status == 'Available' ? 'In Stock' : 'Out of Stock'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-sm btn-secondary update-stock-btn" data-product-id="<?php echo $product->ProductID; ?>" data-current-stock="<?php echo $product->StockQuantity; ?>" data-product-name="<?php echo $product->ProductName; ?>">
                                            Update Stock
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Low Stock Products -->
        <div class="inventory-card">
            <h3><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h3>
            <?php if(empty($data['low_stock'])): ?>
                <p class="no-data">No low stock products found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['low_stock'] as $product): ?>
                                <tr>
                                    <td><?php echo $product->ProductName; ?></td>
                                    <td><span class="stock-badge stock-low"><?php echo $product->StockQuantity; ?> left</span></td>
                                    <td><?php echo $product->Category; ?></td>
                                    <td>
                                        <button class="btn-sm btn-warning update-stock-btn" data-product-id="<?php echo $product->ProductID; ?>" data-current-stock="<?php echo $product->StockQuantity; ?>" data-product-name="<?php echo $product->ProductName; ?>">
                                            Restock
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- All Products Inventory -->
        <div class="inventory-card full-width">
            <h3><i class="fas fa-list"></i> Complete Inventory</h3>
            <div class="filters">
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
                    <select id="stockFilter" class="filter-select">
                        <option value="">All Stock Levels</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                        <option value="available">In Stock</option>
                    </select>
                </div>
            </div>
            
            <?php if(empty($data['products'])): ?>
                <p class="no-data">No products found in inventory.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['products'] as $product): ?>
                                <tr class="inventory-row" data-category="<?php echo $product->Category; ?>" data-stock="<?php echo $product->StockQuantity; ?>" data-status="<?php echo $product->Status; ?>">
                                    <td><?php echo $product->ProductID; ?></td>
                                    <td><?php echo $product->ProductName; ?></td>
                                    <td><?php echo $product->Category; ?></td>
                                    <td>Rs. <?php echo number_format($product->Price, 2); ?></td>
                                    <td>
                                        <?php if($product->StockQuantity <= 5 && $product->StockQuantity > 0): ?>
                                            <span class="stock-badge stock-low"><?php echo $product->StockQuantity; ?></span>
                                        <?php elseif($product->StockQuantity <= 0): ?>
                                            <span class="stock-badge stock-out"><?php echo $product->StockQuantity; ?></span>
                                        <?php else: ?>
                                            <span class="stock-badge stock-normal"><?php echo $product->StockQuantity; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($product->Status); ?>">
                                            <?php echo $product->Status == 'Available' ? 'In Stock' : 'Out of Stock'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($product->LastUpdated)); ?></td>
                                    <td class="actions">
                                        <button class="btn-sm btn-secondary update-stock-btn" data-product-id="<?php echo $product->ProductID; ?>" data-current-stock="<?php echo $product->StockQuantity; ?>" data-product-name="<?php echo $product->ProductName; ?>">
                                            <i class="fas fa-sync-alt"></i> Update
                                        </button>
                                        <a href="<?php echo URLROOT; ?>/sellers/editProduct/<?php echo $product->ProductID; ?>" class="btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Update Stock Modal -->
    <div id="updateStockModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Stock</h2>
            <form id="updateStockForm" action="<?php echo URLROOT; ?>/sellers/updateInventory" method="POST">
                <input type="hidden" id="productId" name="product_id">
                <div class="form-group">
                    <label for="productName">Product:</label>
                    <input type="text" id="productName" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="currentStock">Current Stock:</label>
                    <input type="text" id="currentStock" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="newStock">New Stock Quantity:</label>
                    <input type="number" id="newStock" name="stock_quantity" class="form-control" min="0" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Stock</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Product search and filter functionality
    const productSearch = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const stockFilter = document.getElementById('stockFilter');
    const inventoryRows = document.querySelectorAll('.inventory-row');
    
    function filterInventory() {
        const searchTerm = productSearch.value.toLowerCase();
        const categoryValue = categoryFilter.value;
        const stockValue = stockFilter.value;
        
        inventoryRows.forEach(row => {
            const productName = row.querySelector('td:nth-child(2)').innerText.toLowerCase();
            const productId = row.querySelector('td:nth-child(1)').innerText.toLowerCase();
            const category = row.getAttribute('data-category');
            const stock = parseInt(row.getAttribute('data-stock'));
            const status = row.getAttribute('data-status');
            
            // Search match
            const matchesSearch = productName.includes(searchTerm) || productId.includes(searchTerm);
            
            // Category match
            const matchesCategory = categoryValue === '' || category === categoryValue;
            
            // Stock level match
            let matchesStock = true;
            if (stockValue === 'low') {
                matchesStock = stock > 0 && stock <= 5;
            } else if (stockValue === 'out') {
                matchesStock = stock <= 0 || status === 'OutOfStock';
            } else if (stockValue === 'available') {
                matchesStock = stock > 0 && status === 'Available';
            }
            
            if (matchesSearch && matchesCategory && matchesStock) {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    productSearch.addEventListener('input', filterInventory);
    categoryFilter.addEventListener('change', filterInventory);
    stockFilter.addEventListener('change', filterInventory);
    
    // Modal functionality for Update Stock
    const updateStockModal = document.getElementById('updateStockModal');
    const updateStockBtns = document.querySelectorAll('.update-stock-btn');
    const updateStockCloseBtn = updateStockModal.querySelector('.close');
    
    updateStockBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const currentStock = this.getAttribute('data-current-stock');
            
            document.getElementById('productId').value = productId;
            document.getElementById('productName').value = productName;
            document.getElementById('currentStock').value = currentStock;
            document.getElementById('newStock').value = currentStock;
            
            updateStockModal.style.display = 'block';
        });
    });
    
    updateStockCloseBtn.addEventListener('click', function() {
        updateStockModal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target == updateStockModal) {
            updateStockModal.style.display = 'none';
        }
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>