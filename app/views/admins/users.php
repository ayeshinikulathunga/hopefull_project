<?php require APPROOT . '/views/includes/headers/admin_header.php'; ?>

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
                <?php unset($_SESSION['admin_error']);  ?>
            </div>
        <?php endif; ?>
                    
        <a href="add_user.php" class="add-user-button">Add User</a>
        <center><h1>List of Users</h1></center>       
        <!-- Users table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>User Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['users'] as $user): ?>
                                <tr>
                                    <td><?php echo $user->UserID; ?></td>
                                    <td><?php echo $user->Email; ?></td>
                                    <td><?php echo $user->Username; ?></td>
                                    <td><?php echo $user->UserType; ?></td>
                                    <td class="<?php 
    echo $user->UserStatus == 'Active' ? 'badge-success' : 
        ($user->UserStatus == 'Banned' ? 'badge-danger' : 'badge-warning'); 
?>">
    <?php echo $user->UserStatus ?? 'Active'; ?>
</td>
                                    <td>
                                        <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info edit-user-btn" 
    data-id="<?php echo $user->UserID; ?>" 
    data-email="<?php echo $user->Email; ?>" 
    data-username="<?php echo $user->Username; ?>" 
    data-usertype="<?php echo $user->UserType; ?>" 
    data-userstatus="<?php echo $user->UserStatus ?? 'Active'; ?>"
    onclick="window.location.href='<?php echo URLROOT; ?>/admins/editUser/<?php echo $user->UserID; ?>'">
    Edit
</button>


                                            <?php if(($user->UserStatus ?? 'Active') == 'Active'): ?>
                                                <form action="<?php echo URLROOT; ?>/admins/update_user_status" method="POST" class="d-inline">
                                                    <input type="hidden" name="user_id" value="<?php echo $user->UserID; ?>">
                                                    <input type="hidden" name="status" value="Banned">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to ban this user?')">
                                                        Ban
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form action="<?php echo URLROOT; ?>/admins/update_user_status" method="POST" class="d-inline">
                                                    <input type="hidden" name="user_id" value="<?php echo $user->UserID; ?>">
                                                    <input type="hidden" name="status" value="Active">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to activate this user?')">
                                                        Unban
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <form action="<?php echo URLROOT; ?>/admins/delete_user" method="POST" class="d-inline" onsubmit="return confirmDelete()">
                                                <input type="hidden" name="user_id" value="<?php echo $user->UserID; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger delete-user-btn">
                                                    Delete
                                                </button>
                                            </form>
                                            
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this user?');
    }
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>
