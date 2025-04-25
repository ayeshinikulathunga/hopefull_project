<?php require APPROOT . '/views/includes/headers/officer_header.php'; ?>

<div class="container mt-4">
    <h3>Non-Monetary Donation Tracking</h3>
    
    <!-- Donation Receiving Status Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Donation Receiving Status</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Donation ID</th>
                            <th>Donor</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Scheduled Drop-off</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['donation_status'] as $donation): ?>
                            /*
                        <tr>
                           <td><?php echo htmlspecialchars($donation->DonationID); ?></td>
                            <td><?php echo htmlspecialchars($donation->DonorName); ?></td>
                            <td><?php echo htmlspecialchars($donation->ItemName); ?></td>
                            <td><?php echo htmlspecialchars($donation->QuantityDonated); ?></td>
                            <td>
                                <?php if ($donation->DropOffDate): ?>
                                    <?php echo htmlspecialchars(date('M j, Y', strtotime($donation->DropOffDate)) . ' at ' . date('g:i A', strtotime($donation->DropOffTime))); ?>
                                <?php else: ?>
                                    Not scheduled
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $donation->Status === 'Completed' ? 'success' : 
                                         ($donation->Status === 'Pending' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php echo htmlspecialchars($donation->Status); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <?php if ($donation->Status === 'Pending'): ?>
                                        <button class="btn btn-sm btn-success mark-received" data-donation-id="<?php echo htmlspecialchars($donation->DonationID); ?>">
                                            Mark Received
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-info view-details" data-donation-id="<?php echo htmlspecialchars($donation->DonationID); ?>">
                                        Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cancellation Requests Table -->
    <div class="card">
        <div class="card-header">
            <h4>Cancellation Requests</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Donation ID</th>
                            <th>Donor</th>
                            <th>Item</th>
                            <th>Reason</th>
                            <th>Request Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['cancellation_requests'] as $cancellation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cancellation->DonationID); ?></td>
                            <td><?php echo htmlspecialchars($cancellation->DonorName); ?></td>
                            <td><?php echo htmlspecialchars($cancellation->ItemName); ?></td>
                            <td><?php echo htmlspecialchars($cancellation->CancellationReason); ?></td>
                            <td><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($cancellation->CancellationDate))); ?></td>
                            <td>
                                <div class="btn-group">
                                    <form action="<?php echo URLROOT; ?>/regionalOfficers/approveCancellation" method="POST" class="d-inline">
                                        <input type="hidden" name="donation_id" value="<?php echo htmlspecialchars($cancellation->DonationID); ?>">
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this cancellation?')">Approve</button>
                                    </form>
                                    <form action="<?php echo URLROOT; ?>/regionalOfficers/rejectCancellation" method="POST" class="d-inline">
                                        <input type="hidden" name="donation_id" value="<?php echo htmlspecialchars($cancellation->DonationID); ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this cancellation?')">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Mark donation as received
    $('.mark-received').click(function() {
        const donationId = $(this).data('donation-id');
        if (confirm('Mark this donation as received?')) {
            $.post('<?php echo URLROOT; ?>/regionalOfficers/markReceived', { donation_id: donationId }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }).fail(function() {
                alert('Failed to process request');
            });
        }
    });

    // View donation details
    $('.view-details').click(function() {
        const donationId = $(this).data('donation-id');
        // Implement modal or redirect to details page
        window.location.href = '<?php echo URLROOT; ?>/regionalOfficers/donationDetails/' + donationId;
    });
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>