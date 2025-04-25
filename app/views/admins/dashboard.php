<?php require APPROOT . '/views/includes/headers/admin_header.php'; ?>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="alert alert-success">
        <?php 
            echo $_SESSION['admin_success']; 
            unset($_SESSION['admin_success']); // Clear the message after displaying
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['admin_error'])): ?>
    <div class="alert alert-danger">
        <?php 
            echo $_SESSION['admin_error']; 
            unset($_SESSION['admin_error']); // Clear the message after displaying
        ?>
    </div>
<?php endif; ?>

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
        <table class="table table-striped">
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
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($data['pending_verifications'])): ?>
                    <?php foreach($data['pending_verifications'] as $request): ?>
                    <tr>
                        <td><?php echo $request->FirstName . ' ' . $request->LastName; ?></td>
                        <td><?php echo $request->OrganizationType; ?></td>
                        <td><?php echo $request->Email; ?></td>
                        <td>
                            <a href="<?php echo URLROOT; ?>/admins/verifications" class="btn btn-sm btn-primary">
                                Review
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No pending verification requests</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Inquiries Card - Improved Layout -->
    <div class="dashboard-card inquiries">
        <h3>Inquiries</h3>
        <?php if(isset($data['inquiries']) && !empty($data['inquiries'])): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['inquiries'] as $inquiry): ?>
                    <tr>
                        <td><?php echo $inquiry->Name; ?></td>
                        <td><?php echo $inquiry->Email; ?></td>
                        <td><?php echo $inquiry->Subject; ?></td>
                        <td><?php echo date('M d, Y', strtotime($inquiry->DateSubmitted)); ?></td>
                        <td><?php echo $inquiry->Status; ?></td>
                        <td>
                            
                            <form action="<?php echo URLROOT; ?>/admins/markInquiryReplied" method="POST" class="d-inline">
                                <input type="hidden" name="inquiry_id" value="<?php echo $inquiry->InquiryID; ?>">
                                <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Mark this inquiry as replied? This will remove it from the list.')">
                                    Mark as Replied
                                </button>
                            </form>
                        </td>
                    </tr>
                    
                    
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No pending inquiries</div>
        <?php endif; ?>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>