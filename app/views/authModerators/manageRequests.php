<?php require APPROOT . '/views/includes/headers/moderator_header.php'; ?>

<h2><?= $data['title'] ?></h2>

<!-- Approved Requests Table -->
<?php if (!empty($data['approvedRequests'])): ?>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Category</th>
                <th>Deadline</th>
                <th>Request Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['approvedRequests'] as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request->Title) ?></td>
                    <td><?= htmlspecialchars($request->RequestType) ?></td>
                    <td><?= htmlspecialchars($request->Category) ?></td>
                    <td><?= htmlspecialchars($request->Deadline) ?></td>
                    <td><?= htmlspecialchars($request->RequestStatus) ?></td>
                    <td>
                        
                        <form method="post" action="<?= URLROOT ?>/authModerators/updateRequestStatus">
                            <input type="hidden" name="request_id" value="<?= $request->RequestID ?>">
                            <div class = "custom-dropdown">
                            <select name="new_status">
                                <option value="Pending" <?= $request->RequestStatus == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="InProgress" <?= $request->RequestStatus == 'InProgress' ? 'selected' : '' ?>>InProgress</option>
                                <option value="Completed" <?= $request->RequestStatus == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Expired" <?= $request->RequestStatus == 'Expired' ? 'selected' : '' ?>>Expired</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </div>
                        </form>
          
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No approved requests found.</p>
<?php endif; ?>

<?php require APPROOT . '/views/includes/footer.php'; ?>
