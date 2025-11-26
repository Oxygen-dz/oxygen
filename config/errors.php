<?php
/**
 * Error Handling Configuration
 *
 * OxygenFramework 2.0
 *
 * @package    OxygenFramework
 * @author     REDWAN AOUNI <aouniradouan@gmail.com>
 * @copyright  2024 - REDWAN AOUNI
 * @version    2.0.0
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    |
    | Configure how errors are displayed and reported.
    |
    */

    'dev_mode' => env('APP_DEBUG', true),
    'display_errors' => env('APP_DEBUG', true),
    'display_startup_errors' => env('APP_DEBUG', true),
    'error_reporting' => E_ALL,
];