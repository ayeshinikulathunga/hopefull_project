<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] . ' - ' . SITENAME : SITENAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
    <style>
        /* User Sidebar Styles */

    </style>
</head>
<body>
    <!-- User Sidebar -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <div class="user-sidebar" id="user-sidebar">
        <div class="sidebar-content">
            <div class="sidebar-header">
                <h3>Recipient Dashboard</h3>
                <button id="close-sidebar" class="btn btn-icon">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-menu">
                <a href="<?php echo URLROOT; ?>/recipients/dashboard" class="sidebar-menu-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="<?php echo URLROOT; ?>/recipients/profile" class="sidebar-menu-item">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                <a href="<?php echo URLROOT; ?>/requests/create" class="sidebar-menu-item">
                    <i class="fas fa-plus-circle"></i> Create Request
                </a>
                <a href="<?php echo URLROOT; ?>/requests/manage" class="sidebar-menu-item">
                    <i class="fas fa-clipboard-list"></i> My Requests
                </a>
                <a href="<?php echo URLROOT; ?>/feedback/create" class="sidebar-menu-item">
                    <i class="fas fa-comment-alt"></i> Send Feedback
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
                <a href="<?php echo URLROOT; ?>">Home</a>
                <a href="<?php echo URLROOT; ?>/requests">My Requests</a>
                <a href="<?php echo URLROOT; ?>/about">About</a>
                
                <!-- Notification Icon with Pending Updates Badge -->
                <a href="<?php echo URLROOT; ?>/notifications" class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <?php 
                    // Fetch pending notifications count
                    $pendingNotificationsCount = isset($data['pendingNotifications']) ? $data['pendingNotifications'] : 0; 
                    if ($pendingNotificationsCount > 0): ?>
                        <span class="badge"><?php echo $pendingNotificationsCount; ?></span>
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
</body>
</html>