<?php
if( !isset( $_SESSION ) ) { session_start(); } 
/* =============================================================================================================================*/
/* =============================================================================================================================*/

/**
 * server.php
 *
 * Main file requirments
 *
 * @category   E-Wallet
 * @package    Oxygen
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2021 - Oxygen
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */

/* =============================================================================================================================*/
/* =============================================================================================================================*/

	// License
	require_once __DIR__ .  '/' . 'app/license-halper.php';

	// include autoload file and load all dependencies
	require_once __DIR__ .  '/' . 'vendor/autoload.php';

	// Errors Handler
	require_once __DIR__ .  '/' . 'config/errors.php';

	// File Systems
	require_once __DIR__ .  '/' . 'config/filesystems.php';

	// Main APP CONF
	require_once __DIR__ .  '/' . 'config/app.php';

	// Utilities & Uses
	require_once __DIR__ .  '/' . 'config/utilities.php';

	// Configurations
	require_once __DIR__ .  '/' . 'config/config.php';

	// Databse connection
	require_once __DIR__ .  '/' . 'config/database.php';

	// Security
	//require_once __DIR__ .  '/' . 'config/security.php';

	// Email Config
	require_once __DIR__ .  '/' . 'config/email.php';


	// Payment Configurations
	require_once __DIR__ .  '/' . 'config/payments.php';


	// Codes functions
	 require_once __DIR__ .  '/' . 'app/main/functions.php';

	// View Structures
	require_once __DIR__ .  '/' . 'config/view.php';

	// Main Controller
	require_once __DIR__ . '/' . 'app/Controllers/Controller.php';

	// Templates Arrays
	require_once __DIR__ . '/' . 'resources/templates-vars.php';

	// Routing system
	require_once __DIR__ . '/' . 'routes/web.php';



?>
