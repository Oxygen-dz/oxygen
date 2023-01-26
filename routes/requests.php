<?php



/* =============================================================================================================================*/
/* =============================================================================================================================*/

/**
 * requests.php
 *
 * Request file where we send [[ POST/GET/PUT/UPDATE/PATCHE ]]  Request and do the requests logic
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





// -------- Including section ------------ //

require_once dirname(__DIR__) . '/app/classes/auth.class.php';
// require_once 'app/classes/oauth.class.php';
// require_once 'app/classes/payments.class.php';
// require_once 'app/classes/search.class.php';
require_once dirname(__DIR__) . '/app/classes/ads.class.php';
require_once dirname(__DIR__) . '/app/classes/payments.class.php';
require_once dirname(__DIR__) . '/app/classes/admin.class.php';

// -------- Including section ------------ //


// User ads page request 
$OxRouting->post('/ads/([^/]+)', function() {
    $Ads =  new Ads;
    $Data 	=	array();
    $Data 	=	[
    	// Insert request data here
    	"ad_id" 	=> $_POST['ad_id'],
    	"name" 	    => $_POST['name'],
    	"phone" 	=> $_POST['phone']
    ];
    $Ads->CallRequests($Data['ad_id'], $Data['name'], $Data['phone']);
});


// All /auth request will handeled here
$OxRouting->mount('/auth', function() use ($OxRouting) {

// User Login
$OxRouting->post('/login', function() {
    $login 	=   new Users;
    $Data 	=	array();
    $Data 	=	[
    	// Insert request data here
    	"email" 	=> $_POST['email'],
    	"password" 	=> $_POST['password']
    ];
    // Insert data to db
    $login->Login($Data['email'], $Data['password']);
});



// -------------- Ads Requests ---------------------- //

// All /welcome request will handeled here
$OxRouting->mount('/ads/create/', function() use ($OxRouting) {

});
    





// -------------- OAuth Requests ----------------- //


// User Login/signup with Google
$OxRouting->get('/google', function() {
    $google 	=   new OAuth;
    // Run Google API
    $google->Google();
});

// Google Callback Login/signup 
$OxRouting->get('/google/callback', function() {
    $google 	=   new OAuth;
    // Run Google API
    $google->Google(true);
});




// User Login/signup with facebook
$OxRouting->get('/facebook', function() {
    $facebook 	=   new OAuth;
    // Run facebook API
    $facebook->Facebook();
});

// facebook Callback Login/signup 
$OxRouting->get('/facebook/callback', function() {
    $facebook 	=   new OAuth;
    // Run facebook API
    $facebook->Facebook(true);
});




// User Login/signup with Instagram
$OxRouting->get('/instagram', function() {
    $instagram 	=   new OAuth;
    // Run instagram API
    $instagram->Instagram();
});

// Instagram Callback Login/signup 
$OxRouting->get('/instagram/callback', function() {
    $instagram 	=   new OAuth;
    // Run instagram API
    $instagram->Instagram(true);
});






// User Login/signup with Github
$OxRouting->get('/github', function() {
    $github 	=   new OAuth;
    // Run Github API
    $github->Github();
});

// Github Callback Login/signup 
$OxRouting->get('/github/callback', function() {
    $github 	=   new OAuth;
    // Run Github API
    $github->Github(true);
});


});







// All /welcome request will handeled here
$OxRouting->mount('/welcome', function() use ($OxRouting) {

// User Agreement page 
$OxRouting->post('/agreement', function() {
    $Welcome =  new Welcomer;
    $Data 	=	array();
    $Data 	=	[
    	"iagree" 	=> $_POST['agree'],
    ];
    $Welcome->Agreement($Data['iagree']);
});


// User step/1 page request 
$OxRouting->post('/step/1', function() {
    $Welcome =  new Welcomer;
    $Data 	=	array();
    $Data 	=	[
    	"country" 	=> $_POST['country'],
    	"sexe" 		=> $_POST['sexe'],
    ];
    $Welcome->FirstStep($Data['country'], $Data['sexe']);
});


// User step/1 page request 
$OxRouting->post('/step/2', function() {
    $Welcome =  new Welcomer;
    $Welcome->FinishStep();
});


});






// All /pay request will handeled here
$OxRouting->mount('/pay', function() use ($OxRouting) {

// Pay with stripe getway 
$OxRouting->get('/stripe', function() {
    $Payment =  new Payments;
    $Data   =   array();
    $Data   =   [
        "iagree"    => "0",
    ];
    $Payment->Stripe("4242 4242 4242 4242", "12", "25", "245", "0.99",);
});



// Pay with stripe getway 
$OxRouting->post('/paypal', function() {
    $Payment =  new Payments;
    $Data   =   array();
    $Data   =   [
        "iagree"    => $_POST['agree'],
    ];
    $Payment->Paypal($Data['iagree']);
});



// Pay with Eddahabia [Algeria] getway 
$OxRouting->post('/eddahabia', function() {
    $Payment =  new Payments;
    $Data   =   array();
    $Data   =   [
        "iagree"    => $_POST['agree'],
    ];
    $Payment->Eddahabia($Data['iagree']);
});


// Pay with CIB [Algeria] getway 
$OxRouting->post('/cib', function() {
    $Payment =  new Payments;
    $Data   =   array();
    $Data   =   [
        "iagree"    => $_POST['agree'],
    ];
    $Payment->CIB($Data['iagree']);
});


});





?>