<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] . ' - ' . SITENAME : SITENAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin.css">
</head>
<body class="admin-panel">
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <div class="logo-container">
                    <img src="<?php echo URLROOT; ?>/images/logo.png" alt="<?php echo SITENAME; ?>" class="sidebar-logo">
                </div>
                <div class="admin-badge">Auth Moderator</div>
            </div>
            
            <nav class="admin-nav">
                <ul>
                    <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/authModerators/dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'manageRecipients') !== false) ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/authModerators/manageRecipients">
                            <i class="fas fa-user-check"></i>
                            <span>Verify Recipients</span>
                        </a>
                    </li>
                    <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'manageRequests') !== false) ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/authModerators/manageRequests">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Verify Requests</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/users/logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <h1><?php echo isset($data['title']) ? $data['title'] : 'Dashboard'; ?></h1>
                </div>
                
                <div class="header-right">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php 
                            $pendingCount = isset($data['stats']->pendingRecipients) ? $data['stats']->pendingRecipients : 0;
                            $pendingCount += isset($data['stats']->pendingRequests) ? $data['stats']->pendingRequests : 0;
                            
                            if($pendingCount > 0): 
                        ?>
                            <span class="notification-badge"><?php echo $pendingCount; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="user-profile">
                        <span><?php echo $_SESSION['user_name']; ?></span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content"></div>