<?php require APPROOT . '/views/includes/headers/admin_header.php'; ?>

<div class="dashboard-grid">
    <div class="dashboard-card total-users">
        <div class="card-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="card-content">
            <h3>Total Users</h3>
            <?php 
            $totalUsers = 0;
            foreach($data['user_counts'] as $count) {
                $totalUsers += $count->count;
            }
            ?>
            <p><?php echo $totalUsers; ?></p>
        </div>
    </div>

    <div class="dashboard-card user-types">
        <h3>User Breakdown</h3>
        <div class="user-type-list">
            <?php foreach($data['user_counts'] as $userType): ?>
                <div class="user-type-item">
                    <span class="type-name"><?php echo $userType->UserType; ?></span>
                    <span class="type-count"><?php echo $userType->count; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="dashboard-card recent-users">
        <h3>Recent Users</h3>
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Type</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['recent_users'] as $user): ?>
                <tr>
                    <td><?php echo $user->Email; ?></td>
                    <td><?php echo $user->Username; ?></td>
                    <td><?php echo $user->UserType; ?></td>
                    <td><?php echo date('M d, Y', strtotime($user->RegisteredDate)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="dashboard-card verification-requests">
        <h3>Pending Verifications</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['pending_verifications'] as $request): ?>
                <tr>
                    <td><?php echo $request->FirstName . ' ' . $request->LastName; ?></td>
                    <td><?php echo $request->OrganizationType; ?></td>
                    <td><?php echo $request->Email; ?></td>
                    <td>
                    <a href="<?php echo URLROOT; ?>/admins/verifications" class="btn btn-sm btn-success">
                                            <i class="fas fa-eye"></i>
                                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>