<?php require APPROOT . '/views/includes/headers/donor_header.php'; ?>

<!-- Include FullCalendar library in the header -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>

<!-- Custom styles for modals and other elements -->
<style>

</style>

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
                            $initials = mb_substr($data['donor']->FirstName, 0, 1) . mb_substr($data['donor']->LastName, 0, 1);
                            ?>
                            <div class="avatar-circle">
                                <span class="avatar-initials"><?php echo strtoupper($initials); ?></span>
                            </div>
                        </div>
                        <div class="profile-details">
                            <h3><?php echo $data['donor']->FirstName . ' ' . $data['donor']->LastName; ?></h3>
                            <p><i class="fas fa-envelope"></i> <?php echo $data['user']->Email; ?></p>
                            <p><i class="fas fa-phone"></i> <?php echo $data['donor']->ContactNumber; ?></p>
                            <p><i class="fas fa-gift"></i> Donations: <?php echo $data['donor']->DonationCount; ?></p>
                            <p><i class="fas fa-money-bill"></i> Total: LKR <?php echo number_format($data['donor']->TotalDonations, 2); ?></p>
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

        <!-- Donor Stats Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-chart-bar"></i> Donation Stats</h2>
                </div>
                <div class="profile-card-body">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-donate"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Donations</h3>
                                <div class="stat-value"><?php echo $data['donor']->DonationCount; ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Amount</h3>
                                <div class="stat-value">LKR <?php echo number_format($data['donor']->TotalDonations, 2); ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Your Ranking</h3>
                                <div class="stat-value">#<?php echo $data['ranking']; ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Badges Earned</h3>
                                <div class="stat-value"><?php echo count($data['badges']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Badges Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-award"></i> Your Badges</h2>
                </div>
                <div class="profile-card-body">
                    <?php if(count($data['badges']) > 0) : ?>
                        <div class="badges-grid">
                            <?php foreach($data['badges'] as $badge) : ?>
                                <div class="badge-card" title="<?php echo $badge->Description; ?>">
                                    <div class="badge-icon">
                                        <?php if($badge->BadgeImage) : ?>
                                            <img src="<?php echo URLROOT; ?>/public/img/badges/<?php echo $badge->BadgeImage; ?>" alt="<?php echo $badge->BadgeName; ?>">
                                        <?php else : ?>
                                            <i class="fas fa-medal"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="badge-info">
                                        <h3><?php echo $badge->BadgeName; ?></h3>
                                        <p><?php echo date('M d, Y', strtotime($badge->AwardedDate)); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="no-badges">
                            <p>You haven't earned any badges yet. Start donating to earn badges!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Leaderboard Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-trophy"></i> Donor Leaderboard</h2>
                </div>
                <div class="profile-card-body">
                    <div class="leaderboard">
                        <?php if(count($data['topDonors']) > 0) : ?>
                            <table class="leaderboard-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Donor</th>
                                        <th>Donations</th>
                                        <th>Amount</th>
                                        <th>Badges</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data['topDonors'] as $index => $donor) : ?>
                                        <tr class="<?php echo ($donor->DonorID === $data['donor']->DonorID) ? 'current-donor' : ''; ?>">
                                            <td class="rank">#<?php echo $index + 1; ?></td>
                                            <td>
                                                <?php 
                                                // Display name or "Anonymous" based on preference
                                                if ($donor->DonorID === $data['donor']->DonorID) {
                                                    echo $donor->FirstName . ' ' . $donor->LastName . ' <span class="current-user-tag">(You)</span>';
                                                } else {
                                                    echo $donor->FirstName . ' ' . $donor->LastName;
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $donor->DonationCount; ?></td>
                                            <td>LKR <?php echo number_format($donor->TotalDonations, 2); ?></td>
                                            <td>
                                                <div class="badge-count"><?php echo $donor->BadgeCount; ?></div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="no-data">
                                <p>No donation data available for the leaderboard.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donation Calendar Section -->
        <div class="profile-section full-width">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-calendar-alt"></i> Donation Calendar</h2>
                </div>
                <div class="profile-card-body">
                    <div class="calendar-container">
                        <div id="donationCalendar"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Donations Section -->
        <div class="profile-section">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h2><i class="fas fa-clock"></i> Upcoming Donations</h2>
                </div>
                <div class="profile-card-body">
                    <?php if(count($data['upcomingDonations']) > 0) : ?>
                        <div class="upcoming-donations">
                            <?php foreach($data['upcomingDonations'] as $donation) : ?>
                                <div class="donation-reminder">
                                    <div class="reminder-date">
                                        <div class="reminder-month"><?php echo date('M', strtotime($donation->DropOffDate)); ?></div>
                                        <div class="reminder-day"><?php echo date('d', strtotime($donation->DropOffDate)); ?></div>
                                    </div>
                                    <div class="reminder-details">
                                        <h3><?php echo isset($donation->ItemName) ? $donation->ItemName : ''; ?> - <?php echo $donation->Title; ?></h3>
                                        <p>
                                            <i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($donation->DropOffTime)); ?> 
                                            <i class="fas fa-map-marker-alt ml-3"></i> <?php echo isset($donation->DropOffLocation) ? $donation->DropOffLocation : 'Location not specified'; ?>
                                        </p>
                                        <div class="reminder-actions">
                                            <a href="<?php echo URLROOT; ?>/donations/donationDetails/<?php echo $donation->DonationID; ?>" class="btn btn-outline">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="no-data">
                            <p>You don't have any upcoming donations scheduled.</p>
                            <a href="<?php echo URLROOT; ?>/donors/allRequests" class="btn btn-primary btn-sm mt-3">Find Donation Opportunities</a>
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
                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $data['donor']->FirstName; ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $data['donor']->LastName; ?>" required>
                </div>
                <div class="form-group">
                    <label for="contactNumber">Contact Number</label>
                    <input type="text" class="form-control" id="contactNumber" name="contactNumber" value="<?php echo $data['donor']->ContactNumber; ?>" required>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="anonymousPreference" name="anonymousPreference" <?php echo ($data['donor']->AnonymousPreference) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="anonymousPreference">Make donations anonymous by default</label>
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
        <form action="<?php echo URLROOT; ?>/profile/deleteAccount" method="POST">
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> Warning: This action cannot be undone!
                </div>
                <p>Are you sure you want to delete your account? This will permanently remove all your data, including your donation history and badges.</p>
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
        
        // Create a test event to see if calendar renders properly
        const today = new Date();
        const testEvents = [
            {
                title: 'Test Event',
                start: today.toISOString().split('T')[0],
                className: 'calendar-event-donation'
            }
        ];
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth'
            },
            // First try with test events to check rendering
            events: testEvents,
            eventClick: function(info) {
                console.log('Event clicked:', info.event.title);
                if (info.event.url) {
                    window.location.href = info.event.url;
                    return false;
                }
            }
        });
        
        calendar.render();
        console.log('Calendar rendered with test event');
        
        // After confirming calendar renders, load real events
        fetch('<?php echo URLROOT; ?>/profile/getCalendarData')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Calendar data loaded:', data);
                
                // Remove test events
                calendar.removeAllEvents();
                
                // Add real events
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



