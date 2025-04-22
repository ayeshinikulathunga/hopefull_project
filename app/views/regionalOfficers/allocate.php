<?php require APPROOT . '/views/includes/headers/officer_header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="mb-3"><?php echo $data['title']; ?></h1>
            
            <?php if (!empty($data['message'])): ?>
                <div class="alert alert-<?php echo $data['messageType']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $data['message']; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="m-0">Donation Requests Needing Items</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($data['approvedRequests'])): ?>
                        <p class="text-muted">No donation requests currently need items.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Item Name</th>
                                        <th>Needed</th>
                                        <th>Received</th>
                                        <th>Province</th>
                                        <th>Drop-off Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['approvedRequests'] as $request): ?>
                                        <?php $remaining = $request->QuantityNeeded - $request->QuantityReceived; ?>
                                        <tr>
                                            <td><?php echo $request->RequestID; ?></td>
                                            <td><?php echo $request->ItemName; ?></td>
                                            <td><?php echo $request->QuantityNeeded; ?></td>
                                            <td><?php echo $request->QuantityReceived; ?></td>
                                            <td><?php echo $request->Province; ?></td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($request->DropOffTime)); ?></td>
                                            <td>
                                                <a href="<?php echo URLROOT; ?>/regionalOfficers/allocate?request_id=<?php echo $request->RequestID; ?>&detail_id=<?php echo $request->DetailID; ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-arrow-right"></i> Select
                                                </a>
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
    </div>
    
    <?php if ($data['selectedRequest']): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="m-0">Selected Donation Request</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Request ID:</th>
                                    <td><?php echo $data['selectedRequest']->RequestID; ?></td>
                                </tr>
                                <tr>
                                    <th>Item Name:</th>
                                    <td><?php echo $data['selectedRequest']->ItemName; ?></td>
                                </tr>
                                <tr>
                                    <th>Quantity Needed:</th>
                                    <td><?php echo $data['selectedRequest']->QuantityNeeded; ?></td>
                                </tr>
                                <tr>
                                    <th>Quantity Received:</th>
                                    <td><?php echo $data['selectedRequest']->QuantityReceived; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Remaining Needed:</th>
                                    <td><strong class="text-danger"><?php echo $data['selectedRequest']->QuantityNeeded - $data['selectedRequest']->QuantityReceived; ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Province:</th>
                                    <td><?php echo $data['selectedRequest']->Province; ?></td>
                                </tr>
                                <tr>
                                    <th>Drop-off Location:</th>
                                    <td><?php echo $data['selectedRequest']->DropOffLocation; ?></td>
                                </tr>
                                <tr>
                                    <th>Drop-off Time:</th>
                                    <td><?php echo date('Y-m-d H:i', strtotime($data['selectedRequest']->DropOffTime)); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="m-0">Allocate Items</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Please specify the items you wish to allocate to this request.
                                <br>Remaining items needed: <strong><?php echo $data['selectedRequest']->QuantityNeeded - $data['selectedRequest']->QuantityReceived; ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <form action="<?php echo URLROOT; ?>/regionalOfficers/allocate?request_id=<?php echo $data['selectedRequest']->RequestID; ?>&detail_id=<?php echo $data['selectedRequest']->DetailID; ?>" method="post">
                        <input type="hidden" name="detail_id" value="<?php echo $data['selectedRequest']->DetailID; ?>">
                        
                        <div class="form-group row">
                            <label for="item_id" class="col-md-3 col-form-label">Inventory Item:</label>
                            <div class="col-md-9">
                                <select class="form-control" id="item_id" name="item_id" required>
                                    <option value="">-- Select Inventory Item --</option>
                                    <?php if(isset($data['matchingItems']) && !empty($data['matchingItems'])): ?>
                                        <?php foreach($data['matchingItems'] as $item): ?>
                                            <option value="<?php echo $item->ItemID; ?>" data-quantity="<?php echo $item->Quantity; ?>">
                                                <?php echo $item->ItemName; ?> (Available: <?php echo $item->Quantity; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php elseif(isset($data['inventory']) && !empty($data['inventory'])): ?>
                                        <?php foreach($data['inventory'] as $item): ?>
                                            <?php if($item->Status == 'Available' && $item->Quantity > 0): ?>
                                                <option value="<?php echo $item->ItemID; ?>" data-quantity="<?php echo $item->Quantity; ?>">
                                                    <?php echo $item->ItemName; ?> (Available: <?php echo $item->Quantity; ?>)
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">Select an item from inventory to allocate to this request.</small>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="quantity" class="col-md-3 col-form-label">Quantity to Allocate:</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                       min="1" max="<?php echo $data['selectedRequest']->QuantityNeeded - $data['selectedRequest']->QuantityReceived; ?>" 
                                       value="1" required>
                                <small class="form-text text-muted">Enter the quantity you wish to allocate for this request.</small>
                            </div>
                        </div>
                        
                        
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-success" name="allocate">
                                    <i class="fas fa-check-circle"></i> Confirm Allocation
                                </button>
                                <a href="<?php echo URLROOT; ?>/regionalOfficers/allocate" style="text-decoration:none;" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times-circle"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Update max quantity based on inventory selection
    const itemSelect = document.getElementById('item_id');
    const quantityInput = document.getElementById('quantity');
    
    if (itemSelect && quantityInput) {
        // Initial setup when page loads
        updateMaxQuantity();
        
        // Update when selection changes
        itemSelect.addEventListener('change', updateMaxQuantity);
        
        function updateMaxQuantity() {
            const option = itemSelect.options[itemSelect.selectedIndex];
            if (option && option.value) {
                // Extract the available quantity from the data attribute
                const availableQuantity = parseInt(option.getAttribute('data-quantity'));
                const remainingNeeded = parseInt(<?php echo isset($data['selectedRequest']) ? 
                    $data['selectedRequest']->QuantityNeeded - $data['selectedRequest']->QuantityReceived : 0; ?>);
                
                // Set max to the smaller of the two values
                const maxAllowed = Math.min(availableQuantity, remainingNeeded);
                quantityInput.max = maxAllowed;
                quantityInput.setAttribute('max', maxAllowed);
                
                // Update helper text
                const helperText = document.querySelector('#quantity + .form-text');
                if (helperText) {
                    helperText.textContent = `Enter quantity (max: ${maxAllowed})`;
                }
                
                // If current value is greater than new max, adjust it
                if (parseInt(quantityInput.value) > maxAllowed) {
                    quantityInput.value = maxAllowed;
                }
            }
        }
    }
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>