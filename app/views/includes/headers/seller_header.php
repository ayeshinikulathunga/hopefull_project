<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] . ' - ' . SITENAME : SITENAME; ?></title>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Main Stylesheets -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/seller.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/print-styles.css">
 
    
</head>
<body class="admin-panel">
    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <div class="logo-container">
                    <img src="<?php echo URLROOT; ?>/images/logo.png" alt="Logo" class="sidebar-logo">
                </div>
                <span class="admin-badge">Seller</span>
            </div>
            
            <nav class="admin-nav">
                <ul>
                    <li class="nav-item <?php echo ($data['title'] == 'Seller Dashboard') ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/sellers/dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo ($data['title'] == 'Manage Products') ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/sellers/products">
                            <i class="fas fa-box"></i>
                            <span>Products</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo ($data['title'] == 'Orders') ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/sellers/orders">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo ($data['title'] == 'Cancellation Requests') ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/sellers/cancellationRequests">
                            <i class="fas fa-times-circle"></i>
                            <span>Cancellations</span>
                            <?php 
                            // Show notification badge if there are pending cancellation requests
                            if(isset($data['pending_cancellations_count']) && $data['pending_cancellations_count'] > 0): 
                            ?>
                            <span class="notification-badge"><?php echo $data['pending_cancellations_count']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <li class="nav-item <?php echo ($data['title'] == 'Inventory') ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/sellers/inventory">
                            <i class="fas fa-warehouse"></i>
                            <span>Inventory</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo ($data['title'] == 'Product Inquiries') ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/sellers/inquiries">
                            <i class="fas fa-question-circle"></i>
                            <span>Inquiries</span>
                            <?php 
                            // Show notification badge if there are pending inquiries
                            if(isset($data['pending_inquiries']) && !empty($data['pending_inquiries'])): 
                            ?>
                            <span class="notification-badge"><?php echo count($data['pending_inquiries']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item <?php echo ($data['title'] == 'Delivery Management') ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/sellers/delivery">
                            <i class="fas fa-truck"></i>
                            <span>Delivery</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/users/logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <h1><?php echo $data['title']; ?></h1>
                </div>
                
                <div class="header-right">
                    <div class="search-bar">
                        <input type="text" placeholder="Search...">
                        <i class="fas fa-search"></i>
                    </div>
                    
                   
                    
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php
                        $pendingCount = isset($data['pending_inquiries']) ? count($data['pending_inquiries']) : 0;
                        if ($pendingCount > 0):
                        ?>
                            <span class="notification-badge"><?php echo $pendingCount; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.querySelector('.admin-sidebar');
                
                // Add click event to sidebar to toggle expansion
                sidebar.addEventListener('click', function() {
                    this.classList.toggle('expanded');
                });
            });
            </script>

            <div class="admin-content">
                <!-- Page content will be loaded here -->