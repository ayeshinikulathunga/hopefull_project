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
                <h3>My Account</h3>
                <button id="close-sidebar" class="btn btn-icon">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-menu">
                <a href="<?php echo URLROOT; ?>/profile" class="sidebar-menu-item">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                <a href="<?php echo URLROOT; ?>/events/calendar" class="sidebar-menu-item">
                    <i class="fas fa-calendar-alt"></i> Event Calendar
                </a>
                <a href="<?php echo URLROOT; ?>/donations" class="sidebar-menu-item">
                    <i class="fas fa-heart"></i> My Donations
                </a>
                <!-- Marketplace-specific sidebar options -->
                <a href="<?php echo URLROOT; ?>/marketplace/products" class="sidebar-menu-item">
                    <i class="fas fa-box"></i> All Products
                </a>
                <a href="<?php echo URLROOT; ?>/marketplace/cart" class="sidebar-menu-item">
                    <i class="fas fa-shopping-cart"></i> My Cart
                    <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) : ?>
                        <span class="badge"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo URLROOT; ?>/marketplace/orders" class="sidebar-menu-item">
                    <i class="fas fa-shipping-fast"></i> My Orders
                </a>
                <a href="<?php echo URLROOT; ?>/settings" class="sidebar-menu-item">
                    <i class="fas fa-cog"></i> Settings
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
                <a href="<?php echo URLROOT; ?>/marketplace" <?php echo (strpos($_SERVER['REQUEST_URI'], '/marketplace') !== false) ? 'class="active"' : ''; ?>>Marketplace</a>
                <a href="<?php echo URLROOT; ?>/about">About</a>
                
                <!-- Search for marketplace -->
                <?php if(strpos($_SERVER['REQUEST_URI'], '/marketplace') !== false) : ?>
                <div class="search-bar">
                    <form action="<?php echo URLROOT; ?>/marketplace/search" method="GET">
                        <input type="text" name="query" placeholder="Search products...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <?php endif; ?>
                
                <!-- Cart icon for marketplace -->
                <?php if(strpos($_SERVER['REQUEST_URI'], '/marketplace') !== false) : ?>
                <a href="<?php echo URLROOT; ?>/marketplace/cart" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) : ?>
                        <span class="badge"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <!-- Donation Icon with Pending Donations Badge -->
                <a href="<?php echo URLROOT; ?>/donations" class="donation-icon">
                    <i class="fas fa-heart"></i>
                    <?php 
                    // Fetch pending donations count (you'll need to implement this in your controller)
                    $pendingDonationsCount = isset($data['pendingDonations']) ? $data['pendingDonations'] : 0; 
                    if ($pendingDonationsCount > 0): ?>
                        <span class="badge"><?php echo $pendingDonationsCount; ?></span>
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