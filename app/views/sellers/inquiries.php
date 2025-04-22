<?php require APPROOT . '/views/includes/headers/seller_header.php'; ?>

<div class="seller-inquiries">
    <?php flash('inquiry_message'); ?>
    
    <div class="inquiries-header">
        <div class="filters">
            <div class="search-container">
                <input type="text" id="inquirySearch" placeholder="Search inquiries..." class="search-input">
                <i class="fas fa-search"></i>
            </div>
            <div class="filter-container">
                <select id="statusFilter" class="filter-select">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Answered">Answered</option>
                    <option value="Closed">Closed</option>
                </select>
                <select id="productFilter" class="filter-select">
                    <option value="">All Products</option>
                    <?php
                    // Get unique product names
                    $productNames = [];
                    foreach($data['inquiries'] as $inquiry) {
                        if(!in_array($inquiry->ProductName, $productNames)) {
                            $productNames[] = $inquiry->ProductName;
                            echo '<option value="' . htmlspecialchars($inquiry->ProductName) . '">' . htmlspecialchars($inquiry->ProductName) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    
    <?php if(empty($data['inquiries'])): ?>
        <div class="no-inquiries">
            <div class="no-data-message">
                <i class="fas fa-question-circle"></i>
                <h3>No Inquiries Found</h3>
                <p>You don't have any product inquiries yet. Inquiries will appear here when customers ask questions about your products.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="inquiry-cards">
            <?php foreach($data['inquiries'] as $inquiry): ?>
                <div class="inquiry-card" data-status="<?php echo $inquiry->Status; ?>" data-product="<?php echo $inquiry->ProductName; ?>">
                    <div class="inquiry-header">
                        <div class="inquiry-info">
                            <h3><?php echo $inquiry->ProductName; ?></h3>
                            <span class="inquiry-date"><?php echo date('M j, Y', strtotime($inquiry->CreatedDate)); ?></span>
                        </div>
                        <span class="status-badge status-<?php echo strtolower($inquiry->Status); ?>"><?php echo $inquiry->Status; ?></span>
                    </div>
                    
                    <div class="inquiry-body">
                        <div class="customer-inquiry">
                            <div class="inquiry-user">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo $inquiry->Username; ?> asked:</span>
                            </div>
                            <div class="inquiry-message">
                                <?php echo $inquiry->Message; ?>
                            </div>
                        </div>
                        
                        <?php if($inquiry->Status === 'Answered' || $inquiry->Status === 'Closed'): ?>
                            <div class="seller-response">
                                <div class="inquiry-user">
                                    <i class="fas fa-store"></i>
                                    <span>Your response:</span>
                                </div>
                                <div class="inquiry-message">
                                    <?php echo $inquiry->Response; ?>
                                </div>
                                <div class="response-date">
                                    Responded on <?php echo date('M j, Y', strtotime($inquiry->ResponseDate)); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="inquiry-actions">
                                <button class="btn btn-primary answer-inquiry-btn" data-inquiry-id="<?php echo $inquiry->InquiryID; ?>" data-product-name="<?php echo $inquiry->ProductName; ?>" data-message="<?php echo htmlspecialchars($inquiry->Message); ?>">
                                    <i class="fas fa-reply"></i> Answer Inquiry
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Answer Inquiry Modal -->
    <div id="answerInquiryModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Answer Inquiry</h2>
            <form id="answerInquiryForm" action="<?php echo URLROOT; ?>/sellers/answerInquiry" method="POST">
                <input type="hidden" id="inquiryId" name="inquiry_id">
                <div class="form-group">
                    <label for="inquiryProductName">Product:</label>
                    <input type="text" id="inquiryProductName" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="inquiryMessage">Customer's Inquiry:</label>
                    <textarea id="inquiryMessage" class="form-control" readonly rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="inquiryResponse">Your Response:</label>
                    <textarea id="inquiryResponse" name="response" class="form-control" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Response</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Inquiry search and filter functionality
    const inquirySearch = document.getElementById('inquirySearch');
    const statusFilter = document.getElementById('statusFilter');
    const productFilter = document.getElementById('productFilter');
    const inquiryCards = document.querySelectorAll('.inquiry-card');
    
    function filterInquiries() {
        const searchTerm = inquirySearch.value.toLowerCase();
        const statusValue = statusFilter.value;
        const productValue = productFilter.value;
        
        inquiryCards.forEach(card => {
            const productName = card.getAttribute('data-product').toLowerCase();
            const status = card.getAttribute('data-status');
            const inquiryText = card.querySelector('.inquiry-message').innerText.toLowerCase();
            const username = card.querySelector('.inquiry-user span').innerText.toLowerCase();
            
            const matchesSearch = inquiryText.includes(searchTerm) || 
                                  productName.includes(searchTerm) || 
                                  username.includes(searchTerm);
            const matchesStatus = statusValue === '' || status === statusValue;
            const matchesProduct = productValue === '' || productName === productValue.toLowerCase();
            
            if (matchesSearch && matchesStatus && matchesProduct) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    inquirySearch.addEventListener('input', filterInquiries);
    statusFilter.addEventListener('change', filterInquiries);
    productFilter.addEventListener('change', filterInquiries);
    
    // Answer inquiry modal functionality
    const answerInquiryModal = document.getElementById('answerInquiryModal');
    const answerInquiryBtns = document.querySelectorAll('.answer-inquiry-btn');
    const answerInquiryCloseBtn = answerInquiryModal.querySelector('.close');
    
    answerInquiryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const inquiryId = this.getAttribute('data-inquiry-id');
            const productName = this.getAttribute('data-product-name');
            const message = this.getAttribute('data-message');
            
            document.getElementById('inquiryId').value = inquiryId;
            document.getElementById('inquiryProductName').value = productName;
            document.getElementById('inquiryMessage').value = message;
            document.getElementById('inquiryResponse').value = '';
            
            answerInquiryModal.style.display = 'block';
        });
    });
    
    answerInquiryCloseBtn.addEventListener('click', function() {
        answerInquiryModal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target == answerInquiryModal) {
            answerInquiryModal.style.display = 'none';
        }
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>