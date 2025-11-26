<?php

use Oxygen\Core\Application;

$router = Application::getInstance()->make(\Bramus\Router\Router::class);

$router->get('/', function () {
    $view = Application::getInstance()->make(\Oxygen\Core\View::class);
    echo $view->render('pages/home.twig.html');
});

$router->get('/about', function () {
    $view = Application::getInstance()->make(\Oxygen\Core\View::class);
    echo $view->render('pages/about.twig.html');
});

$router->get('/example', function () {
    header('X-Debug-Route: example');
    $view = Application::getInstance()->make(\Oxygen\Core\View::class);
    echo $view->render('pages/example.twig.html');
});

$router->get('/error', function () {
    throw new Exception("This is a test error to verify Whoops!");
});
