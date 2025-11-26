<?php

namespace Oxygen\Http\Middleware;

use Oxygen\Core\Middleware\Middleware;
use Oxygen\Core\Request;
use Closure;

/**
 * OxygenCorsMiddleware - Cross-Origin Resource Sharing
 * 
 * Handles CORS headers for API requests to allow modern
 * frontend frameworks to communicate with the API.
 * 
 * @package    Oxygen\Http\Middleware
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenCorsMiddleware implements Middleware
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
        $config = require __DIR__ . '/../../../config/cors.php';

        // Get the origin from the request
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        // Determine allowed origin
        $allowedOrigin = $this->getAllowedOrigin($origin, $config['allowed_origins']);

        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $this->sendPreflightResponse($allowedOrigin, $config);
            exit;
        }

        // Add CORS headers to the response
        $this->addCorsHeaders($allowedOrigin, $config);
    }

    /**
     * Get the allowed origin based on configuration
     * 
     * @param string $origin Request origin
     * @param array $allowedOrigins Configured allowed origins
     * @return string Allowed origin
     */
    protected function getAllowedOrigin($origin, $allowedOrigins)
    {
        // If wildcard is set, allow all origins
        if (in_array('*', $allowedOrigins)) {
            return $origin ?: '*';
        }

        // Check if the origin is in the allowed list
        if (in_array($origin, $allowedOrigins)) {
            return $origin;
        }

        // Default to first allowed origin or empty
        return $allowedOrigins[0] ?? '';
    }

    /**
     * Send preflight response for OPTIONS requests
     * 
     * @param string $allowedOrigin Allowed origin
     * @param array $config CORS configuration
     * @return void
     */
    protected function sendPreflightResponse($allowedOrigin, $config)
    {
        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        header('Access-Control-Allow-Methods: ' . implode(', ', $config['allowed_methods']));
        header('Access-Control-Allow-Headers: ' . implode(', ', $config['allowed_headers']));
        header('Access-Control-Max-Age: ' . $config['max_age']);

        if ($config['allow_credentials']) {
            header('Access-Control-Allow-Credentials: true');
        }

        http_response_code(204); // No Content
    }

    /**
     * Add CORS headers to the response
     * 
     * @param string $allowedOrigin Allowed origin
     * @param array $config CORS configuration
     * @return void
     */
    protected function addCorsHeaders($allowedOrigin, $config)
    {
        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        header('Access-Control-Allow-Methods: ' . implode(', ', $config['allowed_methods']));
        header('Access-Control-Allow-Headers: ' . implode(', ', $config['allowed_headers']));
        header('Access-Control-Expose-Headers: ' . implode(', ', $config['exposed_headers']));

        if ($config['allow_credentials']) {
            header('Access-Control-Allow-Credentials: true');
        }
    }
}
