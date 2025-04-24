<?php require APPROOT . '/views/includes/headers/header.php'; ?>

<div class="auth-container">
    <div class="auth-card recipient-registration">
        <h2>Register as a Recipient</h2>
        <p class="auth-subtitle">Create an account to start receiving support</p>

        <form action="<?php echo URLROOT; ?>/users/register_recipient" method="POST" class="auth-form" enctype="multipart/form-data">
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
                <input type="tel" name="contactNumber" id="contactNumber" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="organizationType">Organization Type</label>
                <select name="organizationType" id="organizationType" class="form-control" required>
                    <option value="">Select Organization Type</option>
                    <option value="Individual">Individual</option>
                    <option value="NonProfit">Non-Profit Organization</option>
                    <option value="School">School</option>
                    <option value="Hospital">Hospital</option>
                    <option value="Community">Community Organization</option>
                </select>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="documentation">Documentation</label>
                <input type="file" name="documentation" id="documentation" class="form-control" required>
                <small class="form-text">Please upload relevant documentation to verify your identity/organization (PDF, JPG, PNG)</small>
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