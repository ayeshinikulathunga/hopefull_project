<?php require APPROOT . '/views/includes/headers/moderator_header.php'; ?>

<?php if (isset($_SESSION['moderator_success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['moderator_success']; ?>
        <?php unset($_SESSION['moderator_success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['moderator_error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['moderator_error']; ?>
        <?php unset($_SESSION['moderator_error']); ?>
    </div>
<?php endif; ?>


<div class="verification-requests">
    <h2>Manage Recipients</h2>
    <div class="table-responsive">
        <?php if (!empty($data['pendingRecipients'])) : ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Organization Type</th>
                        <th>Email</th>
                        <th>Proof Document</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['pendingRecipients'] as $recipient) : ?>
                        <tr>
                            <td><?= htmlspecialchars($recipient->FirstName . ' ' . $recipient->LastName) ?></td>
                            <td><?= htmlspecialchars($recipient->OrganizationType) ?></td>
                            <td><?= htmlspecialchars($recipient->Email) ?></td>
                            <td>
    <?php if (!empty($recipient->DocumentationURL)) : ?>
        <a href="<?= URLROOT . '/uploads/documents/' . $recipient->DocumentationURL ?>" target="_blank" class="btn btn-sm btn-info">View Document</a>
    <?php else : ?>
        <em>No document</em>
    <?php endif; ?>
</td>

                            <td>
                                <div class="btn-group">
                                    <form action="<?= URLROOT ?>/authModerators/approve" method="post" style="display:inline;">
                                        <input type="hidden" name="recipient_id" value="<?= $recipient->RecipientID ?>">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="<?= URLROOT ?>/authModerators/reject" method="post" style="display:inline;">
                                        <input type="hidden" name="recipient_id" value="<?= $recipient->RecipientID ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No pending recipients at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
