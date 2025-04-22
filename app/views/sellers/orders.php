<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-orders">
    <?php flash('order_message'); ?>
    
    <div class="orders-header">
        <div class="filters">
            <div class="search-container">
                <input type="text" id="orderSearch" placeholder="Search by Order ID or Customer..." class="search-input">
                <i class="fas fa-search"></i>
            </div>
            <div class="filter-container">
                <select id="statusFilter" class="filter-select">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <select id="dateFilter" class="filter-select">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="3months">Last 3 Months</option>
                </select>
            </div>
        </div>
    </div>
    
    <?php if(empty($data['orders'])): ?>
        <div class="no-orders">
            <div class="no-data-message">
                <i class="fas fa-shopping-cart"></i>
                <h3>No Orders Found</h3>
                <p>You don't have any orders yet. Orders will appear here when customers purchase your products.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['orders'] as $order): ?>
                        <tr class="order-row" data-order-id="<?php echo $order->OrderID; ?>" data-customer="<?php echo $order->Username; ?>" data-status="<?php echo $order->Status; ?>" data-date="<?php echo strtotime($order->OrderDate); ?>">
                            <td><?php echo $order->OrderID; ?></td>
                            <td>
                                <div class="customer-info">
                                    <span class="customer-name"><?php echo $order->Username; ?></span>
                                    <span class="customer-email"><?php echo $order->Email; ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="order-date">
                                    <span class="date"><?php echo date('M j, Y', strtotime($order->OrderDate)); ?></span>
                                    <span class="time"><?php echo date('g:i A', strtotime($order->OrderDate)); ?></span>
                                </div>
                            </td>
                            <td class="order-amount">Rs. <?php echo number_format($order->TotalAmount, 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($order->Status); ?>"><?php echo $order->Status; ?></span>
                            </td>
                            <td class="actions">
                                <a href="<?php echo URLROOT; ?>/sellers/orderDetails/<?php echo $order->OrderID; ?>" class="btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Details
                                </a>
                                <?php if($order->Status == 'Pending'): ?>
                                    <button class="btn-sm btn-secondary update-status-btn" data-order-id="<?php echo $order->OrderID; ?>" data-current-status="<?php echo $order->Status; ?>">
                                        <i class="fas fa-cog"></i> Update
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <!-- Update Status Modal -->
    <div id="updateStatusModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Order Status</h2>
            <form id="updateStatusForm" action="<?php echo URLROOT; ?>/sellers/updateOrderStatus" method="POST">
                <input type="hidden" id="orderIdInput" name="order_id">
                
                <div class="form-group">
                    <label for="orderIdDisplay">Order ID:</label>
                    <input type="text" id="orderIdDisplay" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label for="currentStatus">Current Status:</label>
                    <input type="text" id="currentStatus" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label for="newStatus">New Status:</label>
                    <select id="newStatus" name="status" class="form-control" required>
                        <option value="">Select New Status</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary cancel-update">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Order search and filter functionality
    const orderSearch = document.getElementById('orderSearch');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const orderRows = document.querySelectorAll('.order-row');
    
    function filterOrders() {
        const searchTerm = orderSearch.value.toLowerCase();
        const statusValue = statusFilter.value;
        const dateValue = dateFilter.value;
        const currentDate = new Date();
        
        let startDate = new Date(0); // Beginning of time
        
        // Set start date based on filter
        if (dateValue) {
            if (dateValue === 'today') {
                startDate = new Date(currentDate.setHours(0, 0, 0, 0));
            } else if (dateValue === 'week') {
                const day = currentDate.getDay();
                const diff = currentDate.getDate() - day + (day === 0 ? -6 : 1); // Adjust to Monday
                startDate = new Date(currentDate.setDate(diff));
                startDate.setHours(0, 0, 0, 0);
            } else if (dateValue === 'month') {
                startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            } else if (dateValue === '3months') {
                startDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 3, currentDate.getDate());
            }
        }
        
        orderRows.forEach(row => {
            const orderId = row.getAttribute('data-order-id').toLowerCase();
            const customer = row.getAttribute('data-customer').toLowerCase();
            const status = row.getAttribute('data-status');
            const orderDateTimestamp = parseInt(row.getAttribute('data-date')) * 1000; // Convert to milliseconds
            const orderDate = new Date(orderDateTimestamp);
            
            const matchesSearch = orderId.includes(searchTerm) || customer.includes(searchTerm);
            const matchesStatus = statusValue === '' || status === statusValue;
            const matchesDate = orderDate >= startDate;
            
            if (matchesSearch && matchesStatus && matchesDate) {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    orderSearch.addEventListener('input', filterOrders);
    statusFilter.addEventListener('change', filterOrders);
    dateFilter.addEventListener('change', filterOrders);
    
    // Update status modal functionality
    const updateStatusModal = document.getElementById('updateStatusModal');
    const updateStatusBtns = document.querySelectorAll('.update-status-btn');
    const updateStatusCloseBtn = updateStatusModal.querySelector('.close');
    const cancelUpdateBtn = updateStatusModal.querySelector('.cancel-update');
    
    updateStatusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const currentStatus = this.getAttribute('data-current-status');
            
            document.getElementById('orderIdInput').value = orderId;
            document.getElementById('orderIdDisplay').value = orderId;
            document.getElementById('currentStatus').value = currentStatus;
            document.getElementById('newStatus').value = '';
            
            updateStatusModal.style.display = 'block';
        });
    });
    
    updateStatusCloseBtn.addEventListener('click', function() {
        updateStatusModal.style.display = 'none';
    });
    
    cancelUpdateBtn.addEventListener('click', function() {
        updateStatusModal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target == updateStatusModal) {
            updateStatusModal.style.display = 'none';
        }
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>