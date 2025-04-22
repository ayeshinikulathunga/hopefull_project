<?php
// Load Config
require_once 'config/config.php';

// Load Helper
require_once 'helpers/helper.php';

// Load Core Class
require_once 'core/Core.php';
require_once 'core/Controller.php'; 
require_once 'core/Database.php';

// Autoload other classes as needed
spl_autoload_register(function($className) {
    // Look in controllers directory
    if(file_exists('controllers/' . $className . '.php')){
        require_once 'controllers/' . $className . '.php';
    }
    // Look in models directory
    else if(file_exists('models/' . $className . '.php')){
        require_once 'models/' . $className . '.php';
    }
    // Look in core directory (for any other core classes)
    else if(file_exists('core/' . $className . '.php')){
        require_once 'core/' . $className . '.php';
    }
});

// Initialize Core Library
$init = new Core();