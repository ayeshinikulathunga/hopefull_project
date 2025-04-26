<?php require APPROOT . '/views/includes/headers/header.php'; ?>


<div class="auth-container">
    <div class="auth-card recipient-registration">
        <h2>Register as a Recipient</h2>
        <p class="auth-subtitle">Create an account to start receiving support</p>

        <?php flash('register_info'); ?>
        <?php flash('register_success'); ?>

        <?php if(isset($data['pending_approval']) && $data['pending_approval']): ?>
            <!-- Pending Approval View -->
            <div class="alert alert-info">
                <p>Your registration request is pending approval. You'll receive an email once your account is approved.</p>
                <p>Status: <strong>Pending Approval</strong></p>
            </div>

            <!-- Show submitted data (readonly) -->
            <div class="submitted-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" value="<?= $data['firstName'] ?? '' ?>" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" value="<?= $data['lastName'] ?? '' ?>" class="form-control" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" value="<?= $data['email'] ?? '' ?>" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="tel" value="<?= $data['contactNumber'] ?? '' ?>" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label>Organization Type</label>
                    <input type="text" value="<?= $data['organizationType'] ?? '' ?>" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" rows="3" readonly><?= $data['address'] ?? '' ?></textarea>
                </div>

                <button type="button" class="btn btn-secondary btn-block" disabled>Pending Approval</button>
            </div>

        <?php else: ?>
            <!-- Registration Form View -->
            <form action="<?= URLROOT ?>/users/register_recipient" method="POST" class="auth-form" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="firstName">First Name<span style="color: red;" class="required">*</span></label>
                        <input type="text" name="firstName" id="firstName" class="form-control" 
                               value="<?= $data['firstName'] ?? '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Last Name<span style="color: red;" class="required">*</span></label>
                        <input type="text" name="lastName" id="lastName" class="form-control" 
                               value="<?= $data['lastName'] ?? '' ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address<span style="color: red;" class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" 
                           value="<?= $data['email'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="contactNumber">Contact Number<span style="color: red;" class="required">*</span></label>
                    <input type="tel" name="contactNumber" id="contactNumber" class="form-control" 
                           value="<?= $data['contactNumber'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="organizationType">Organization Type<span style="color: red;" class="required">*</span></label>
                    <select name="organizationType" id="organizationType" class="form-control" required>
                        <option value="">Select Organization Type</option>
                        <option value="Individual" <?= (isset($data['organizationType']) && $data['organizationType'] === 'Individual' ? 'selected' : '') ?>>Individual</option>
                        <option value="NonProfit" <?= (isset($data['organizationType']) && $data['organizationType'] === 'NonProfit' ? 'selected' : '') ?>>Non-Profit Organization</option>
                        <option value="School" <?= (isset($data['organizationType']) && $data['organizationType'] === 'School' ? 'selected' : '') ?>>School</option>
                        <option value="Hospital" <?= (isset($data['organizationType']) && $data['organizationType'] === 'Hospital' ? 'selected' : '') ?>>Hospital</option>
                        <option value="Community" <?= (isset($data['organizationType']) && $data['organizationType'] === 'Community' ? 'selected' : '') ?>>Community Organization</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="address">Address<span style="color: red;" class="required">*</span></label>
                    <textarea name="address" id="address" class="form-control" rows="3" required><?= $data['address'] ?? '' ?></textarea>
                </div>

                <div class="form-group">
                    <label for="documentation">Documentation<span style="color: red;" class="required">*</span></label>
                    <input type="file" name="documentation" id="documentation" class="form-control" required>
                    <small class="form-text">Please upload relevant documentation to verify your identity/organization (PDF, JPG, PNG)</small>
                </div>

                <div class="form-group">
                    <label for="password">Password<span style="color: red;" class="required">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password<span style="color: red;" class="required">*</span></label>
                    <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="terms" name="terms" required <?= (isset($data['terms']) && $data['terms'] ? 'checked' : '') ?>>
                    <label for="terms">I agree to the <a href="<?= URLROOT ?>/pages/terms" target="_blank">Terms and Conditions</a></label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                <?php echo (isset($data['pending_approval']) ? 'Pending Approval' : 'Create Account'); ?>
                </button>
            </form>
        <?php endif; ?>

        <div class="auth-footer">
            <p>Already have an account? <a href="<?= URLROOT ?>/users/login">Login</a></p>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>