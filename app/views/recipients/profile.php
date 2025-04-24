<?php require APPROOT . '/views/includes/headers/recipient_header.php'; ?>

<!-- Include FullCalendar library in the header -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>

<div class="container recipient-profile">
    <div class="profile-header">
        <h1><?php echo $data['title']; ?></h1>
    </div>

    <?php flash('profile_success'); ?>
    <?php flash('profile_error'); ?>
    
    <div class="profile-grid">
        <!-- Profile Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-user-circle"></i> Account Information</h2>
                </div>
                <div class="profile-card-body">
                    <div class="profile-info">
                        <div class="profile-avatar">
                            <?php
                            // Create initials from first and last name
                            $initials = mb_substr($data['recipient']->FirstName, 0, 1) . mb_substr($data['recipient']->LastName, 0, 1);
                            ?>
                            <div class="avatar-circle">
                                <span class="avatar-initials"><?php echo strtoupper($initials); ?></span>
                            </div>
                        </div>
                        <div class="profile-details">
                            <h3><?php echo $data['recipient']->FirstName . ' ' . $data['recipient']->LastName; ?></h3>
                            <p><i class="fas fa-envelope"></i> <?php echo $data['user']->Email; ?></p>
                            <p><i class="fas fa-phone"></i> <?php echo $data['recipient']->ContactNumber; ?></p>
                            <p><i class="fas fa-box-open"></i> Donations Received: <?php echo $data['recipient']->TotalDonationsReceived; ?></p>
                            <p><i class="fas fa-money-bill"></i> Total: LKR <?php echo number_format($data['recipient']->TotalMonetaryDonations, 2); ?></p>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <button type="button" class="btn2 btn2-outline" id="editProfileBtn">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                        <button type="button" class="btn2 btn2-outline" id="changePasswordBtn">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                        <button type="button" class="btn2 btn2-danger" id="deleteAccountBtn">
                            <i class="fas fa-trash-alt"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Stats Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-chart-bar"></i> Request Stats</h2>
                </div>
                <div class="profile-card-body">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Requests</h3>
                                <div class="stat-value"><?php echo isset($data['stats']->TotalRequests) ? $data['stats']->TotalRequests : 0; ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Completed</h3>
                                <div class="stat-value"><?php echo isset($data['stats']->CompletedRequests) ? $data['stats']->CompletedRequests : 0; ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info">
                                <h3>In Progress</h3>
                                <div class="stat-value"><?php echo isset($data['stats']->InProgressRequests) ? $data['stats']->InProgressRequests : 0; ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Contributors</h3>
                                <div class="stat-value"><?php echo isset($data['stats']->TotalContributors) ? $data['stats']->TotalContributors : 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Donations Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-gift"></i> Recent Donations</h2>
                </div>
                <div class="profile-card-body">
                <?php if(isset($data['recentDonations']) && is_array($data['recentDonations']) && count($data['recentDonations']) > 0) : ?>
                        <div class="recent-donations">
                            <?php foreach($data['recentDonations'] as $donation) : ?>
                                <div class="donation-item">
                                    <div class="donation-icon">
                                        <?php if($donation->DonationType == 'monetary') : ?>
                                            <i class="fas fa-money-bill"></i>
                                        <?php else : ?>
                                            <i class="fas fa-box"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="donation-details">
                                        <h3>
                                            <?php if($donation->DonationType == 'monetary') : ?>
                                                LKR <?php echo number_format($donation->Amount, 2); ?>
                                            <?php else : ?>
                                                <?php echo $donation->ItemName; ?>
                                            <?php endif; ?>
                                        </h3>
                                        <p>
                                            <i class="fas fa-user"></i> 
                                            <?php echo $donation->AnonymousDonation ? 'Anonymous Donor' : $donation->DonorName; ?>
                                            <span class="donation-date"><?php echo date('M d, Y', strtotime($donation->DonationDate)); ?></span>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="no-data">
                            <p>You haven't received any donations yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

                 
        <!-- Donation Request Calendar Section -->
        <div class="profile-section full-width">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-calendar-alt"></i> Request Calendar</h2>
                </div>
                <div class="profile-card-body">
                    <div class="calendar-container">
                        <div id="requestCalendar"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Request History Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-history"></i> Request History</h2>
                </div>
                <div class="profile-card-body">
                <?php if(isset($data['requestHistory']) && is_array($data['requestHistory']) && count($data['requestHistory']) > 0) : ?>
                        <div class="history-list">
                            <?php foreach($data['requestHistory'] as $request) : ?>
                                <div class="history-item">
                                    <div class="history-date">
                                        <div class="history-month"><?php echo date('M', strtotime($request->DateCreated)); ?></div>
                                        <div class="history-day"><?php echo date('d', strtotime($request->DateCreated)); ?></div>
                                    </div>
                                    <div class="history-details">
                                        <h3><?php echo $request->Title; ?></h3>
                                        <p>
                                            <span class="status-badge <?php echo strtolower($request->Status); ?>">
                                                <?php echo ucfirst($request->Status); ?>
                                            </span>
                                            <i class="fas fa-users ml-3"></i> <?php echo $request->DonorCount; ?> donors contributed
                                        </p>
                                        <div class="history-actions">
                                            <a href="<?php echo URLROOT; ?>/recipients/requests<?php echo $request->RequestID; ?>" class="btn2 btn2-outline btn2-sm">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="no-data">
                            <p>You haven't created any requests yet.</p>
                            <a href="<?php echo URLROOT; ?>/recipients/createRequest" class="btn2 btn2-primary btn2-sm mt-4">Create New Request</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Modals -->
<!-- Edit Profile Modal -->
<div class="custom-modal" id="editProfileModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Edit Profile</h5>
            <span class="close-modal">&times;</span>
        </div>
        <form action="<?php echo URLROOT; ?>/profile/updateProfile" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $data['recipient']->FirstName; ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $data['recipient']->LastName; ?>" required>
                </div>
                <div class="form-group">
                    <label for="contactNumber">Contact Number</label>
                    <input type="text" class="form-control" id="contactNumber" name="contactNumber" value="<?php echo $data['recipient']->ContactNumber; ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo $data['recipient']->Address; ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn2 btn2-secondary modal-close-btn2">Cancel</button>
                <button type="submit" class="btn2 btn2-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div class="custom-modal" id="changePasswordModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Change Password</h5>
            <span class="close-modal">&times;</span>
        </div>
        <form action="<?php echo URLROOT; ?>/profile/changePassword" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                    <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn2 btn2-secondary modal-close-btn2">Cancel</button>
                <button type="submit" class="btn2 btn2-primary">Change Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="custom-modal" id="deleteAccountModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Delete Account</h5>
            <span class="close-modal">&times;</span>
        </div>
        <form action="<?php echo URLROOT; ?>/profile/deleteAccount" method="POST">
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> Warning: This action cannot be undone!
                </div>
                <p>Are you sure you want to delete your account? This will permanently remove all your data, including your request history and donation records.</p>
                <div class="form-group">
                    <label for="confirmation_password">Enter your password to confirm deletion:</label>
                    <input type="password" class="form-control" id="confirmation_password" name="confirmation_password" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn2 btn2-secondary modal-close-btn2">Cancel</button>
                <button type="submit" class="btn2 btn2-danger">Delete Account</button>
            </div>
        </form>
    </div>
</div>

<!-- Vanilla JS for handling custom modals and calendar -->
<script>
// Modal handling with vanilla JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Get all modal elements
    const editProfileModal = document.getElementById('editProfileModal');
    const changePasswordModal = document.getElementById('changePasswordModal');
    const deleteAccountModal = document.getElementById('deleteAccountModal');
    
    // Get all buttons that open the modals
    const editProfileBtn = document.getElementById('editProfileBtn');
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const deleteAccountBtn = document.getElementById('deleteAccountBtn');
    
    // Get all close buttons
    const closeButtons = document.querySelectorAll('.close-modal, .modal-close-btn2');
    
    // Function to open a modal
    function openModal(modal) {
        if (!modal) return;
        modal.style.display = 'block';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }
    
    // Function to close a modal
    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
    
    // Add click event listeners to buttons
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', () => openModal(editProfileModal));
    }
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', () => openModal(changePasswordModal));
    }
    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', () => openModal(deleteAccountModal));
    }
    
    // Add click event listeners to close buttons
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.custom-modal');
            closeModal(modal);
        });
    });
    
    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('custom-modal')) {
            closeModal(event.target);
        }
    });
    
    // Initialize Calendar
    // Check if FullCalendar is loaded
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar is not loaded. Make sure to include the library.');
        
        // Add a message to the calendar container
        const calendarEl = document.getElementById('requestCalendar');
        if (calendarEl) {
            calendarEl.innerHTML = '<div class="calendar-error">Calendar library not loaded. Please refresh the page or contact support.</div>';
        }
        return;
    }
    
    const calendarEl = document.getElementById('requestCalendar');
    
    if (calendarEl) {
        console.log('Calendar element found, initializing...');
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth'
            },
            events: function(info, successCallback, failureCallback) {
                fetch('<?php echo URLROOT; ?>/profile/getCalendarData')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Calendar data loaded:', data);
                        successCallback(data);
                    })
                    .catch(error => {
                        console.error('Error fetching calendar data:', error);
                        failureCallback(error);
                        calendarEl.insertAdjacentHTML('beforeend', 
                            '<div class="calendar-error">Failed to load calendar events. Please try again later.</div>'
                        );
                    });
            },
            eventDidMount: function(info) {
                // Add custom styles or tooltips for events
                const eventType = info.event.extendedProps.type;
                if (eventType === 'deadline') {
                    info.el.classList.add('calendar-event-deadline');
                } else if (eventType === 'creation') {
                    info.el.classList.add('calendar-event-creation');
                }
            },
            eventClick: function(info) {
                if (info.event.url) {
                    window.location.href = info.event.url;
                    return false;
                }
            }
        });
        
        calendar.render();
        console.log('Calendar rendered');
    } else {
        console.error('Calendar element not found. Check if the ID exists in your HTML.');
    }
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>