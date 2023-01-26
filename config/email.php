<?php
/**
 * Email.php
 *
 * Email Manager
 *
 * @category   E-Wallet
 * @package    Oxygen
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2021 - Oxygen
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */

// Put : SENDGRID - TWILIO - MSG91
define("DEFAULT_EMAIL_DRIVER", $_ENV['DEFAULT_EMAIL_DRIVER']);


// Drivers API KEY
define("SENDGRID_API_KEY", $_ENV['SENDGRID_API_KEY']);

// FROM
define("FROM_EMAIL", $_ENV['FROM_EMAIL']);
define("FROM_USER", $_ENV['FROM_USER']);




?>