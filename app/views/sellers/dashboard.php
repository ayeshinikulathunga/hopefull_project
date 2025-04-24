<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-dashboard">
    <!-- Statistics Cards -->
    <div class="stats-card-container">
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stats-info">
                <h3>Total Products</h3>
                <p class="stats-number"><?php echo isset($data['inventory']->totalProducts) ? $data['inventory']->totalProducts : '0'; ?></p>
                <p class="stats-period">In your inventory</p>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stats-info">
                <h3>Total Revenue</h3>
                <p class="stats-number">Rs. <?php echo number_format(isset($data['revenue']) ? $data['revenue'] : 0, 2); ?></p>
                <p class="stats-period">All time</p>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stats-info">
                <h3>Total Orders</h3>
                <p class="stats-number"><?php echo isset($data['orders_count']) ? $data['orders_count'] : '0'; ?></p>
                <p class="stats-period">All time</p>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <div class="stats-info">
                <h3>Stock Status</h3>
                <p class="stats-number"><?php echo isset($data['inventory']->totalStock) ? $data['inventory']->totalStock : '0'; ?> items</p>
                <p class="stats-period"><?php echo isset($data['inventory']->outOfStockProducts) ? $data['inventory']->outOfStockProducts : '0'; ?> out of stock</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <!-- Recent Orders -->
        <div class="dashboard-card">
            <h3><i class="fas fa-clipboard-list"></i> Recent Orders</h3>
            <?php if(empty($data['recent_orders'])): ?>
                <p class="no-data">No recent orders found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['recent_orders'] as $order): ?>
                                <tr>
                                    <td><?php echo $order->OrderID; ?></td>
                                    <td><?php echo $order->Username; ?></td>
                                    <td>Rs. <?php echo number_format($order->TotalAmount, 2); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($order->OrderDate)); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($order->Status); ?>"><?php echo $order->Status; ?></span></td>
                                    <td>
                                        <a href="<?php echo URLROOT; ?>/sellers/orderDetails/<?php echo $order->OrderID; ?>" class="btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Low Stock Products -->
        <div class="dashboard-card">
            <h3><i class="fas fa-exclamation-triangle"></i> Low Stock Products</h3>
            <?php if(empty($data['low_stock'])): ?>
                <p class="no-data">No low stock products found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['low_stock'] as $product): ?>
                                <tr>
                                    <td><?php echo $product->ProductName; ?></td>
                                    <td><span class="stock-badge stock-low"><?php echo $product->StockQuantity; ?> left</span></td>
                                    <td>
                                        <a href="<?php echo URLROOT; ?>/sellers/editProduct/<?php echo $product->ProductID; ?>" class="btn-sm btn-secondary">Update Stock</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Products List -->
        <div class="dashboard-card full-width">
            <h3><i class="fas fa-list"></i> Your Products</h3>
            
            <?php if(empty($data['products'])): ?>
                <p class="no-data">No products found.</p>
                <div class="text-center mt-4">
                    <a href="<?php echo URLROOT; ?>/sellers/addProduct" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Your First Product
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['products'] as $product): ?>
                                <tr>
                                    <td><?php echo $product->ProductID; ?></td>
                                    <td><?php echo $product->ProductName; ?></td>
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
                                    <td class="actions">
                                        <a href="<?php echo URLROOT; ?>/sellers/editProduct/<?php echo $product->ProductID; ?>" class="btn-sm btn-secondary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/marketplace/product/<?php echo $product->ProductID; ?>" class="btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?php echo URLROOT; ?>/sellers/products" class="btn btn-primary">
                        <i class="fas fa-list"></i> View All Products
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>