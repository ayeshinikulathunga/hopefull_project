<?php require APPROOT . '/views/includes/headers/officer_header.php'; ?>

<div class="container">
    <h1><?php echo $data['title']; ?></h1>

    <?php flash('success_message'); ?>
    <?php flash('error_message'); ?>

    <form action="<?php echo URLROOT; ?>/regionalOfficers/addItem" method="POST">
        <div class="form-group">
            <label for="itemName">Item Name</label>
            <input type="text" name="itemName" class="form-control" value="<?php echo $data['itemName']; ?>" required>
            <?php if (!empty($data['errors']['itemName'])) : ?>
                <div class="text-danger"><?php echo $data['errors']['itemName']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" name="category" class="form-control" value="<?php echo $data['category']; ?>" required>
            <?php if (!empty($data['errors']['category'])) : ?>
                <div class="text-danger"><?php echo $data['errors']['category']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" class="form-control" value="<?php echo $data['quantity']; ?>" min="1" required>
            <?php if (!empty($data['errors']['quantity'])) : ?>
                <div class="text-danger"><?php echo $data['errors']['quantity']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control" required>
                <option value="Available" <?php echo ($data['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                <option value="Reserved" <?php echo ($data['status'] == 'Reserved') ? 'selected' : ''; ?>>Reserved</option>
                <option value="Distributed" <?php echo ($data['status'] == 'Distributed') ? 'selected' : ''; ?>>Distributed</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success btn-sm">Add Item</button>
        <a href="<?php echo URLROOT; ?>/regionalOfficers/inventory" style="text-decoration:none;" class="btn btn-danger btn-sm">Cancel</a>
    </form>
</div>
<br/>

<?php require APPROOT . '/views/includes/footer.php'; ?>