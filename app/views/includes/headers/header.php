<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] . ' - ' . SITENAME : SITENAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
</head>
<body>
<header class="main-header">
    <nav class="main-nav container">
        <div class="nav-brand">
            <a href="<?php echo URLROOT; ?>">
                <img src="<?php echo URLROOT; ?>/images/logo.png" alt="Hopefull Logo" class="nav-logo">
            </a>
        </div>
        
        <div class="nav-links">
            <a href="<?php echo URLROOT; ?>">Home</a>
            <a href="<?php echo URLROOT; ?>/marketplace">Marketplace</a>
            <a href="<?php echo URLROOT; ?>/about">About</a>
            <a href="<?php echo URLROOT; ?>/users/login" class=" btn btn-outline">Login</a>
            <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-outline">Register</a>
        </div>
    </nav>
</header>