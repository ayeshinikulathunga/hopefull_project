<?php require APPROOT . '/views/includes/headers/recipient_header.php'; ?>

<div class="container create-request-page-container">
    <div class="form-container">
        <div class="form-header">
            <h1>Create Donation Request</h1>
            <a href="<?php echo URLROOT; ?>/recipients/requests" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Requests
            </a>
        </div>
        
        <div class="form-card">
            <form action="<?php echo URLROOT; ?>/recipients/createRequest" method="POST" enctype="multipart/form-data">
                <!-- Request Type Selection -->
                <div class="form-group">
                    <label for="requestType">Request Type <span class="required">*</span></label>
                    <div class="request-type-selector">
                        <div class="type-option <?php echo ($data['requestType'] == 'Monetary') ? 'active' : ''; ?>">
                            <input type="radio" name="requestType" id="monetary" value="Monetary" <?php echo ($data['requestType'] == 'Monetary') ? 'checked' : ''; ?> required>
                            <label for="monetary">
                                <i class="fas fa-hand-holding-usd"></i>
                                <span>Monetary</span>
                                <small>Request financial assistance</small>
                            </label>
                        </div>
                        <div class="type-option <?php echo ($data['requestType'] == 'NonMonetary') ? 'active' : ''; ?>">
                            <input type="radio" name="requestType" id="nonMonetary" value="NonMonetary" <?php echo ($data['requestType'] == 'NonMonetary') ? 'checked' : ''; ?> required>
                            <label for="nonMonetary">
                                <i class="fas fa-box-open"></i>
                                <span>Non-Monetary</span>
                                <small>Request physical items or goods</small>
                            </label>
                        </div>
                    </div>
                    <span class="error-text"><?php echo $data['requestType_err']; ?></span>
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
                <div id="monetaryFields" class="form-section <?php echo ($data['requestType'] != 'Monetary') ? 'hidden' : ''; ?>">
                    <h3>Monetary Request Details</h3>
                    
                    <div class="form-group">
                        <label for="targetAmount">Target Amount <span class="required">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rs.</span>
                            </div>
                            <input type="number" name="targetAmount" id="targetAmount" class="form-control <?php echo (!empty($data['targetAmount_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['targetAmount']; ?>" min="0" step="1">
                        </div>
                        <span class="error-text"><?php echo isset($data['targetAmount_err']) ? $data['targetAmount_err'] : ''; ?></span>
                    </div>
                </div>
                
               <!-- Non-Monetary Request Fields -->
<div id="nonMonetaryFields" class="form-section <?php echo ($data['requestType'] != 'NonMonetary') ? 'hidden' : ''; ?>">
    <h3>Non-Monetary Request Details</h3>
    
    <div class="form-group">
        <label for="itemName">Item Name <span class="required">*</span></label>
        <input type="text" name="itemName" id="itemName" class="form-control <?php echo (!empty($data['itemName_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['itemName']); ?>" required>
        <span class="error-text"><?php echo $data['itemName_err']; ?></span>
    </div>
    
    <div class="form-group">
        <label for="quantityNeeded">Quantity Needed <span class="required">*</span></label>
        <input type="number" name="quantityNeeded" id="quantityNeeded" class="form-control <?php echo (!empty($data['quantityNeeded_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['quantityNeeded']); ?>" min="1" step="1" required>
        <span class="error-text"><?php echo $data['quantityNeeded_err']; ?></span>
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
        <span class="error-text"><?php echo $data['province_err']; ?></span>
    </div>
    
    <div class="form-group">
        <label for="dropOffLocation">Drop-off Location <span class="required">*</span></label>
        <textarea name="dropOffLocation" id="dropOffLocation" class="form-control <?php echo (!empty($data['dropOffLocation_err'])) ? 'is-invalid' : ''; ?>" rows="3" required><?php echo htmlspecialchars($data['dropOffLocation']); ?></textarea>
        <span class="error-text"><?php echo $data['dropOffLocation_err']; ?></span>
    </div>
    
    <div class="form-group">
        <label for="dropOffTime">Drop-off Time <span class="required">*</span></label>
        <input type="datetime-local" name="dropOffTime" id="dropOffTime" class="form-control <?php echo (!empty($data['dropOffTime_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['dropOffTime']); ?>" required>
        <span class="error-text"><?php echo $data['dropOffTime_err']; ?></span>
    </div>
</div>
                
                <!-- File Upload Section -->
                <div class="form-section">
                    <h3>Supporting Documents</h3>
                    
                    <div class="form-group">
                        <label for="requestImage">Request Image <span class="required">*</span></label>
                        <input type="file" name="requestImage" id="requestImage" class="form-control file-input <?php echo (!empty($data['requestImage_err'])) ? 'is-invalid' : ''; ?>" accept="image/jpeg,image/jpg,image/png" required>
                        <span class="error-text"><?php echo isset($data['requestImage_err']) ? $data['requestImage_err'] : ''; ?></span>
                        <small class="form-text">Upload an image related to your request (JPG or PNG only, max 2MB). This will be displayed publicly.</small>
                        
                        <div class="image-preview">
                            <div id="imagePreviewContainer" class="preview-container">
                                <div class="no-preview">
                                    <i class="fas fa-image"></i>
                                    <p>No image selected</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="proofDocument">Proof Document <span class="required">*</span></label>
                        <input type="file" name="proofDocument" id="proofDocument" class="form-control file-input <?php echo (!empty($data['proofDocument_err'])) ? 'is-invalid' : ''; ?>" accept="application/pdf" required>
                        <span class="error-text"><?php echo isset($data['proofDocument_err']) ? $data['proofDocument_err'] : ''; ?></span>
                        <small class="form-text">Upload a PDF document as proof for your request (medical reports, school documents, etc.). PDF only, max 5MB.</small>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Create Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Handle request type selection
    const monetaryRadio = document.getElementById('monetary');
    const nonMonetaryRadio = document.getElementById('nonMonetary');
    const monetaryFields = document.getElementById('monetaryFields');
    const nonMonetaryFields = document.getElementById('nonMonetaryFields');
    
    // Function to toggle form fields based on request type
    function toggleFields() {
        if (monetaryRadio.checked) {
            monetaryFields.classList.remove('hidden');
            nonMonetaryFields.classList.add('hidden');
            
            // Make monetary fields required
            document.querySelectorAll('#monetaryFields input:not([type="file"]), #monetaryFields select').forEach(elem => {
                if (elem.id === 'targetAmount') {
                    elem.setAttribute('required', 'required');
                }
            });
            
            // Remove required attribute from non-monetary fields
            document.querySelectorAll('#nonMonetaryFields input:not([type="file"]), #nonMonetaryFields select, #nonMonetaryFields textarea').forEach(elem => {
                elem.removeAttribute('required');
            });
            
            // Add active class to monetary option
            document.querySelector('.type-option:first-child').classList.add('active');
            document.querySelector('.type-option:last-child').classList.remove('active');
            
        } else if (nonMonetaryRadio.checked) {
            monetaryFields.classList.add('hidden');
            nonMonetaryFields.classList.remove('hidden');
            
            // Remove required attribute from monetary fields
            document.querySelectorAll('#monetaryFields input:not([type="file"]), #monetaryFields select').forEach(elem => {
                elem.removeAttribute('required');
            });
            
            // Make non-monetary fields required
            document.querySelectorAll('#nonMonetaryFields input:not([type="file"]), #nonMonetaryFields select').forEach(elem => {
                if (['itemName', 'quantityNeeded', 'province', 'dropOffLocation', 'dropOffTime'].includes(elem.id)) {
                    elem.setAttribute('required', 'required');
                }
            });
            
            // Add active class to non-monetary option
            document.querySelector('.type-option:first-child').classList.remove('active');
            document.querySelector('.type-option:last-child').classList.add('active');
        }
    }
    
    // Add event listeners for radio buttons
    monetaryRadio.addEventListener('change', toggleFields);
    nonMonetaryRadio.addEventListener('change', toggleFields);
    
    // Also add click event listeners to the label containers
    document.querySelectorAll('.type-option').forEach(option => {
        option.addEventListener('click', function() {
            const radioInput = this.querySelector('input[type="radio"]');
            radioInput.checked = true;
            
            // Manually trigger the change event
            const event = new Event('change');
            radioInput.dispatchEvent(event);
        });
    });
    
    // Set initial state
    toggleFields();
    
    // Handle image preview
    const requestImage = document.getElementById('requestImage');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    
    requestImage.addEventListener('change', function() {
        const file = this.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                imagePreviewContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Image Preview" class="preview-image">
                `;
            }
            
            reader.readAsDataURL(file);
        } else {
            imagePreviewContainer.innerHTML = `
                <div class="no-preview">
                    <i class="fas fa-image"></i>
                    <p>No image selected</p>
                </div>
            `;
        }
    });
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; 
                     