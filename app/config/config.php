<?php
// Database configuration
define('DB_HOST', 'localhost:3306');  // From your SQL dump port
define('DB_USER', 'root');            // Default XAMPP user
define('DB_PASS', '');                // Default XAMPP password
define('DB_NAME', 'hopefull_db');     // Your database name

// URL Root
define('URLROOT', 'http://localhost/hopefull');

// Site Name
define('SITENAME', 'Hopefull');

// App Root
define('APPROOT', dirname(dirname(__FILE__)));

$rootDir = realpath(dirname(__FILE__) . '/..');
define('ROOT_PATH', $rootDir);
define('UPLOADS_PATH', ROOT_PATH . '/public/uploads');
define('UPLOADS_URL', URLROOT . '/uploads');

// PayHere Configuration
define('PAYHERE_MERCHANT_ID', '1230184');
define('PAYHERE_MERCHANT_SECRET', 'Mjc5NzQ1NTk1MjI0ODI1MzI1MzA4MDYzNTEwMDgyNTY1ODgzNDcy');
define('PAYHERE_SANDBOX', true); 
define('PAYHERE_RETURN_URL', URLROOT . '/marketplace/paymentSuccess');
define('PAYHERE_CANCEL_URL', URLROOT . '/marketplace/paymentCancelled');
define('PAYHERE_NOTIFY_URL', URLROOT . '/marketplace/paymentNotify');





