<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-add-product">
    <div class="form-card">
        <div class="form-header">
            <h2><i class="fas fa-plus-circle"></i> Add New Product</h2>
            <a href="<?php echo URLROOT; ?>/sellers/products" class="btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>
        
        <form action="<?php echo URLROOT; ?>/sellers/addProduct" method="POST" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label for="product_name">Product Name <span class="required">*</span></label>
                <input type="text" name="product_name" id="product_name" class="form-control <?php echo (!empty($data['product_name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['product_name']; ?>" required>
                <span class="invalid-feedback"><?php echo $data['product_name_err']; ?></span>
            </div>
            
            <div class="form-group">
                <label for="category">Category <span class="required">*</span></label>
                <select name="category" id="category" class="form-control <?php echo (!empty($data['category_err'])) ? 'is-invalid' : ''; ?>" required>
                    <option value="">Select Category</option>
                    <option value="Handicrafts" <?php echo ($data['category'] == 'Handicrafts') ? 'selected' : ''; ?>>Handicrafts</option>
                    <option value="Jewelry" <?php echo ($data['category'] == 'Jewelry') ? 'selected' : ''; ?>>Jewelry</option>
                    <option value="Textiles" <?php echo ($data['category'] == 'Textiles') ? 'selected' : ''; ?>>Textiles</option>
                    <option value="Home" <?php echo ($data['category'] == 'Home') ? 'selected' : ''; ?>>Home Decor</option>
                    <option value="Art" <?php echo ($data['category'] == 'Art') ? 'selected' : ''; ?>>Art</option>
                </select>
                <span class="invalid-feedback"><?php echo $data['category_err']; ?></span>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="price">Price (Rs.) <span class="required">*</span></label>
                    <input type="number" name="price" id="price" step="0.01" min="0" class="form-control <?php echo (!empty($data['price_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['price']; ?>" required>
                    <span class="invalid-feedback"><?php echo $data['price_err']; ?></span>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="stock_quantity">Stock Quantity <span class="required">*</span></label>
                    <input type="number" name="stock_quantity" id="stock_quantity" min="0" class="form-control <?php echo (!empty($data['stock_quantity_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['stock_quantity']; ?>" required>
                    <span class="invalid-feedback"><?php echo $data['stock_quantity_err']; ?></span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description <span class="required">*</span></label>
                <textarea name="description" id="description" rows="5" class="form-control <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>" required><?php echo $data['description']; ?></textarea>
                <span class="invalid-feedback"><?php echo $data['description_err']; ?></span>
                <small class="form-text text-muted">Provide a detailed description of the product. Include information about materials, dimensions, care instructions, etc.</small>
            </div>
            
            <div class="form-group">
                <label for="product_image">Product Image <span class="required">*</span></label>
                <div class="custom-file-upload">
                    <input type="file" name="product_image" id="product_image" class="file-input <?php echo (!empty($data['upload_err'])) ? 'is-invalid' : ''; ?>" accept="image/*" required>
                    <label for="product_image" class="file-label">
                        <i class="fas fa-cloud-upload-alt"></i> 
                        <span id="file-name">Choose a file</span>
                    </label>
                    <span class="invalid-feedback"><?php echo $data['upload_err']; ?></span>
                </div>
                <small class="form-text text-muted">File must be JPG, JPEG, PNG, or GIF. Recommended size: 800x800 pixels. Maximum file size: 2MB.</small>
                <div id="image-preview" class="image-preview-container"></div>
            </div>
            
            <div class="form-actions">
                <button type="reset" class="btn btn-secondary">Reset</button>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>

<script>
    // File input preview functionality
    const fileInput = document.getElementById('product_image');
    const fileLabel = document.getElementById('file-name');
    const imagePreview = document.getElementById('image-preview');
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            fileLabel.textContent = file.name;
            
            // Check file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size is too large. Maximum allowed size is 2MB.');
                this.value = '';
                fileLabel.textContent = 'Choose a file';
                imagePreview.innerHTML = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" alt="Image Preview">`;
            };
            reader.readAsDataURL(file);
        } else {
            fileLabel.textContent = 'Choose a file';
            imagePreview.innerHTML = '';
        }
    });
    
    // Form reset handler
    document.querySelector('button[type="reset"]').addEventListener('click', function() {
        fileLabel.textContent = 'Choose a file';
        imagePreview.innerHTML = '';
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>