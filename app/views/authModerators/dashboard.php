<?php require APPROOT . '/views/includes/headers/moderator_header.php'; ?>

<div class="dashboard-container">
    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="card-content">
                <h3><?php echo $data['stats']->pendingRecipients; ?></h3>
                <p>Pending Recipients</p>
            </div>
            <?php if($data['stats']->pendingRecipients > 0): ?>
                <a href="<?php echo URLROOT; ?>/authModerators/manageRecipients" class="card-action">Verify Now</a>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="card-content">
                <h3><?php echo $data['stats']->pendingRequests; ?></h3>
                <p>Pending Requests</p>
            </div>
            <?php if($data['stats']->pendingRequests > 0): ?>
                <a href="<?php echo URLROOT; ?>/authModerators/manageRequests" class="card-action">Verify Now</a>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-content">
                <h3><?php echo $data['stats']->approvedRecipients + $data['stats']->approvedRequests; ?></h3>
                <p>Total Approved</p>
            </div>
        </div>
        
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="card-content">
                <h3><?php echo $data['moderator']->VerificationCount; ?></h3>
                <p>Your Verifications</p>
            </div>
        </div>
    </div>
    
    <!-- Charts & Tables Row -->
    <div class="dashboard-grid">
        <!-- Analytics Chart -->
        <div class="dashboard-card">
            <h3>Verification Analytics</h3>
            <div class="chart-container">
                <canvas id="verificationChart"></canvas>
            </div>
        </div>
        
        <!-- Pending Recipients Table -->
        <div class="dashboard-card">
            <h3>Pending Recipients</h3>
            <?php if(empty($data['pendingRecipients'])): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No pending recipients to verify</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Organization</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(array_slice($data['pendingRecipients'], 0, 5) as $recipient): ?>
                                <tr>
                                    <td><?php echo $recipient->FirstName . ' ' . $recipient->LastName; ?></td>
                                    <td><?php echo $recipient->OrganizationType; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($recipient->RegisteredDate)); ?></td>
                                    <td>
                                        <a href="<?php echo URLROOT; ?>/authModerators/viewRecipient/<?php echo $recipient->RecipientID; ?>" class="btn-sm btn-outline">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if(count($data['pendingRecipients']) > 5): ?>
                        <div class="see-all">
                            <a href="<?php echo URLROOT; ?>/authModerators/manageRecipients">See All</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Activity Row -->
    <div class="dashboard-card">
        <h3>Recently Verified</h3>
        <?php if(empty($data['recentlyVerified'])): ?>
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <p>No recent verifications yet</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Verified By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['recentlyVerified'] as $recipient): ?>
                            <tr>
                                <td><?php echo $recipient->FirstName . ' ' . $recipient->LastName; ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($recipient->VerificationStatus); ?>">
                                        <?php echo $recipient->VerificationStatus; ?>
                                    </span>
                                </td>
                                <td><?php echo $recipient->ModeratorFirstName . ' ' . $recipient->ModeratorLastName; ?></td>
                                <td><?php echo date('M d, Y', strtotime($recipient->ApprovalDate)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
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
    
    // Chart.js implementation
    const ctx = document.getElementById('verificationChart').getContext('2d');
    const verificationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Recipients', 'Requests'],
            datasets: [
                {
                    label: 'Pending',
                    data: [
                        <?php echo $data['stats']->pendingRecipients; ?>, 
                        <?php echo $data['stats']->pendingRequests; ?>
                    ],
                    backgroundColor: 'rgba(255, 159, 64, 0.7)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Approved',
                    data: [
                        <?php echo $data['stats']->approvedRecipients; ?>, 
                        <?php echo $data['stats']->approvedRequests; ?>
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Rejected',
                    data: [
                        <?php echo $data['stats']->rejectedRecipients; ?>, 
                        <?php echo $data['stats']->rejectedRequests; ?>
                    ],
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<!-- Add some additional CSS for dashboard-specific elements -->
<style>
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.card-icon {
    background-color: var(--admin-primary);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: 15px;
}

.card-content h3 {
    font-size: 1.75rem;
    margin: 0;
    color: var(--admin-text-dark);
}

.card-content p {
    margin: 5px 0 0 0;
    color: var(--admin-text-light);
}

.card-action {
    display: inline-block;
    margin-top: 10px;
    color: var(--admin-primary);
    text-decoration: none;
    font-weight: 500;
}

.card-action:hover {
    text-decoration: underline;
}

.chart-container {
    height: 300px;
}

.status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
}

.status-badge.approved {
    background-color: rgba(75, 192, 192, 0.2);
    color: rgba(75, 192, 192, 1);
}

.status-badge.rejected {
    background-color: rgba(255, 99, 132, 0.2);
    color: rgba(255, 99, 132, 1);
}

.status-badge.pending {
    background-color: rgba(255, 159, 64, 0.2);
    color: rgba(255, 159, 64, 1);
}

.empty-state {
    text-align: center;
    padding: 30px 0;
}

.empty-state i {
    font-size: 2rem;
    color: var(--admin-text-light);
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--admin-text-light);
}

.see-all {
    text-align: right;
    padding-top: 10px;
}

.see-all a {
    color: var(--admin-primary);
    text-decoration: none;
}

.see-all a:hover {
    text-decoration: underline;
}

.btn-sm {
    padding: 4px 8px;
    font