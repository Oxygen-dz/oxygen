<?php

namespace Oxygen\Core;

/**
 * OxygenSession - Session Management System
 * 
 * This class provides a clean, object-oriented interface for working with PHP sessions.
 * It includes support for flash messages, CSRF tokens, and secure session handling.
 * 
 * @package    Oxygen\Core
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * // Store a value
 * OxygenSession::put('user_id', 123);
 * 
 * // Retrieve a value
 * $userId = OxygenSession::get('user_id');
 * 
 * // Flash a message (available only for next request)
 * OxygenSession::flash('success', 'Profile updated successfully!');
 * 
 * // Check if session has a key
 * if (OxygenSession::has('user_id')) {
 *     // User is logged in
 * }
 */
class OxygenSession
{
    /**
     * Session started flag
     * 
     * @var bool
     */
    protected static $started = false;

    /**
     * Start the session if not already started
     * 
     * @return void
     */
    public static function start()
    {
        if (!static::$started && session_status() === PHP_SESSION_NONE) {
            session_start();
            static::$started = true;

            // Age flash data
            static::ageFlashData();
        }
    }

    /**
     * Store a value in the session
     * 
     * @param string $key Session key
     * @param mixed $value Value to store
     * @return void
     */
    public static function put($key, $value)
    {
        static::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a value from the session
     * 
     * @param string $key Session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        static::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists
     * 
     * @param string $key Session key
     * @return bool
     */
    public static function has($key)
    {
        static::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a value from the session
     * 
     * @param string $key Session key
     * @return void
     */
    public static function forget($key)
    {
        static::start();
        unset($_SESSION[$key]);
    }

    /**
     * Flash a value for the next request only
     * 
     * Flash data is automatically removed after being displayed once.
     * Perfect for success messages, error notifications, etc.
     * 
     * @param string $key Flash key
     * @param mixed $value Value to flash
     * @return void
     */
    public static function flash($key, $value)
    {
        static::start();
        $_SESSION['_flash']['new'][$key] = $value;
    }

    /**
     * Get all flash data
     * 
     * @return array
     */
    public static function getFlash()
    {
        static::start();
        return $_SESSION['_flash']['old'] ?? [];
    }

    /**
     * Get a specific flash message
     * 
     * @param string $key Flash key
     * @param mixed $default Default value
     * @return mixed
     */
    public static function getFlashMessage($key, $default = null)
    {
        $flash = static::getFlash();
        return $flash[$key] ?? $default;
    }

    /**
     * Age flash data (move new to old, remove old)
     * 
     * This is called automatically at the start of each request
     * 
     * @return void
     */
    protected static function ageFlashData()
    {
        // Remove old flash data
        unset($_SESSION['_flash']['old']);

        // Move new flash data to old
        if (isset($_SESSION['_flash']['new'])) {
            $_SESSION['_flash']['old'] = $_SESSION['_flash']['new'];
            unset($_SESSION['_flash']['new']);
        }
    }

    /**
     * Get all session data
     * 
     * @return array
     */
    public static function all()
    {
        static::start();
        return $_SESSION;
    }

    /**
     * Destroy the entire session
     * 
     * @return void
     */
    public static function destroy()
    {
        static::start();
        session_destroy();
        $_SESSION = [];
        static::$started = false;
    }

    /**
     * Regenerate the session ID
     * 
     * This is important for security, especially after login
     * 
     * @param bool $deleteOldSession Whether to delete the old session file
     * @return void
     */
    public static function regenerate($deleteOldSession = true)
    {
        static::start();
        session_regenerate_id($deleteOldSession);
    }

    /**
     * Get the session ID
     * 
     * @return string
     */
    public static function id()
    {
        static::start();
        return session_id();
    }

    /**
     * Pull a value from the session (get and forget)
     * 
     * @param string $key Session key
     * @param mixed $default Default value
     * @return mixed
     */
    public static function pull($key, $default = null)
    {
        $value = static::get($key, $default);
        static::forget($key);
        return $value;
    }
}
