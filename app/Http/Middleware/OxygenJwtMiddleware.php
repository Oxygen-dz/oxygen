<?php

namespace Oxygen\Http\Middleware;

use Oxygen\Core\Middleware\Middleware;
use Oxygen\Core\Request;
use Oxygen\Core\Auth\OxygenJWT;
use Closure;

/**
 * OxygenJwtMiddleware - JWT Authentication
 * 
 * Validates JWT tokens and authenticates API requests.
 * Extracts user information from the token and makes it
 * available to the application.
 * 
 * @package    Oxygen\Http\Middleware
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenJwtMiddleware implements Middleware
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
        // Get Authorization header
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;

        if (!$authHeader) {
            $this->unauthorized('No authorization token provided');
            return;
        }

        // Extract token from header
        $token = OxygenJWT::extractFromHeader($authHeader);

        if (!$token) {
            $this->unauthorized('Invalid authorization header format');
            return;
        }

        // Validate token
        $decoded = OxygenJWT::validate($token);

        if (!$decoded) {
            $this->unauthorized('Invalid or expired token');
            return;
        }

        // Check token type (should be access token, not refresh)
        if (isset($decoded->type) && $decoded->type !== 'access') {
            $this->unauthorized('Invalid token type');
            return;
        }

        // Store user data in request for later use
        $_SERVER['JWT_USER'] = $decoded->data;
        $_SERVER['JWT_PAYLOAD'] = $decoded;
    }

    /**
     * Send unauthorized response
     * 
     * @param string $message Error message
     * @return void
     */
    protected function unauthorized($message)
    {
        header('Content-Type: application/json');
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => $message,
            'error' => 'Unauthorized',
            'timestamp' => date('c')
        ]);

        exit;
    }
}
