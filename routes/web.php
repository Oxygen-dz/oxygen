<?php
// Create Router instance
$OxRouting = new \Bramus\Router\Router();

// including middlewares
include_once 'middlewares.php';

// Requests [POST GET ect ] Method will be requested from here
include_once 'requests.php';

// if the requested page isn't exists show 404 page
$OxRouting->set404(function() {
    global $twig,$ErrorPageArray;
    header('HTTP/1.1 404 Not Found');
    echo $twig->render("errors.blade.html",$ErrorPageArray);
    exit();
});

$OxRouting->get('/', function() {
    global $twig;
    echo $twig->render("index.blade.html");
});




$OxRouting->mount('/payment', function() use ($OxRouting) {
    // This will result payment/eddahabia
    $OxRouting->get('/algpay','Payments@Eddahabia');
    $OxRouting->get('/algpay/success','Payments@Eddahabia');
});


// Signup Routing
$OxRouting->get('/auth/signup', function() {
    global $signupPageArrays,$twig;
    echo $twig->render("signup.blade.html",$signupPageArrays);

});

// Login Routing
$OxRouting->get('/auth/login', function() {
    global $loginPageArrays,$twig;
    echo $twig->render("login.blade.html",$loginPageArrays);

});

$OxRouting->mount('/welcome', function() use ($OxRouting) {

    // will result in '/'
    $OxRouting->get('/', function() {
        global $welcomePageArrays,$twig;
        echo $twig->render("welcome.blade.html",$welcomePageArrays);
    });

    // will result in 'welcome/agreement'
    $OxRouting->get('/agreement', function() {
        global $welcomePageArrays,$twig;
        echo $twig->render("agreements.blade.html",$welcomePageArrays);
    });

    // will result in '/welcome/step/1'
    $OxRouting->get('/step/1', function() {
        global $welcomePageArrays,$twig;
        echo $twig->render("setup-account.blade.html",$welcomePageArrays);
    });


    // will result in '/welcome/step/2'
    $OxRouting->get('/step/2', function() {
        global $welcomePageArrays,$twig;
        echo $twig->render("package.blade.html",$welcomePageArrays);
    });

});



// Browse Page


$OxRouting->mount('/browse', function() use ($OxRouting) {

    // will result in '/browse'
    $OxRouting->get('/', function() {
        global $BrowsePageArray,$twig;
        echo $twig->render("browse.blade.html",$BrowsePageArray);
    });


});


$OxRouting->mount('/course', function() use ($OxRouting) {
    // will result in '/course/{Course key}' will show the user the course details page
    $OxRouting->get('/([^/]+)','Courses@CourseDetails');

    // will result in '/watch' will show the user the wtach page
    $OxRouting->get('/watch/([^/]+)', 'Courses@CourseWatch');
});




$OxRouting->mount('/videos', function() use ($OxRouting) {

    // Get the movies list from class and render it
    $OxRouting->get('/movies','Videos@MoviesList');

    // will result in '/videos/movies/{Course key}' will show the user the course details page
    $OxRouting->get('/movies/([^/]+)','Videos@MovieWatch');

    // Get the series list from class and render it
    $OxRouting->get('/series','Videos@SeriesList');

    // will result in '/watch' will show the user the wtach page
    $OxRouting->get('/series/([^/]+)', 'Videos@SeriesWatch');
});


// will result in Scrap from given urls
$OxRouting->mount('/search', function() use ($OxRouting) {
    // will result in '/course/{Course key}' will show the user the course details page
    $OxRouting->get('/([^/]+)','SearchEngine@Search');
});


// will result in Scrap from given urls
$OxRouting->mount('/ads', function() use ($OxRouting) {
    // will result in '/ads/{ads_id}' will show the user the course details page
    $OxRouting->get('/([^/]+)','Ads@ViewAdsbyID');
});

$OxRouting->get('/search', function() {
    global $twig;
    echo $twig->render("search.blade.html");
});






// Admin Panel

// will result in Scrap from given urls
$OxRouting->mount('/admin', function() use ($OxRouting) {
    // will result in '/ads/{ads_id}' will show the user the course details page
    $OxRouting->get('/add-ads','AdminAds@index');
});





// Run it!
$OxRouting->run();
?>