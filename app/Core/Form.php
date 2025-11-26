<?php

namespace Oxygen\Core;

class Form
{
    public static function open($action = '', $method = 'POST', $options = [])
    {
        $attributes = [];
        foreach ($options as $key => $value) {
            $attributes[] = "$key=\"$value\"";
        }
        $attrString = implode(' ', $attributes);

        $html = "<form action=\"$action\" method=\"$method\" $attrString>";

        if (strtoupper($method) !== 'GET') {
            $html .= Application::getInstance()->make(CSRF::class)->field();
        }

        return $html;
    }

    public static function close()
    {
        return '</form>';
    }

    public static function text($name, $value = null, $options = [])
    {
        return static::input('text', $name, $value, $options);
    }

    public static function password($name, $options = [])
    {
        return static::input('password', $name, null, $options);
    }

    public static function email($name, $value = null, $options = [])
    {
        return static::input('email', $name, $value, $options);
    }

    public static function input($type, $name, $value = null, $options = [])
    {
        $options['type'] = $type;
        $options['name'] = $name;
        if ($value !== null) {
            $options['value'] = $value;
        }

        $attributes = [];
        foreach ($options as $key => $val) {
            $attributes[] = "$key=\"$val\"";
        }

        return '<input ' . implode(' ', $attributes) . '>';
    }
}
