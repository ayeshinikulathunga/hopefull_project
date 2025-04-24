<?php require APPROOT . '/views/includes/headers/admin_header.php'; ?>

<div class="container mt-4">
    <h2>Edit User</h2>

    <?php if (isset($_SESSION['admin_error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo URLROOT; ?>/admins/editUser/<?php echo $data['user']->UserID; ?>">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo $data['user']->Email; ?>" required>
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $data['user']->Username; ?>" required>
        </div>

        <div class="form-group">
            <label>User Type</label>
            <select name="user_type" class="form-control">
                <?php 
                $types = ['Donor', 'Recipient', 'SystemAdmin', 'AuthModerator', 'RegionalOfficer', 'DeliveryOfficer'];
                foreach ($types as $type): ?>
                    <option value="<?php echo $type; ?>" <?php echo $data['user']->UserType == $type ? 'selected' : ''; ?>>
                        <?php echo $type; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>User Status</label>
            <select name="user_status" class="form-control">
                <option value="Active" <?php echo $data['user']->UserStatus == 'Active' ? 'selected' : ''; ?>>Active</option>
                <option value="Inactive" <?php echo $data['user']->UserStatus == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="<?php echo URLROOT; ?>/admins/users" class="btn btn-secondary">Cancel</a>
    </form>
</div>
