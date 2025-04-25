<?php require APPROOT . '/views/includes/headers/admin_header.php'; ?>

<div class="admin-content">
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card-body">
                    <?php if (isset($_SESSION['admin_success'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $_SESSION['admin_success']; ?>
                            <?php unset($_SESSION['admin_success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['admin_error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i>
                            <?php echo $_SESSION['admin_error']; ?>
                            <?php unset($_SESSION['admin_error']); ?>
                        </div>
                    <?php endif; ?>

                    <center><h1>Add User</h1></center>

                    <a href="<?php echo URLROOT; ?>/admins/users" class="add-user-button">Back to User Management</a>

                    <form action="<?php echo URLROOT; ?>/admins/addUser" method="POST">
                        <div class="form-group">
                            <label for="Email">Email:</label>
                            <input type="email" id="Email" name="Email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="Username">Username:</label>
                            <input type="text" id="Username" name="Username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="Password">Password:</label>
                            <input type="password" id="Password" name="Password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="User Type">User  Type:</label>
                            <select id="user_type" name="user_type" class="form-control" required>
                                <option value="Donor">Donor</option>
                                <option value="Recipient">Recipient</option>
                                <option value="SystemAdmin">System Admin</option>
                                <option value="AuthModerator">Authentication Moderator</option>
                                <option value="RegionalOfficer">Regional Officer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="User Status">User  Status:</label>
                            <select id="user_status" name="user_status" class="form-control" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-sm btn-success">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
