<?php require APPROOT . '/views/includes/headers/admin_header.php'; ?>

<div class="verification-requests">
    <h3>Pending Verification Requests</h3>
    <div class="table-responsive">
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
                <?php foreach($data['pending_verifications'] as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request->FirstName . ' ' . $request->LastName); ?></td>
                    <td><?php echo htmlspecialchars($request->OrganizationType); ?></td>
                    <td><?php echo htmlspecialchars($request->Email); ?></td>
                    <td>
                        <?php if (!empty($request->DocumentationURL)): ?>
                            <a href="<?php echo URLROOT . '/uploads/documents/' . htmlspecialchars($request->DocumentationURL); ?>" target="_blank" class="btn btn-sm btn-info">View Document</a>
                        <?php else: ?>
                            No Document
                        <?php endif; ?>
                    </td>
                    <td>
    <div class="btn-group">
        <form action="<?php echo URLROOT; ?>/admins/approve" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to approve this request?');">
            <input type="hidden" name="recipient_id" value="<?php echo htmlspecialchars($request->RecipientID); ?>">
            <button type="submit" class="btn btn-sm btn-success">Approve</button>
        </form>
        <form action="<?php echo URLROOT; ?>/admins/reject" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to reject this request?');">
            <input type="hidden" name="recipient_id" value="<?php echo htmlspecialchars($request->RecipientID); ?>">
            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
        </form>
    </div>
</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>


