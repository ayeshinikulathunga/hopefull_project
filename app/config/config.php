<?php
// Database configuration
define('DB_HOST', 'localhost:3308');  // From your SQL dump port
define('DB_USER', 'root');            // Default XAMPP user
define('DB_PASS', '');                // Default XAMPP password
define('DB_NAME', 'hopefull_db');     // Your database name

// URL Root
define('URLROOT', 'http://localhost/hopefull');

// Site Name
define('SITENAME', 'Hopefull');

// App Root
define('APPROOT', dirname(dirname(__FILE__)));


// In config/config.php
$rootDir = realpath(dirname(__FILE__) . '/..');
define('ROOT_PATH', $rootDir);
define('UPLOADS_PATH', ROOT_PATH . '/public/uploads');
define('UPLOADS_URL', URLROOT . '/uploads');