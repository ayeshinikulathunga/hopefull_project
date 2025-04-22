<?php require APPROOT . '/views/includes/headers/header.php'; ?>

<div class="auth-container">
    <div class="auth-card register-choice">
        <h2>Join Hopefull</h2>
        <p class="auth-subtitle">Choose how you want to make a difference</p>

        <div class="register-options">
            <a href="<?php echo URLROOT; ?>/users/register_donor" class="register-option">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>Register as a Donor</h3>
                <p>Support causes and make donations to help those in need</p>
            </a>

            <a href="<?php echo URLROOT; ?>/users/register_recipient" class="register-option">
                <i class="fas fa-users"></i>
                <h3>Register as a Recipient</h3>
                <p>Create campaigns and receive support for your cause</p>
            </a>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>