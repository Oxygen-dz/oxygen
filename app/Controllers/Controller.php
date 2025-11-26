<?php

use Oxygen\Core\Application;
use Oxygen\Core\View;
use Oxygen\Core\Response;

class Controller
{
    protected $app;

    public function __construct()
    {
        $this->app = Application::getInstance();
    }

    protected function view($template, $data = [])
    {
        $view = $this->app->make(View::class);
        return $view->render($template, $data);
    }

    protected function json($data, $status = 200)
    {
        return Response::json($data, $status)->send();
    }

    protected function db()
    {
        return $this->app->make('db');
    }

    function index()
    {

    }
}