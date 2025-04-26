<?php require APPROOT . '/views/includes/headers/officer_header.php'; ?>

<div class="container mt-4">
    <h3>Non-Monetary Donation Tracking</h3>
    
    <?php if(isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show">
            <?php echo $_SESSION['flash_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header">
            <h4>Donation Receiving Status</h4>
        </div>
        <div class="card-body">
            <?php if(empty($data['donation_status'])): ?>
                <div class="alert alert-info">No donation records found.</div>
            <?php else: ?>
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
                            <tr>
                                <td><?php echo htmlspecialchars($donation->DonationID); ?></td>
                                <td><?php echo htmlspecialchars($donation->DonorName); ?></td>
                                <td><?php echo htmlspecialchars($donation->ItemName); ?></td>
                                <td><?php echo htmlspecialchars($donation->QuantityDonated); ?></td>
                                <td>
                                    <?php if(isset($donation->DropOffDate) && $donation->DropOffDate): ?>
                                        <?php echo htmlspecialchars(date('M j, Y', strtotime($donation->DropOffDate)) . ' at ' . date('g:i A', strtotime($donation->DropOffTime))); ?>
                                    <?php else: ?>
                                        Not scheduled
                                    <?php endif; ?>
                                </td>
                                <td class="text-<?php 
    echo $donation->Status === 'Completed' ? 'success' : 
         ($donation->Status === 'Pending' ? 'warning' : 'danger'); 
?>">
    <?php echo htmlspecialchars($donation->Status); ?>
</td>

<td>
    <?php if($donation->Status === 'Pending'): ?>
        <form action="<?php echo URLROOT; ?>/regionalOfficers/markReceived" method="POST" class="d-inline">
            <input type="hidden" name="donation_id" value="<?php echo htmlspecialchars($donation->DonationID); ?>">
            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark this donation as received?')">
                Mark as Received
            </button>
        </form>
        <?php elseif($donation->Status === 'Completed'): ?>
    <form action="<?php echo URLROOT; ?>/regionalOfficers/markPending" method="POST" class="d-inline">
        <input type="hidden" name="donation_id" value="<?php echo htmlspecialchars($donation->DonationID); ?>">
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Change status back to Pending?')">
            Undo
        </button>
    </form>
<?php endif; ?>
</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cancellation Requests Table -->
    <div class="card">
        <div class="card-header">
            <h4>Cancellation Requests</h4>
        </div>
        <div class="card-body">
            <?php if(empty($data['cancellation_requests'])): ?>
                <div class="alert alert-info">No cancellation requests pending.</div>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.view-details').click(function() {
        const donationId = $(this).data('donation-id');
        window.location.href = '<?php echo URLROOT; ?>/regionalOfficers/donationDetails/' + donationId;
    });
});
</script>


<?php require APPROOT . '/views/includes/footer.php'; ?>