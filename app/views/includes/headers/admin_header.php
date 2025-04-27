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
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/userStyles.css">
</head>
<body class="admin-panel">
    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <div class="logo-container">
                    <img src="<?php echo URLROOT; ?>/images/logo.png" alt="Logo" class="sidebar-logo">
                </div>
                <span class="admin-badge">Admin</span>
            </div>
            
            <nav class="admin-nav">
                <ul>
                    <li class="nav-item <?php echo ($data['title'] == 'Admin Dashboard') ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/admins/dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo ($data['title'] == 'Manage Users') ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/admins/users">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li class="nav-item <?php echo ($data['title'] == 'Verification Requests') ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/admins/verifications">
                            <i class="fas fa-user-check"></i>
                            <span>Verifications</span>
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


        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <h1><?php echo $data['title']; ?></h1>
                </div>
                
                <div class="header-right">
                    
                    
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php
                        $notificationCount = isset($data['notifications']) ? count($data['notifications']) : 0;
                        if ($notificationCount > 0):
                        ?>
                            <span class="notification-badge"><?php echo $notificationCount; ?></span>
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

            <div class="admin-content"></div>