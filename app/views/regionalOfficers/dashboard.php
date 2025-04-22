<?php require APPROOT . '/views/includes/headers/officer_header.php'; ?>

<div class="dashboard-container">
    <!-- Warehouse Info Card -->
    <div class="warehouse-info-card">
        <div class="card-header">
            <h2><i class="fas fa-warehouse"></i> Warehouse Information</h2>
        </div>
        <div class="warehouse-details">
            <div class="info-group">
                <label>Location:</label>
                <span><?php echo $data['officer']->WarehouseLocation; ?></span>
            </div>
            <div class="info-group">
                <label>Officer in Charge:</label>
                <span><?php echo $data['officer']->FirstName . ' ' . $data['officer']->LastName; ?></span>
            </div>
            <div class="info-group">
                <label>Contact:</label>
                <span><?php echo $data['officer']->ContactNumber; ?></span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="card-content">
                <h3><?php echo $data['stats']->totalItems; ?></h3>
                <p>Total Items</p>
            </div>
        </div>
        
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-cubes"></i>
            </div>
            <div class="card-content">
                <h3><?php echo $data['stats']->totalQuantity; ?></h3>
                <p>Total Quantity</p>
            </div>
        </div>
        
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-content">
                <h3><?php echo $data['stats']->availableQuantity; ?></h3>
                <p>Available Items</p>
            </div>
        </div>
        
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="card-content">
                <h3><?php echo count($data['pendingRequests']); ?></h3>
                <p>Pending Requests</p>
            </div>
            <?php if(count($data['pendingRequests']) > 0): ?>
                <a href="<?php echo URLROOT; ?>/regionalOfficers/requests" class="card-action">View Requests</a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Charts & Tables Row -->
    <div class="dashboard-grid">
        <!-- Inventory Chart -->
        <div class="dashboard-card">
            <h3>Inventory Status</h3>
            <div class="chart-container">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
        
        <!-- Recent Inventory Items -->
        <div class="dashboard-card">
            <div class="card-header-with-action">
                <h3>Recent Inventory Items</h3>
                <a href="<?php echo URLROOT; ?>/regionalOfficers/inventory" class="btn btn-sm btn-success">Manage Inventory</a>
            </div>
            
            <?php if(empty($data['inventoryItems'])): ?>
                <div class="empty-state">
                    <i class="fas fa-boxes"></i>
                    <p>No inventory items added yet</p>
                    <a href="<?php echo URLROOT; ?>/regionalOfficers/addItem" class="btn btn-sm btn-success">Add First Item</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(array_slice($data['inventoryItems'], 0, 5) as $item): ?>
                                <tr>
                                    <td><?php echo $item->ItemName; ?></td>
                                    <td><?php echo $item->Category; ?></td>
                                    <td><?php echo $item->Quantity; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($item->Status); ?>">
                                            <?php echo $item->Status; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if(count($data['inventoryItems']) > 5): ?>
                        <div class="see-all">
                            <a href="<?php echo URLROOT; ?>/regionalOfficers/inventory">See All Items</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Pending Requests -->
    <div class="dashboard-card">
        <div class="card-header-with-action">
            <h3>Pending Donation Requests</h3>
            <a href="<?php echo URLROOT; ?>/regionalOfficers/requests" class="btn btn-sm btn-success">View All</a>
        </div>
        
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar expansion on click
    const sidebar = document.getElementById('adminSidebar');
    sidebar.addEventListener('click', function() {
        sidebar.classList.toggle('expanded');
    });
    
    // Inventory status chart
    const ctx = document.getElementById('inventoryChart').getContext('2d');
    const inventoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Reserved', 'Distributed'],
            datasets: [{
                data: [
                    <?php echo $data['stats']->availableQuantity; ?>, 
                    <?php echo $data['stats']->reservedQuantity; ?>, 
                    <?php echo $data['stats']->distributedQuantity; ?>
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });
});
</script>



<?php require APPROOT . '/views/includes/footer.php'; ?>