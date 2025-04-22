<?php require APPROOT . '/views/includes/headers/recipient_header.php'; ?>

<div class="container edit-request-page-container">
    <div class="form-container">
        <div class="form-header">
            <h1>Edit Donation Request</h1>
            <a href="<?php echo URLROOT; ?>/recipients/viewRequest/<?php echo $data['requestId']; ?>" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Request
            </a>
        </div>
        
        <div class="form-card">
            <form action="<?php echo URLROOT; ?>/recipients/editRequest/<?php echo $data['requestId']; ?>" method="POST" enctype="multipart/form-data">
                <!-- Request Type - Read Only in Edit Mode -->
                <div class="form-group">
                    <label>Request Type</label>
                    <div class="readonly-field">
                        <?php if($data['requestType'] == 'Monetary'): ?>
                            <i class="fas fa-hand-holding-usd"></i> Monetary Request
                        <?php else: ?>
                            <i class="fas fa-box-open"></i> Non-Monetary Request
                        <?php endif; ?>
                        <input type="hidden" name="requestType" value="<?php echo $data['requestType']; ?>">
                    </div>
                    <small class="form-text">Request type cannot be changed after creation.</small>
                </div>
                
                <!-- Basic Request Details -->
                <div class="form-section">
                    <h3>Basic Information</h3>
                    
                    <div class="form-group">
                        <label for="title">Request Title <span class="required">*</span></label>
                        <input type="text" name="title" id="title" class="form-control <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['title']; ?>" maxlength="100" required>
                        <span class="error-text"><?php echo $data['title_err']; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category <span class="required">*</span></label>
                        <select name="category" id="category" class="form-control <?php echo (!empty($data['category_err'])) ? 'is-invalid' : ''; ?>" required>
                            <option value="" disabled <?php echo empty($data['category']) ? 'selected' : ''; ?>>Select a category</option>
                            <option value="Healthcare" <?php echo ($data['category'] == 'Healthcare') ? 'selected' : ''; ?>>Healthcare</option>
                            <option value="Education" <?php echo ($data['category'] == 'Education') ? 'selected' : ''; ?>>Education</option>
                            <option value="Community" <?php echo ($data['category'] == 'Community') ? 'selected' : ''; ?>>Community</option>
                            <option value="Sports" <?php echo ($data['category'] == 'Sports') ? 'selected' : ''; ?>>Sports</option>
                            <option value="MakeAWish" <?php echo ($data['category'] == 'MakeAWish') ? 'selected' : ''; ?>>Make A Wish</option>
                        </select>
                        <span class="error-text"><?php echo $data['category_err']; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description <span class="required">*</span></label>
                        <textarea name="description" id="description" class="form-control <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>" rows="6" required><?php echo $data['description']; ?></textarea>
                        <span class="error-text"><?php echo $data['description_err']; ?></span>
                        <small class="form-text">Describe your request in detail. Include why you need help, how the donations will be used, and who will benefit.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="deadline">Deadline <span class="required">*</span></label>
                        <input type="date" name="deadline" id="deadline" class="form-control <?php echo (!empty($data['deadline_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['deadline']; ?>" required>
                        <span class="error-text"><?php echo $data['deadline_err']; ?></span>
                    </div>
                </div>
                
                <!-- Monetary Request Fields -->
                <?php if($data['requestType'] == 'Monetary'): ?>
                <div class="form-section">
                    <h3>Monetary Request Details</h3>
                    
                    <div class="form-group">
                        <label for="targetAmount">Target Amount <span class="required">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rs.</span>
                            </div>
                            <input type="number" name="targetAmount" id="targetAmount" class="form-control <?php echo (!empty($data['targetAmount_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['targetAmount']; ?>" min="0" step="1" required>
                        </div>
                        <span class="error-text"><?php echo isset($data['targetAmount_err']) ? $data['targetAmount_err'] : ''; ?></span>
                    </div>
                </div>
                <?php else: ?>
                <!-- Non-Monetary Request Fields -->
                <div class="form-section">
                    <h3>Non-Monetary Request Details</h3>
                    
                    <div class="form-group">
                        <label for="itemName">Item Name <span class="required">*</span></label>
                        <input type="text" name="itemName" id="itemName" class="form-control <?php echo (!empty($data['itemName_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['itemName']; ?>" required>
                        <span class="error-text"><?php echo isset($data['itemName_err']) ? $data['itemName_err'] : ''; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantityNeeded">Quantity Needed <span class="required">*</span></label>
                        <input type="number" name="quantityNeeded" id="quantityNeeded" class="form-control <?php echo (!empty($data['quantityNeeded_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['quantityNeeded']; ?>" min="1" step="1" required>
                        <span class="error-text"><?php echo isset($data['quantityNeeded_err']) ? $data['quantityNeeded_err'] : ''; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="province">Province <span class="required">*</span></label>
                        <select name="province" id="province" class="form-control <?php echo (!empty($data['province_err'])) ? 'is-invalid' : ''; ?>" required>
                            <option value="" disabled <?php echo empty($data['province']) ? 'selected' : ''; ?>>Select a province</option>
                            <option value="Western" <?php echo ($data['province'] == 'Western') ? 'selected' : ''; ?>>Western</option>
                            <option value="Central" <?php echo ($data['province'] == 'Central') ? 'selected' : ''; ?>>Central</option>
                            <option value="Southern" <?php echo ($data['province'] == 'Southern') ? 'selected' : ''; ?>>Southern</option>
                            <option value="Northern" <?php echo ($data['province'] == 'Northern') ? 'selected' : ''; ?>>Northern</option>
                            <option value="Eastern" <?php echo ($data['province'] == 'Eastern') ? 'selected' : ''; ?>>Eastern</option>
                            <option value="North-Western" <?php echo ($data['province'] == 'North-Western') ? 'selected' : ''; ?>>North-Western</option>
                            <option value="North-Central" <?php echo ($data['province'] == 'North-Central') ? 'selected' : ''; ?>>North-Central</option>
                            <option value="Uva" <?php echo ($data['province'] == 'Uva') ? 'selected' : ''; ?>>Uva</option>
                            <option value="Sabaragamuwa" <?php echo ($data['province'] == 'Sabaragamuwa') ? 'selected' : ''; ?>>Sabaragamuwa</option>
                        </select>
                        <span class="error-text"><?php echo isset($data['province_err']) ? $data['province_err'] : ''; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="dropOffLocation">Drop-off Location <span class="required">*</span></label>
                        <textarea name="dropOffLocation" id="dropOffLocation" class="form-control <?php echo (!empty($data['dropOffLocation_err'])) ? 'is-invalid' : ''; ?>" rows="3" required><?php echo $data['dropOffLocation']; ?></textarea>
                        <span class="error-text"><?php echo isset($data['dropOffLocation_err']) ? $data['dropOffLocation_err'] : ''; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="dropOffTime">Drop-off Time <span class="required">*</span></label>
                        <input type="datetime-local" name="dropOffTime" id="dropOffTime" class="form-control <?php echo (!empty($data['dropOffTime_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['dropOffTime']; ?>" required>
                        <span class="error-text"><?php echo isset($data['dropOffTime_err']) ? $data['dropOffTime_err'] : ''; ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- File Upload Section -->
                <div class="form-section">
                    <h3>Supporting Documents</h3>
                    
                    <div class="form-group">
                        <label for="requestImage">Request Image</label>
                        <input type="file" name="requestImage" id="requestImage" class="form-control file-input <?php echo (!empty($data['requestImage_err'])) ? 'is-invalid' : ''; ?>" accept="image/jpeg,image/jpg,image/png">
                        <span class="error-text"><?php echo isset($data['requestImage_err']) ? $data['requestImage_err'] : ''; ?></span>
                        <small class="form-text">Upload a new image only if you want to replace the current one (JPG or PNG only, max 2MB).</small>
                        
                        <div class="image-preview">
                            <div id="imagePreviewContainer" class="preview-container">
                                <?php if($data['request']->HasImage): ?>
                                    <div class="current-image">
                                        <img src="<?php echo URLROOT; ?>/uploads/requests/<?php echo $data['requestId']; ?>.jpg?v=<?php echo time(); ?>" alt="Current Image" class="preview-image">
                                        <span class="image-label">Current Image</span>
                                    </div>
                                <?php else: ?>
                                    <div class="no-preview">
                                        <i class="fas fa-image"></i>
                                        <p>No current image</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="proofDocument">Proof Document</label>
                        <input type="file" name="proofDocument" id="proofDocument" class="form-control file-input <?php echo (!empty($data['proofDocument_err'])) ? 'is-invalid' : ''; ?>" accept="application/pdf">
                        <span class="error-text"><?php echo isset($data['proofDocument_err']) ? $data['proofDocument_err'] : ''; ?></span>
                        <small class="form-text">Upload a new document only if you want to replace the current one (PDF only, max 5MB).</small>
                        
                        <?php if($data['request']->HasProofDocument): ?>
                            <div class="current-document">
                                <i class="fas fa-file-pdf"></i>
                                <span>Current proof document is uploaded</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="<?php echo URLROOT; ?>/recipients/viewRequest/<?php echo $data['requestId']; ?>" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle image preview for new uploads
        const requestImage = document.getElementById('requestImage');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        let currentImage = null;
        
        // Store the current image HTML if it exists
        if (document.querySelector('.current-image')) {
            currentImage = document.querySelector('.current-image').outerHTML;
        }
        
        requestImage.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreviewContainer.innerHTML = `
                        <div class="current-image">
                            ${currentImage ? currentImage : ''}
                        </div>
                        <div class="new-image">
                            <img src="${e.target.result}" alt="New Image Preview" class="preview-image">
                            <span class="image-label">New Image</span>
                        </div>
                    `;
                }
                
                reader.readAsDataURL(file);
            } else {
                // If no new file is selected, show only current image if it exists
                if (currentImage) {
                    imagePreviewContainer.innerHTML = currentImage;
                } else {
                    imagePreviewContainer.innerHTML = `
                        <div class="no-preview">
                            <i class="fas fa-image"></i>
                            <p>No image selected</p>
                        </div>
                    `;
                }
            }
        });
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>