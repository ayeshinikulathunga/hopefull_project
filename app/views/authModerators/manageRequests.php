<?php require APPROOT . '/views/includes/headers/moderator_header.php'; ?>

<h2><?= $data['title'] ?></h2>

<!-- Display flash messages -->
<?php flash('success_message'); ?>
<?php flash('error_message', null, 'alert alert-danger'); ?>

<?php if (!empty($data['pendingRequests'])): ?>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Category</th>
                <th>Deadline</th>
                <th>Proof Document</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['pendingRequests'] as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request->Title) ?></td>
                    <td><?= htmlspecialchars($request->RequestType) ?></td>
                    <td><?= htmlspecialchars($request->Category) ?></td>
                    <td><?= htmlspecialchars($request->Deadline) ?></td>
                    <td>
                        <?php if (!empty($request->ProofDocument)) : ?>
                            <a href="<?= URLROOT .'/uploads/documents/'. $request->ProofDocument ?>" target="_blank" class="btn btn-sm btn-primary">View Document</a>
                        <?php else : ?>
                            <em>No document</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <form action="<?= URLROOT ?>/authModerators/approveRequest/<?= $request->RequestID ?>" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to approve this request?');">
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <form action="<?= URLROOT ?>/authModerators/rejectRequest/<?= $request->RequestID ?>" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to approve this request?');">
                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No pending requests found.</p>
<?php endif; ?>

<?php require APPROOT . '/views/includes/footer.php'; ?>