<?php require APPROOT . '/views/includes/headers/header.php'; ?>

<div class="auth-container">

    <div class="auth-card">
        <h2>Login to Hopefull</h2>
        <p class="auth-subtitle">Welcome back! Please login to your account.</p>

        <?php flash('login_error'); ?>

        <?php if(isset($data['errors']['login'])): ?>
            <div class="alert alert-danger"><?php echo $data['errors']['login']; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo URLROOT; ?>/users/login" method="POST" class="auth-form">
          
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="form-group remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
            </div>
            <input type="hidden" name="debug" value="1">
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="<?php echo URLROOT; ?>/users/register">Register</a></p>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>