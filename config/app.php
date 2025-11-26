<?php
/**
 * Application Configuration
 *
 * OxygenFramework 2.0
 *
 * @package    OxygenFramework
 * @author     REDWAN AOUNI <aouniradouan@gmail.com>
 * @copyright  2024 - REDWAN AOUNI
 * @version    2.0.0
 */

return [
    'name' => env('APP_NAME', 'OxygenFramework'),
    'env' => env('APP_ENV', 'development'),
    'debug' => env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
];