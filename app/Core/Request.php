<?php

namespace Oxygen\Core;

class Request
{
    protected $get;
    protected $post;
    protected $server;
    protected $files;
    protected $cookies;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
    }

    public static function capture()
    {
        return new static();
    }

    public function get($key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    public function post($key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    public function input($key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all()
    {
        return array_merge($this->get, $this->post);
    }

    public function file($key)
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile($key)
    {
        return isset($this->files[$key]);
    }

    public function method()
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function uri()
    {
        return $this->server['REQUEST_URI'];
    }
}
