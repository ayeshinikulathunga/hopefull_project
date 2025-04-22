<?php require APPROOT . '/views/includes/headers/marketplace_header.php'; ?>

<section class="mp-inquiries">
    <div class="container mp-inquiries__container">
        <h1 class="mp-inquiries__title">Product Inquiries</h1>
        
        <?php flash('inquiry_message'); ?>
        
        <div class="mp-inquiries__form">
            <h2 class="mp-inquiries__form-title">Submit a New Inquiry</h2>
            <form action="<?php echo URLROOT; ?>/marketplace/inquiries" method="POST">
                <div class="form-group">
                    <label for="product_id">Select Product</label>
                    <select id="product_id" name="product_id" class="form-control <?php echo (!empty($data['product_id_err'])) ? 'is-invalid' : ''; ?>" required>
                        <option value="">Select a product</option>
                        <?php foreach($data['products'] as $product): ?>
                            <option value="<?php echo $product->ProductID; ?>" <?php echo (isset($data['product_id']) && $data['product_id'] == $product->ProductID) ? 'selected' : ''; ?>>
                                <?php echo $product->ProductName; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="invalid-feedback"><?php echo $data['product_id_err'] ?? ''; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="message">Your Inquiry</label>
                    <textarea id="message" name="message" rows="5" class="form-control <?php echo (!empty($data['message_err'])) ? 'is-invalid' : ''; ?>" required placeholder="Type your question about the product here..."><?php echo $data['message'] ?? ''; ?></textarea>
                    <span class="invalid-feedback"><?php echo $data['message_err'] ?? ''; ?></span>
                    <small class="form-text text-muted">Be specific with your question to get the most accurate information.</small>
                </div>
                
                <button type="submit" class="mp-inquiries__submit">Submit Inquiry</button>
            </form>
        </div>
        
        <?php if (!empty($data['inquiries'])): ?>
            <div class="mp-inquiries__history">
                <h3 class="mp-inquiries__history-title">Your Inquiry History</h3>
                <div class="mp-inquiries__list">
                    <?php foreach($data['inquiries'] as $inquiry): ?>
                        <div class="mp-inquiries__item">
                            <div class="mp-inquiries__header">
                                <div>
                                    <h4 class="mp-inquiries__product"><?php echo $inquiry->ProductName; ?></h4>
                                    <p class="mp-inquiries__date"><?php echo date('F j, Y', strtotime($inquiry->CreatedDate)); ?></p>
                                </div>
                                <span class="mp-inquiries__status mp-inquiries__status-<?php echo strtolower($inquiry->Status); ?>">
                                    <?php echo $inquiry->Status; ?>
                                </span>
                            </div>
                            
                            <div class="mp-inquiries__message">
                                <strong>Your Inquiry:</strong>
                                <p><?php echo $inquiry->Message; ?></p>
                            </div>
                            
                            <?php if($inquiry->Status === 'Answered' || $inquiry->Status === 'Closed'): ?>
                                <div class="mp-inquiries__response">
                                    <div class="mp-inquiries__response-header">Response:</div>
                                    <p><?php echo $inquiry->Response; ?></p>
                                    <?php if(!empty($inquiry->ResponseDate)): ?>
                                    <p class="mp-inquiries__response-date">
                                        <small>Responded on: <?php echo date('F j, Y', strtotime($inquiry->ResponseDate)); ?></small>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center mt-5">
                <p>You haven't submitted any inquiries yet.</p>
                <p>If you have questions about any product, feel free to submit an inquiry above.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require APPROOT . '/views/includes/footer.php'; ?>