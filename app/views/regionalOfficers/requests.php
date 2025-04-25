<?php require APPROOT . '/views/includes/headers/officer_header.php'; ?>

<h2><?= htmlspecialchars($data['title']) ?></h2>

<!-- Approved Requests Table -->
<?php if (!empty($data['approvedRequests'])): ?>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Detail ID</th>
                <th>Item Name</th>
                <th>Quantity Needed</th>
                <th>Quantity Received</th>
                <th>Province</th>
                <th>Drop Off Location</th>
                <th>Drop Off Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['approvedRequests'] as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request->DetailID) ?></td>
                    <td><?= htmlspecialchars($request->ItemName) ?></td>
                    <td><?= htmlspecialchars($request->QuantityNeeded) ?></td>
                    <td><?= htmlspecialchars($request->QuantityReceived) ?></td>
                    <td><?= htmlspecialchars($request->Province) ?></td>
                    <td><?= htmlspecialchars($request->DropOffLocation) ?></td>
                    <td><?= htmlspecialchars($request->DropOffTime) ?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No approved requests found.</p>
<?php endif; ?>

<?php require APPROOT . '/views/includes/footer.php'; ?>