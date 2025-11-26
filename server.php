<?php

/**
 * OxygenFramework Bootstrap
 * 
 * This file bootstraps the OxygenFramework application.
 * 
 * @package    OxygenFramework
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Load Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

use Oxygen\Core\Application;

// Initialize Application
$app = new Application(__DIR__);

// Register Core Services
$app->registerCoreServices();

// Initialize View (sets global $twig for backward compatibility)
$app->make(\Oxygen\Core\View::class);

// Load legacy helper functions (will be deprecated in future versions)
if (file_exists(__DIR__ . '/app/main/functions.php')) {
	require_once __DIR__ . '/app/main/functions.php';
}

// Load base controller
if (file_exists(__DIR__ . '/app/Controllers/Controller.php')) {
	require_once __DIR__ . '/app/Controllers/Controller.php';
}

return $app;
