<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<div class="donation-checkout">
    <div class="container">
        <!-- Breadcrumb Navigation -->
        <div class="donation-breadcrumb">
            <a href="<?php echo URLROOT; ?>">Home</a> &gt;
            <a href="<?php echo URLROOT; ?>/donors/allRequests">All Requests</a> &gt;
            <a href="<?php echo URLROOT; ?>/donors/details/<?php echo $data['request']->RequestID; ?>"><?php echo $data['request']->Title; ?></a> &gt;
            <span>Donate</span>
        </div>

        <h1 class="donation-checkout__title">Make a Non-Monetary Donation</h1>

        <div class="donation-checkout__container">
            <!-- Left Column - Checkout Form -->
            <div class="donation-checkout__form">
                <form id="donationForm" action="<?php echo URLROOT; ?>/donations/donate/<?php echo $data['request']->RequestID; ?>" method="POST">
                    <input type="hidden" name="request_id" value="<?php echo $data['request']->RequestID; ?>">
                    <input type="hidden" name="donation_type" value="nonmonetary">

                    <!-- Donation Quantity Section -->
                    <div class="donation-checkout__section">
                        <h2>Donation Details</h2>
                        <div class="donation-checkout__intro">
                            You're donating <strong><?php echo isset($data['itemDetails']) ? $data['itemDetails']->ItemName : 'items'; ?></strong> to this request. Please specify the quantity you wish to donate.
                        </div>
                        
                        <div class="form-group">
                            <label for="quantity">Quantity to Donate</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" min="1" required value="<?php echo $data['quantity'] ?? '1'; ?>">
                            <?php if(isset($data['quantity_err'])): ?>
                                <span class="form-error"><?php echo $data['quantity_err']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Schedule Dropoff Section -->
                    <div class="donation-checkout__section">
                        <h2>Schedule Drop-off</h2>
                        
                        <div class="schedule-dropoff">
                            <div class="dropoff-details">
                                <div class="dropoff-location">
                                    <h3>Drop-off Location</h3>
                                    <p><strong>Address:</strong> <?php echo isset($data['itemDetails']) ? $data['itemDetails']->DropOffLocation : 'N/A'; ?></p>
                                    <p><strong>Province:</strong> <?php echo isset($data['itemDetails']) ? $data['itemDetails']->Province : 'N/A'; ?></p>
                                </div>
                                
                                <div class="dropoff-hours">
                                    <h3>Available Drop-off Hours</h3>
                                    <p>Monday to Friday: 9:00 AM - 5:00 PM</p>
                                    <p>Saturday: 10:00 AM - 2:00 PM</p>
                                    <p>Sunday: Closed</p>
                                </div>
                            </div>
                            
                            <div class="date-time-selector">
                                <div class="form-group">
                                    <label for="dropoff_date">Select Drop-off Date</label>
                                    <?php 
                                        // Calculate min date (today) and max date (deadline)
                                        $today = date('Y-m-d');
                                        $deadline = date('Y-m-d', strtotime($data['request']->Deadline));
                                    ?>
                                    <input type="date" id="dropoff_date" name="dropoff_date" class="form-control" min="<?php echo $today; ?>" max="<?php echo $deadline; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="dropoff_time">Select Drop-off Time</label>
                                    <select id="dropoff_time" name="dropoff_time" class="form-control" required>
                                        <option value="">Select a time</option>
                                        <option value="09:00">9:00 AM</option>
                                        <option value="10:00">10:00 AM</option>
                                        <option value="11:00">11:00 AM</option>
                                        <option value="12:00">12:00 PM</option>
                                        <option value="13:00">1:00 PM</option>
                                        <option value="14:00">2:00 PM</option>
                                        <option value="15:00">3:00 PM</option>
                                        <option value="16:00">4:00 PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Donor Information Section -->
                    <div class="donation-checkout__section">
                        <h2>Donor Information</h2>
                        
                        <div class="anonymous-option">
                            <input type="checkbox" id="anonymous" name="anonymous" value="1">
                            <label for="anonymous">Make this donation anonymous</label>
                        </div>
                        
                        <div class="donor-info">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo $_SESSION['user_name'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control" value="<?php echo $_SESSION['user_email'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes Section -->
                    <div class="donation-checkout__section notes-section">
                        <h2>Additional Notes</h2>
                        
                        <div class="form-group">
                            <label for="notes">Add a message (optional)</label>
                            <textarea id="notes" name="notes" class="form-control" placeholder="Add details about your donation or any specific instructions"><?php echo $data['notes'] ?? ''; ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Terms Agreement -->
                    <div class="donation-checkout__terms-agreement">
                        <input type="checkbox" id="terms_agreement" name="terms_agreement" required>
                        <label for="terms_agreement">
                            I agree to the <a href="#" class="donation-checkout__terms-link">Terms and Conditions</a> and <a href="#" class="donation-checkout__terms-link">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="donation-checkout__actions">
                        <a href="<?php echo URLROOT; ?>/donors/details/<?php echo $data['request']->RequestID; ?>" class="donation-checkout__back">
                            <i class="fas fa-arrow-left"></i> Back to Details
                        </a>
                        
                        <button type="submit" class="donation-checkout__submit">
                            Schedule Drop-off <i class="fas fa-calendar-check"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Right Column - Donation Summary -->
            <div class="donation-checkout__summary">
                <h2>Donation Summary</h2>
                
                <!-- Request Details -->
                <div class="donation-request-details">
                    <?php
                    // Try common image extensions
                    $extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    $imageFound = false;
                    $imagePath = URLROOT . '/img/placeholder.jpg'; // Default to placeholder
                    
                    foreach($extensions as $ext) {
                        $testPath = APPROOT . '/../public/uploads/requests/' . $data['request']->RequestID . '.' . $ext;
                        if(file_exists($testPath)) {
                            $imagePath = URLROOT . '/uploads/requests/' . $data['request']->RequestID . '.' . $ext;
                            $imageFound = true;
                            break;
                        }
                    }
                    ?>
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $data['request']->Title; ?>" class="donation-request-image">
                    
                    <div class="donation-request-title"><?php echo $data['request']->Title; ?></div>
                    <span class="donation-request-category"><?php echo $data['request']->Category; ?></span>
                    <div class="donation-request-recipient">
                        Requested by: <?php echo isset($data['request']->RecipientName) ? $data['request']->RecipientName : 'Anonymous'; ?>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="donation-checkout__progress">
                        <?php 
                            $percentage = 0;
                            if(isset($data['request']->QuantityReceived) && isset($data['request']->QuantityNeeded) && $data['request']->QuantityNeeded > 0) {
                                $percentage = ($data['request']->QuantityReceived / $data['request']->QuantityNeeded) * 100;
                            }
                        ?>
                        <div class="donation-checkout__progress-bar">
                            <div class="donation-checkout__progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                        <div class="donation-checkout__progress-stats">
                            <span><?php echo ($data['request']->QuantityReceived ?? 0); ?> received</span>
                            <span><?php echo round($percentage); ?>% of goal</span>
                        </div>
                    </div>
                </div>
                
                <!-- Donation Summary (will update via JavaScript) -->
                <div class="donation-checkout__summary-item">
                    <span>Item</span>
                    <span><?php echo isset($data['itemDetails']) ? $data['itemDetails']->ItemName : 'Items'; ?></span>
                </div>
                
                <div class="donation-checkout__summary-item">
                    <span>Quantity</span>
                    <span id="summaryQuantity">1</span>
                </div>
                
                <div class="donation-checkout__summary-item">
                    <span>Drop-off Date</span>
                    <span id="summaryDate">Not selected yet</span>
                </div>
                
                <div class="donation-checkout__summary-item">
                    <span>Drop-off Time</span>
                    <span id="summaryTime">Not selected yet</span>
                </div>
                
                <div class="donation-checkout__summary-total">
                    <span>Total Items</span>
                    <span id="summaryTotal">1</span>
                </div>
                
                <!-- Info Notes -->
                <div class="donation-checkout__notes">
                    <p><i class="fas fa-info-circle"></i> Please ensure the items are in good condition and bring them at your scheduled time.</p>
                    <p><i class="fas fa-exclamation-circle"></i> You will receive a confirmation email with your drop-off details.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for dynamic behavior -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity input
        const quantityInput = document.getElementById('quantity');
        const summaryQuantity = document.getElementById('summaryQuantity');
        const summaryTotal = document.getElementById('summaryTotal');
        
        quantityInput.addEventListener('input', function() {
            const quantity = this.value || 1;
            summaryQuantity.textContent = quantity;
            summaryTotal.textContent = quantity;
        });
        
        // Date and time selection
        const dateInput = document.getElementById('dropoff_date');
        const timeInput = document.getElementById('dropoff_time');
        const summaryDate = document.getElementById('summaryDate');
        const summaryTime = document.getElementById('summaryTime');
        
        dateInput.addEventListener('change', function() {
            if (this.value) {
                const date = new Date(this.value);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                summaryDate.textContent = date.toLocaleDateString('en-US', options);
            } else {
                summaryDate.textContent = 'Not selected yet';
            }
        });
        
        timeInput.addEventListener('change', function() {
            if (this.value) {
                const time = this.value.split(':');
                const hour = parseInt(time[0]);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const hour12 = hour % 12 || 12;
                summaryTime.textContent = `${hour12}:00 ${ampm}`;
            } else {
                summaryTime.textContent = 'Not selected yet';
            }
        });
        
        // Anonymous donation toggle
        const anonymousCheckbox = document.getElementById('anonymous');
        const donorInfoFields = document.querySelectorAll('.donor-info input');
        
        anonymousCheckbox.addEventListener('change', function() {
            const isDisabled = this.checked;
            
            donorInfoFields.forEach(field => {
                if (isDisabled) {
                    field.setAttribute('disabled', 'disabled');
                } else {
                    field.removeAttribute('disabled');
                }
            });
        });
        
        // Validate weekend selection
        dateInput.addEventListener('change', function() {
            if (this.value) {
                const date = new Date(this.value);
                const day = date.getDay();
                
                // Sunday is 0, Saturday is 6
                if (day === 0) {
                    alert('Sorry, drop-offs are not available on Sundays. Please select another day.');
                    this.value = '';
                    summaryDate.textContent = 'Not selected yet';
                }
                
                // Adjust time dropdown based on day
                const timeOptions = timeInput.querySelectorAll('option');
                
                if (day === 6) { // Saturday
                    // Only show 10am-2pm options
                    timeOptions.forEach(option => {
                        const value = option.value;
                        if (value && (value < '10:00' || value > '14:00')) {
                            option.style.display = 'none';
                        } else {
                            option.style.display = 'block';
                        }
                    });
                } else {
                    // Show all time options
                    timeOptions.forEach(option => {
                        option.style.display = 'block';
                    });
                }
            }
        });
        
        // Initialize summary with default quantity
        summaryQuantity.textContent = quantityInput.value || 1;
        summaryTotal.textContent = quantityInput.value || 1;
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>