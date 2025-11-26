<?php

namespace Oxygen\Http\Middleware;

use Oxygen\Core\Middleware\Middleware;
use Oxygen\Core\Request;
use Closure;

/**
 * OxygenRateLimitMiddleware - API Rate Limiting
 * 
 * Implements token bucket algorithm to limit API requests
 * and prevent abuse. Tracks requests by IP address or user ID.
 * 
 * @package    Oxygen\Http\Middleware
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenRateLimitMiddleware implements Middleware
{
    /**
     * Handle the request
     * 
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next = null)
    {
        $config = require __DIR__ . '/../../../config/api.php';

        if (!$config['rate_limit']['enabled']) {
            return;
        }

        // Get identifier (IP or user ID)
        $identifier = $this->getIdentifier();

        // Get rate limit settings
        $maxRequests = $this->getMaxRequests($config);
        $window = $config['rate_limit']['window'];

        // Check rate limit
        $allowed = $this->checkRateLimit($identifier, $maxRequests, $window);

        if (!$allowed) {
            $this->tooManyRequests($maxRequests, $window);
            return;
        }

        // Add rate limit headers
        $this->addRateLimitHeaders($identifier, $maxRequests, $window);
    }

    /**
     * Get identifier for rate limiting (IP or user ID)
     * 
     * @return string Identifier
     */
    protected function getIdentifier()
    {
        // Use user ID if authenticated
        if (isset($_SERVER['JWT_USER'])) {
            $userData = $_SERVER['JWT_USER'];
            if (is_object($userData) && isset($userData->id)) {
                return 'user_' . $userData->id;
            } elseif (is_array($userData) && isset($userData['id'])) {
                return 'user_' . $userData['id'];
            }
        }

        // Fall back to IP address
        return 'ip_' . $this->getClientIp();
    }

    /**
     * Get client IP address
     * 
     * @return string IP address
     */
    protected function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }

    /**
     * Get max requests based on authentication status
     * 
     * @param array $config API configuration
     * @return int Max requests
     */
    protected function getMaxRequests($config)
    {
        // Authenticated users get higher limits
        if (isset($_SERVER['JWT_USER'])) {
            return $config['rate_limit']['authenticated_max_requests'];
        }

        return $config['rate_limit']['max_requests'];
    }

    /**
     * Check if request is within rate limit
     * 
     * @param string $identifier Client identifier
     * @param int $maxRequests Max requests allowed
     * @param int $window Time window in seconds
     * @return bool True if allowed
     */
    protected function checkRateLimit($identifier, $maxRequests, $window)
    {
        $storageDir = __DIR__ . '/../../../storage/framework/rate-limits';

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $filename = $storageDir . '/' . md5($identifier) . '.json';
        $now = time();

        // Load existing data
        if (file_exists($filename)) {
            $data = json_decode(file_get_contents($filename), true);
        } else {
            $data = [
                'requests' => [],
                'reset_at' => $now + $window
            ];
        }

        // Reset if window has passed
        if ($now >= $data['reset_at']) {
            $data = [
                'requests' => [],
                'reset_at' => $now + $window
            ];
        }

        // Remove old requests outside the window
        $data['requests'] = array_filter($data['requests'], function ($timestamp) use ($now, $window) {
            return $timestamp > ($now - $window);
        });

        // Check if limit exceeded
        if (count($data['requests']) >= $maxRequests) {
            file_put_contents($filename, json_encode($data));
            return false;
        }

        // Add current request
        $data['requests'][] = $now;
        file_put_contents($filename, json_encode($data));

        return true;
    }

    /**
     * Add rate limit headers to response
     * 
     * @param string $identifier Client identifier
     * @param int $maxRequests Max requests allowed
     * @param int $window Time window in seconds
     * @return void
     */
    protected function addRateLimitHeaders($identifier, $maxRequests, $window)
    {
        $storageDir = __DIR__ . '/../../../storage/framework/rate-limits';
        $filename = $storageDir . '/' . md5($identifier) . '.json';

        if (file_exists($filename)) {
            $data = json_decode(file_get_contents($filename), true);
            $remaining = max(0, $maxRequests - count($data['requests']));
            $resetAt = $data['reset_at'];
        } else {
            $remaining = $maxRequests;
            $resetAt = time() + $window;
        }

        header('X-RateLimit-Limit: ' . $maxRequests);
        header('X-RateLimit-Remaining: ' . $remaining);
        header('X-RateLimit-Reset: ' . $resetAt);
    }

    /**
     * Send too many requests response
     * 
     * @param int $maxRequests Max requests allowed
     * @param int $window Time window in seconds
     * @return void
     */
    protected function tooManyRequests($maxRequests, $window)
    {
        header('Content-Type: application/json');
        http_response_code(429);

        echo json_encode([
            'success' => false,
            'message' => 'Too many requests. Please try again later.',
            'error' => 'Rate limit exceeded',
            'limit' => $maxRequests,
            'window' => $window,
            'timestamp' => date('c')
        ]);

        exit;
    }
}
