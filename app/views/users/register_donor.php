<?php require APPROOT . '/views/includes/headers/header.php'; ?>

<div class="auth-container">
    <div class="auth-card donor-registration">
        <h2>Register as a Donor</h2>
        <p class="auth-subtitle">Join our community of donors making a difference</p>

        <form action="<?php echo URLROOT; ?>/users/register_donor" method="POST" class="auth-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" name="firstName" id="firstName" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" name="lastName" id="lastName" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="contactNumber">Contact Number</label>
                <input type="tel" name="contactNumber" id="contactNumber" class="form-control">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
            </div>

            <div class="form-group checkbox-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to the <a href="<?php echo URLROOT; ?>/pages/terms" target="_blank">Terms and Conditions</a></label>
            </div>

            <button type="submit" class="btn btn-primary btn-block ">Create Account</button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="<?php echo URLROOT; ?>/users/login">Login</a></p>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>