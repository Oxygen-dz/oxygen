<?php
/* =============================================================================================================================*/
/* =============================================================================================================================*/

/**
 * middlewares.php
 *
 * This file handle the requests before that they send and check things that we need before send requests
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


$OxRouting->mount('/welcome', function() use ($OxRouting) {


    // Before accessing '/'
	$OxRouting->before('GET|POST', '/', function() {
	    if (!isset( $_SESSION['FirstLogin'] )) {
	        header('Location: '.$_ENV['APP_URL'].'/auth/login');
	        exit();
	    }
	});

    // Before accessing '/*'
	$OxRouting->before('GET|POST', '/(.*)', function() {
	    if (!isset( $_SESSION['FirstLogin'] )) {
	        header('Location: '.$_ENV['APP_URL'].'/auth/login');
	        exit();
	    }
	});


    // Before accessing '/welcome/step/' (.* Means all url after that will included in that) 
	$OxRouting->before('GET|POST', '/agreement', function() {
	    if (isset( $_SESSION['UserAgree'] )) {
	        header('Location: '.$_ENV['APP_URL'].'/welcome/step/1');
	        exit();
	    }
	});

    // Before accessing '/welcome/step/' (.* Means all url after that will included in that) 
	$OxRouting->before('GET|POST', '/step/(.*)', function() {
	    if (!isset( $_SESSION['UserAgree'] )) {
	        header('Location: '.$_ENV['APP_URL'].'/welcome/agreement');
	        exit();
	    }
	});



});


// Middleware : Before Accessing the site check if user is logged in or no
$OxRouting->before('GET|POST', '/browse', function() {
    if (!isset( $_SESSION['Login'] )) {
        header('Location: '.$_ENV['APP_URL'].'/auth/login');
        exit();
    }
});



// Middleware : Before Accessing the site check if user is logged in or no
$OxRouting->before('GET|POST', '/course/(.*)', function() {
    if (!isset( $_SESSION['Login'] )) {
        header('Location: '.$_ENV['APP_URL'].'/auth/login');
        exit();
    }
});



// Middleware : Check if user logged in if yes redirect him to the browse page
$OxRouting->before('GET|POST', '/', function() {
    if (isset( $_SESSION['Login'] )) {
        header('Location: '.$_ENV['APP_URL'].'/browse');
        exit();
    }
});

?>