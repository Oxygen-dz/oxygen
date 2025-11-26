<?php

/**
 * Storage Helper Functions
 * 
 * All storage is in public/storage/
 * 
 * @package    OxygenFramework
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 */

use Oxygen\Core\Storage\OxygenStorage;

if (!function_exists('storage_path')) {
    /**
     * Get storage path (public/storage)
     */
    function storage_path($path = '')
    {
        return __DIR__ . '/../../public/storage/' . ltrim($path, '/');
    }
}

if (!function_exists('public_path')) {
    /**
     * Get public path
     */
    function public_path($path = '')
    {
        return __DIR__ . '/../../public/' . ltrim($path, '/');
    }
}

if (!function_exists('storage_url')) {
    /**
     * Get storage URL
     */
    function storage_url($path)
    {
        return OxygenStorage::url($path);
    }
}

if (!function_exists('asset')) {
    /**
     * Get asset URL
     */
    function asset($path)
    {
        $appUrl = $_ENV['APP_URL'] ?? '';
        return $appUrl . '/' . ltrim($path, '/');
    }
}

if (!function_exists('upload_file')) {
    /**
     * Upload a file to public/storage
     */
    function upload_file($file, $directory = 'uploads')
    {
        return OxygenStorage::put($file, $directory);
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable
     */
    function env($key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}
