<?php
// Check if user is logged in and store for easy access
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] . ' - ' . SITENAME : SITENAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/marketplace.css">

</head>
<body>
    <!-- User Sidebar - Only shown when logged in -->
    <?php if($isLoggedIn): ?>
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <div class="user-sidebar" id="user-sidebar">
        <div class="sidebar-content">
            <div class="sidebar-header">
                <h3>Marketplace Menu</h3>
                <button id="close-sidebar" class="btn btn-icon">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-menu">
                <a href="<?php echo URLROOT; ?>/donors/dashboard" class="sidebar-menu-item">
                    <i class="fas fa-tachometer-alt"></i> Donor Dashboard
                </a>
                
                <a href="<?php echo URLROOT; ?>/marketplace/allProducts" class="sidebar-menu-item">
                    <i class="fas fa-store"></i> Browse Products
                </a>
                
                <a href="<?php echo URLROOT; ?>/marketplace/cart" class="sidebar-menu-item">
                    <i class="fas fa-shopping-cart"></i> My Cart
                    <?php 
                    $cartItemsCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                    if ($cartItemsCount > 0): ?>
                        <span class="badge"><?php echo $cartItemsCount; ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo URLROOT; ?>/marketplace/orders" class="sidebar-menu-item">
                    <i class="fas fa-box"></i> My Orders
                </a>
                <a href="<?php echo URLROOT; ?>/marketplace/track" class="sidebar-menu-item">
                    <i class="fas fa-truck"></i> Track My Order
                </a>
                <a href="<?php echo URLROOT; ?>/marketplace/inquiries" class="sidebar-menu-item">
                    <i class="fas fa-question-circle"></i> Product Inquiries
                </a>
                <a href="<?php echo URLROOT; ?>/marketplace/wishlist" class="sidebar-menu-item">
                    <i class="fas fa-heart"></i> My Wishlist
                </a>
            </div>

            <div class="sidebar-footer">
                <a href="<?php echo URLROOT; ?>/users/logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <header class="main-header">
        <nav class="main-nav container">
            <div class="nav-brand">
                <a href="<?php echo URLROOT; ?>">
                    <img src="<?php echo URLROOT; ?>/images/logo.png" alt="Hopefull Logo" class="nav-logo">
                </a>
            </div>
            
            <div class="nav-links">
                
                <a href="<?php echo URLROOT; ?>/marketplace">Home</a>
                <a href="<?php echo URLROOT; ?>/donors">Hopefull</a>
                <a href="<?php echo URLROOT; ?>/marketplace/allProducts">Products</a>
                
                <?php if($isLoggedIn): ?>
                    <a href="<?php echo URLROOT; ?>/marketplace/orders">Orders</a>
                    
                    <!-- Shopping Cart Icon with Item Count Badge -->
                    <a href="<?php echo URLROOT; ?>/marketplace/cart" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <?php 
                        // Display cart items count if cart is not empty
                        if ($cartItemsCount > 0): ?>
                            <span class="badge"><?php echo $cartItemsCount; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Donation Icon - Link back to donor features -->
                    <a href="<?php echo URLROOT; ?>/donations" class="donation-icon">
                        <i class="fas fa-heart"></i>
                    </a>
                    
                    <!-- Toggle Sidebar Button -->
                    <a href="#" id="toggle-sidebar" class="user-icon">
                        <i class="fas fa-user"></i>
                    </a>
                <?php else: ?>
                    <!-- Login and Register buttons for non-logged in users -->
                    <a href="<?php echo URLROOT; ?>/users/login?redirect=marketplace" class=" btn btn-outline">Login</a>
                    <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-outline">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <?php if($isLoggedIn): ?>
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
    <?php endif; ?>