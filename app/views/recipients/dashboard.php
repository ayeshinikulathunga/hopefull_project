<?php require APPROOT . '/views/includes/headers/recipient_header.php'; ?>

<!-- Hero Section -->


<!-- Hero Section -->
<div class="dashboard-hero" style="background-image: url('<?php echo URLROOT; ?>/images/recipient-hero-bg.jpg');">
    <div class="hero-overlay"></div>
    <div class="hero-content container">
        <h1>Welcome, <?php echo $data['recipient']->FirstName; ?></h1>
        <p>Your dashboard for monitoring donation requests and impact</p>
        <a href="<?php echo URLROOT; ?>/recipients/createRequest" class="btn btn-primary">New Request</a>
    </div>
</div>

<div class="container">
    <div class="dashboard-content">
        <!-- Analytics Section -->
        <div class="analytics-section">
            <div class="section-header">
                <h2>Request Analytics</h2>
                <select class="time-filter">
                    <option value="week">This Week</option>
                    <option value="month" selected>This Month</option>
                    <option value="year">This Year</option>
                </select>
            </div>
            
            <div class="analytics-cards">
                <div class="analytics-card">
                    <div class="analytics-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="analytics-info">
                        <h3><?php echo count($data['requests']); ?></h3>
                        <p>Total Requests</p>
                    </div>
                </div>
                
                <div class="analytics-card">
                    <div class="analytics-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="analytics-info">
                        <h3><?php 
                            $completed = 0;
                            foreach($data['requests'] as $request) {
                                if($request->RequestStatus == 'Completed') $completed++;
                            }
                            echo $completed;
                        ?></h3>
                        <p>Completed</p>
                    </div>
                </div>
                
                <div class="analytics-card">
                    <div class="analytics-icon">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div class="analytics-info">
                        <h3><?php 
                            $inProgress = 0;
                            foreach($data['requests'] as $request) {
                                if($request->RequestStatus == 'InProgress') $inProgress++;
                            }
                            echo $inProgress;
                        ?></h3>
                        <p>In Progress</p>
                    </div>
                </div>
                
                <div class="analytics-card">
                    <div class="analytics-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="analytics-info">
                        <h3>0</h3>
                        <p>Donors</p>
                    </div>
                </div>
            </div>
            
            <div class="analytics-graph">
                <canvas id="requestsChart"></canvas>
            </div>
        </div>
        
        <!-- Requests Table Section -->
        <div class="requests-section">
            <div class="section-header">
                <h2>Your Donation Requests</h2>
                <a href="<?php echo URLROOT; ?>/recipients/createRequest" class="btn btn-sm btn-primary">New Request</a>
            </div>
            
            <?php if(empty($data['requests'])) : ?>
                <div class="empty-state">
                    <img src="<?php echo URLROOT; ?>/images/empty-state.svg" alt="No requests" class="empty-state-img">
                    <h3>No Requests Yet</h3>
                    <p>You haven't created any donation requests yet.</p>
                    <a href="<?php echo URLROOT; ?>/recipients/createRequest" class="btn btn-primary">Create Your First Request</a>
                </div>
            <?php else : ?>
                <div class="table-responsive">
                <table class="requests-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Category</th>
            <th>Type</th>
            <th>Status</th>
            <th>Deadline</th>
            <th>Progress</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data['requests'] as $request) : ?>
            <tr>
                <td data-label="Image" class="request-image-cell">
                    <img src="<?php echo URLROOT; ?>/uploads/requests/<?php echo $request->RequestID; ?>.jpg" 
                         alt="<?php echo $request->Title; ?>" class="request-img">
                </td>
                <td data-label="Title">
                    <div class="request-title-info">
                        <h4><?php echo $request->Title; ?></h4>
                        <span class="request-date">Created: <?php echo date('M d, Y', strtotime($request->CreatedDate)); ?></span>
                    </div>
                </td>
                <td data-label="Category"><?php echo $request->Category; ?></td>
                <td data-label="Type"><?php echo $request->RequestType; ?></td>
                <td data-label="Status">
                    <span class="status-badge <?php echo strtolower($request->RequestStatus); ?>">
                        <?php echo $request->RequestStatus; ?>
                    </span>
                </td>
                <td data-label="Deadline"><?php echo date('M d, Y', strtotime($request->Deadline)); ?></td>
                <td data-label="Progress">
                    <div class="progress-bar">
                        <?php 
                            // This is a placeholder - you'll need to calculate actual progress
                            $progress = 45; 
                            if($request->RequestStatus == 'Completed') $progress = 100;
                            else if($request->RequestStatus == 'Expired') $progress = 0;
                        ?>
                        <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                    <span class="progress-text"><?php echo $progress; ?>%</span>
                </td>
                <td data-label="Actions">
                    <div class="actions-cell">
                        <a href="<?php echo URLROOT; ?>/requests/view/<?php echo $request->RequestID; ?>" class="btn btn-sm btn-outline" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?php echo URLROOT; ?>/requests/edit/<?php echo $request->RequestID; ?>" class="btn btn-sm btn-outline" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sample data for chart - replace with actual data
    const ctx = document.getElementById('requestsChart').getContext('2d');
    const requestsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Requests Created',
                data: [3, 5, 2, 4, 6, 8],
                backgroundColor: 'rgba(92, 126, 221, 0.2)',
                borderColor: 'rgba(92, 126, 221, 1)',
                borderWidth: 2,
                tension: 0.3
            }, {
                label: 'Requests Completed',
                data: [1, 3, 1, 2, 3, 5],
                backgroundColor: 'rgba(76, 175, 80, 0.2)',
                borderColor: 'rgba(76, 175, 80, 1)',
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
});
</script>



<?php require APPROOT . '/views/includes/footer.php'; ?>