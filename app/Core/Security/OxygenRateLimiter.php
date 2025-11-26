<?php

namespace Oxygen\Core\Security;

use Oxygen\Core\OxygenSession;

/**
 * OxygenRateLimiter - Rate Limiting System
 * 
 * Protect your application from brute force attacks and abuse
 * by limiting the number of requests per time period.
 * 
 * @package    Oxygen\Core\Security
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * // Limit to 5 attempts per minute
 * if (OxygenRateLimiter::tooManyAttempts('login', 5, 60)) {
 *     die('Too many login attempts. Please try again later.');
 * }
 * 
 * OxygenRateLimiter::hit('login');
 */
class OxygenRateLimiter
{
    /**
     * Check if too many attempts have been made
     * 
     * @param string $key Unique key for the action
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $decaySeconds Time window in seconds
     * @return bool
     */
    public static function tooManyAttempts($key, $maxAttempts, $decaySeconds)
    {
        $attempts = static::attempts($key);

        if ($attempts >= $maxAttempts) {
            $availableAt = static::availableAt($key);

            if ($availableAt > time()) {
                return true;
            }

            // Reset if decay time has passed
            static::clear($key);
        }

        return false;
    }

    /**
     * Increment the counter for a given key
     * 
     * @param string $key Unique key
     * @param int $decaySeconds Time window in seconds
     * @return int Current attempt count
     */
    public static function hit($key, $decaySeconds = 60)
    {
        $sessionKey = "rate_limit_{$key}";
        $data = OxygenSession::get($sessionKey, [
            'attempts' => 0,
            'available_at' => time() + $decaySeconds
        ]);

        $data['attempts']++;
        $data['available_at'] = time() + $decaySeconds;

        OxygenSession::put($sessionKey, $data);

        return $data['attempts'];
    }

    /**
     * Get the number of attempts for a key
     * 
     * @param string $key Unique key
     * @return int
     */
    public static function attempts($key)
    {
        $sessionKey = "rate_limit_{$key}";
        $data = OxygenSession::get($sessionKey, ['attempts' => 0]);
        return $data['attempts'];
    }

    /**
     * Get the time when the key will be available again
     * 
     * @param string $key Unique key
     * @return int Unix timestamp
     */
    public static function availableAt($key)
    {
        $sessionKey = "rate_limit_{$key}";
        $data = OxygenSession::get($sessionKey, ['available_at' => 0]);
        return $data['available_at'];
    }

    /**
     * Clear the counter for a key
     * 
     * @param string $key Unique key
     * @return void
     */
    public static function clear($key)
    {
        $sessionKey = "rate_limit_{$key}";
        OxygenSession::forget($sessionKey);
    }

    /**
     * Get remaining attempts
     * 
     * @param string $key Unique key
     * @param int $maxAttempts Maximum attempts allowed
     * @return int
     */
    public static function remaining($key, $maxAttempts)
    {
        $attempts = static::attempts($key);
        return max(0, $maxAttempts - $attempts);
    }
}
