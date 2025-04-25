<?php require APPROOT . '/views/includes/headers/recipient_header.php'; ?>

<!-- Include FullCalendar library in the header -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>

<div class="container donor-profile">
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
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo $data['recipient']->Address; ?></p>
                            <p><i class="fas fa-building"></i> Type: <?php echo $data['recipient']->OrganizationType; ?></p>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <button type="button" class="btn btn-outline" id="editProfileBtn">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                        <button type="button" class="btn btn-outline" id="changePasswordBtn">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                        <button type="button" class="btn btn-danger" id="deleteAccountBtn">
                            <i class="fas fa-trash-alt"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donation Stats Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-chart-bar"></i> Request & Donation Stats</h2>
                </div>
                <div class="profile-card-body">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-hands-helping"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Requests</h3>
                                <div class="stat-value"><?php echo $data['stats']->totalRequests ?? 0; ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Completed</h3>
                                <div class="stat-value"><?php echo $data['stats']->completedRequests ?? 0; ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Pending</h3>
                                <div class="stat-value"><?php echo $data['stats']->pendingRequests ?? 0; ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-donate"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Donations Received</h3>
                                <div class="stat-value"><?php echo $data['stats']->totalDonationsReceived ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Metrics Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-comments"></i> Feedback Metrics</h2>
                </div>
                <div class="profile-card-body">
                    <?php if(isset($data['feedbackMetrics']) && $data['feedbackMetrics']->totalFeedback > 0): ?>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>Average Rating</h3>
                                    <div class="stat-value"><?php echo $data['feedbackMetrics']->averageRating; ?>/5</div>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-thumbs-up"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>Positive Feedback</h3>
                                    <div class="stat-value"><?php echo $data['feedbackMetrics']->positiveCount; ?></div>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>Impact Reports</h3>
                                    <div class="stat-value"><?php echo $data['feedbackMetrics']->impactReports; ?></div>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-comment-alt"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>Total Feedback</h3>
                                    <div class="stat-value"><?php echo $data['feedbackMetrics']->totalFeedback; ?></div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <p>No feedback data available yet. Feedback will appear here as donors respond to your requests.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Donation Summary Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-money-bill-wave"></i> Donation Summary</h2>
                </div>
                <div class="profile-card-body">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Monetary</h3>
                                <div class="stat-value">LKR <?php echo number_format($data['stats']->monetaryTotal ?? 0, 2); ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Items</h3>
                                <div class="stat-value"><?php echo $data['stats']->nonMonetaryTotal ?? 0; ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Unique Donors</h3>
                                <div class="stat-value"><?php echo $data['stats']->uniqueDonors ?? 0; ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Active Requests</h3>
                                <div class="stat-value"><?php echo $data['stats']->pendingRequests ?? 0; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donation Calendar Section -->
        <div class="profile-section full-width">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-calendar-alt"></i> Request & Donation Calendar</h2>
                </div>
                <div class="profile-card-body">
                    <div class="calendar-container">
                        <div id="donationCalendar"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Deadlines Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-hourglass-half"></i> Upcoming Deadlines</h2>
                </div>
                <div class="profile-card-body">
                    <?php if(count($data['upcomingDeadlines']) > 0) : ?>
                        <div class="upcoming-donations">
                            <?php foreach($data['upcomingDeadlines'] as $deadline) : ?>
                                <div class="donation-reminder">
                                    <div class="reminder-date">
                                        <div class="reminder-month"><?php echo date('M', strtotime($deadline->Deadline)); ?></div>
                                        <div class="reminder-day"><?php echo date('d', strtotime($deadline->Deadline)); ?></div>
                                    </div>
                                    <div class="reminder-details">
                                        <h3><?php echo $deadline->Title; ?></h3>
                                        <p>
                                            <i class="fas fa-clock"></i> <?php echo $deadline->DaysRemaining; ?> days remaining 
                                            <i class="fas fa-tags ml-3"></i> <?php echo $deadline->Category; ?>
                                        </p>
                                        <div class="reminder-actions">
                                            <a href="<?php echo URLROOT; ?>/recipients/viewRequest/<?php echo $deadline->RequestID; ?>" class="btn btn-outline">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="no-data">
                            <p>You don't have any upcoming deadlines for your active requests.</p>
                            <a href="<?php echo URLROOT; ?>/recipients/createRequest" class="btn btn-primary btn-sm mt-3">Create New Request</a>
                        </div>
                    <?php endif; ?>
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
                    <?php if(count($data['recentDonations']) > 0) : ?>
                        <div class="upcoming-donations">
                            <?php foreach($data['recentDonations'] as $donation) : ?>
                                <div class="donation-reminder">
                                    <div class="reminder-date" style="background-color: <?php echo $donation->DonationType == 'Monetary' ? 'var(--primary-color)' : 'var(--secondary-color)'; ?>">
                                        <div class="reminder-month">
                                            <?php if($donation->DonationType == 'Monetary'): ?>
                                                <i class="fas fa-money-bill"></i>
                                            <?php else: ?>
                                                <i class="fas fa-box"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="reminder-day"><?php echo $donation->DonationType == 'Monetary' ? 'LKR' : 'Items'; ?></div>
                                    </div>
                                    <div class="reminder-details">
                                        <h3><?php echo $donation->Title; ?></h3>
                                        <p>
                                            <i class="fas fa-user"></i> <?php echo $donation->DonorName; ?> 
                                            <i class="fas fa-calendar ml-3"></i> <?php echo date('M d, Y', strtotime($donation->DonationDate)); ?>
                                        </p>
                                        <p>
                                            <?php if($donation->DonationType == 'Monetary'): ?>
                                                <i class="fas fa-hand-holding-usd"></i> LKR <?php echo number_format($donation->Amount, 2); ?>
                                            <?php else: ?>
                                                <i class="fas fa-box-open"></i> <?php echo $donation->QuantityDonated; ?> items
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="<?php echo URLROOT; ?>/recipients/donations" class="btn btn-outline">View All Donations</a>
                        </div>
                    <?php else : ?>
                        <div class="no-data">
                            <p>No donations have been received yet.</p>
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
        <form action="<?php echo URLROOT; ?>/recipientProfile/updateProfile" method="POST">
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
                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $data['recipient']->Address; ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
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
        <form action="<?php echo URLROOT; ?>/recipientProfile/changePassword" method="POST">
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
                <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                <button type="submit" class="btn btn-primary">Change Password</button>
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
        <form action="<?php echo URLROOT; ?>/recipientProfile/deleteAccount" method="POST">
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> Warning: This action cannot be undone!
                </div>
                <p>Are you sure you want to delete your account? This will permanently remove all your data, including your donation requests and history.</p>
                <div class="form-group">
                    <label for="confirmation_password">Enter your password to confirm deletion:</label>
                    <input type="password" class="form-control" id="confirmation_password" name="confirmation_password" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete Account</button>
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
    const closeButtons = document.querySelectorAll('.close-modal, .modal-close-btn');
    
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
        const calendarEl = document.getElementById('donationCalendar');
        if (calendarEl) {
            calendarEl.innerHTML = '<div class="calendar-error">Calendar library not loaded. Please refresh the page or contact support.</div>';
        }
        return;
    }
    
    const calendarEl = document.getElementById('donationCalendar');
    
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
            // Calendar event display configuration
            eventDisplay: 'block',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: 'short'
            },
            eventClick: function(info) {
                console.log('Event clicked:', info.event.title);
                if (info.event.url) {
                    window.location.href = info.event.url;
                    return false;
                }
            },
            // Custom styling for different event types
            eventClassNames: function(arg) {
                // Add classes based on event ID prefix
                const eventId = arg.event.id || '';
                if (eventId.startsWith('req_')) {
                    return ['calendar-event-deadline'];
                } else if (eventId.startsWith('drop_')) {
                    return ['calendar-event-dropoff'];
                }
                return [];
            }
        });
        
        calendar.render();
        console.log('Calendar rendered');
        
        // Load calendar data from API
        fetch('<?php echo URLROOT; ?>/recipientProfile/getCalendarData')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Calendar data loaded:', data);
                
                // Add events to calendar
                calendar.addEventSource(data);
            })
            .catch(error => {
                console.error('Error fetching calendar data:', error);
                calendarEl.insertAdjacentHTML('beforeend', 
                    '<div class="calendar-error">Failed to load calendar events. Please try again later.</div>'
                );
            });
    } else {
        console.error('Calendar element not found. Check if the ID exists in your HTML.');
    }
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>