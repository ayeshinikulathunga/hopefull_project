<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] . ' - ' . SITENAME : SITENAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/donor.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/donation-checkout.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/donation.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/profile.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/donor_feedback.css">

</head>
<body>
    <!-- User Sidebar -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <div class="user-sidebar" id="user-sidebar">
        <div class="sidebar-content">
            <div class="sidebar-header">
                <h3>Donor Dashboard</h3>
                <button id="close-sidebar" class="btn btn-icon">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-menu">
                <a href="<?php echo URLROOT; ?>/donors/dashboard" class="sidebar-menu-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="<?php echo URLROOT; ?>/profile" class="sidebar-menu-item">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                <a href="<?php echo URLROOT; ?>/donations/myDonations" class="sidebar-menu-item">
                    <i class="fas fa-gift"></i> My Donations
                </a>
                <a href="<?php echo URLROOT; ?>/donations/pendingDonations" class="sidebar-menu-item">
                    <i class="fas fa-heart"></i> Pending Donations
                </a>
                <a href="<?php echo URLROOT; ?>/donors/events" class="sidebar-menu-item">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
               
                </a>
                <a href="<?php echo URLROOT; ?>/donors/feedback" class="sidebar-menu-item">
                    <i class="fas fa-comment-alt"></i> Impact Reports
                </a>
                <a href="<?php echo URLROOT; ?>/donors/inquiries" class="sidebar-menu-item">
                    <i class="fas fa-question-circle"></i> Inquiries
                </a>
                <a href="<?php echo URLROOT; ?>/marketplace" class="sidebar-menu-item">
                    <i class="fas fa-store"></i> Marketplace
                </a>
                <a href="<?php echo URLROOT; ?>/marketplace/orders" class="sidebar-menu-item">
                    <i class="fas fa-shopping-bag"></i> My Orders
                </a>
            </div>

            <div class="sidebar-footer">
                <a href="<?php echo URLROOT; ?>/users/logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <header class="main-header">
        <nav class="main-nav container">
            <div class="nav-brand">
                <a href="<?php echo URLROOT; ?>">
                    <img src="<?php echo URLROOT; ?>/images/logo.png" alt="Hopefull Logo" class="nav-logo">
                </a>
            </div>
            
            <div class="nav-links">
                <a href="<?php echo URLROOT; ?>/donors/dashboard">Home</a>
                <a href="<?php echo URLROOT; ?>/donors/allRequests">Donations</a>
                <a href="<?php echo URLROOT; ?>/donors/events">Events</a>
                <a href="<?php echo URLROOT; ?>/marketplace">Marketplace</a>


                
                
                <!-- Donation Icon with Pending Donations Badge -->
                <a href="<?php echo URLROOT; ?>/donations/pendingDonations" class="donation-icon">
                    <i class="fas fa-heart"></i>
                    <?php 
                    // Get count of pending donations
                    $db = new Database;
                    $db->query('SELECT COUNT(*) as pendingCount FROM donations 
                             WHERE DonorID = :donorId AND Status = "Pending"');
                    $db->bind(':donorId', $_SESSION['donor_id']);
                    $result = $db->single();
                    
                    if($result && $result->pendingCount > 0): 
                    ?>
                        <span class="badge"><?php echo $result->pendingCount; ?></span>
                    <?php endif; ?>
                </a>
                
                <!-- User Icon to Toggle Sidebar -->
                <a href="#" id="toggle-sidebar" class="user-icon">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </nav>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('user-sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const toggleButton = document.getElementById('toggle-sidebar');
            const closeButton = document.getElementById('close-sidebar');

            // Open sidebar
            toggleButton.addEventListener('click', (e) => {
                e.preventDefault();
                sidebar.classList.add('open');
                sidebarOverlay.classList.add('show');
            });

            // Close sidebar
            const closeSidebar = () => {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('show');
            };

            closeButton.addEventListener('click', closeSidebar);
            sidebarOverlay.addEventListener('click', closeSidebar);
        });
    </script>