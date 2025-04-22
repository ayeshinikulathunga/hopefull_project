<?php require APPROOT . '/views/includes/headers/officer_header.php'; ?>

<div class="container">
    <h1><?php echo $data['title']; ?></h1>

    <?php flash('success_message'); ?>
    <?php flash('error_message'); ?>

    <div class="mb-3">
    <a href="<?php echo URLROOT; ?>/regionalOfficers/addItem" style="text-decoration: none;" class="btn btn-sm btn-success">Add New Item</a>

    </div>

    <?php if (!empty($data['inventoryItems'])) : ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['inventoryItems'] as $item) : ?>
                    <tr>
                        <td><?php echo $item->ItemID; ?></td>
                        <td><?php echo $item->ItemName; ?></td>
                        <td><?php echo $item->Category; ?></td>
                        <td><?php echo $item->Quantity; ?></td>
                        <td><?php echo $item->Status; ?></td>
                        <td>
                            <a href="<?php echo URLROOT; ?>/regionalOfficers/editItem/<?php echo $item->ItemID; ?>" style="text-decoration: none;" class="btn btn-sm btn-info edit-user-btn">Edit</a>
                            <form action="<?php echo URLROOT; ?>/regionalOfficers/deleteItem/<?php echo $item->ItemID; ?>" method="POST" style="display:inline;">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No inventory items found.</p>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>