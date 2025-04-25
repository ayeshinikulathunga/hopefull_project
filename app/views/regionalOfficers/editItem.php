<?php require APPROOT . '/views/includes/headers/officer_header.php'; ?>

<div class="container mt-4">
    <h2>Edit Inventory Item - ID: <?php echo $data['itemId']; ?></h2>

    <?php flash('success_message'); ?>
    <?php flash('error_message'); ?>

    <form action="<?php echo URLROOT; ?>/regionalOfficers/editItem/<?php echo $data['itemId']; ?>" method="POST">
        <div class="mb-3">
            <label for="itemName" class="form-label">Item Name</label>
            <input type="text" name="itemName" class="form-control <?php echo !empty($data['errors']['itemName']) ? 'is-invalid' : ''; ?>" value="<?php echo $data['itemName']; ?>" required>
            <div class="invalid-feedback">
                <?php echo $data['errors']['itemName'] ?? ''; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" name="category" class="form-control <?php echo !empty($data['errors']['category']) ? 'is-invalid' : ''; ?>" value="<?php echo $data['category']; ?>" required>
            <div class="invalid-feedback">
                <?php echo $data['errors']['category'] ?? ''; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control <?php echo !empty($data['errors']['quantity']) ? 'is-invalid' : ''; ?>" value="<?php echo $data['quantity']; ?>" min="1" required>
            <div class="invalid-feedback">
                <?php echo $data['errors']['quantity'] ?? ''; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="Available" <?php echo ($data['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                <option value="Reserved" <?php echo ($data['status'] == 'Reserved') ? 'selected' : ''; ?>>Reserved</option>
                <option value="Distributed" <?php echo ($data['status'] == 'Distributed') ? 'selected' : ''; ?>>Distributed</option>
            </select>
        </div>
        <br/>

        <button type="submit" class="btn btn-success">Update Item</button>
        <a href="<?php echo URLROOT; ?>/regionalOfficers/inventory" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
