<?php

namespace Oxygen\Http\Middleware;

use Oxygen\Core\Middleware\Middleware;
use Oxygen\Core\Request;
use Oxygen\Core\CSRF;
use Oxygen\Core\Application;
use Closure;

/**
 * OxygenCsrfMiddleware - CSRF Protection Middleware
 * 
 * This middleware protects your application from Cross-Site Request Forgery (CSRF)
 * attacks by validating CSRF tokens on state-changing requests (POST, PUT, DELETE, PATCH).
 * 
 * @package    Oxygen\Http\Middleware
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * // This middleware should be applied globally to all POST/PUT/DELETE/PATCH routes
 * // It will automatically check for the csrf_token in the request
 */
class OxygenCsrfMiddleware implements Middleware
{
    /**
     * HTTP methods that require CSRF protection
     * 
     * @var array
     */
    protected $protectedMethods = ['POST', 'PUT', 'DELETE', 'PATCH'];

    /**
     * Handle an incoming request
     * 
     * @param Request $request The incoming HTTP request
     * @param Closure $next The next middleware in the pipeline
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check CSRF for state-changing methods
        if (!in_array($request->method(), $this->protectedMethods)) {
            return $next($request);
        }

        // Get the CSRF instance from the container
        $csrf = Application::getInstance()->make(CSRF::class);

        // Get the token from the request
        $token = $request->input('csrf_token') ?? $request->input('_token');

        // Verify the token
        if (!$csrf->verify($token)) {
            // CSRF token is invalid
            http_response_code(419); // 419 Page Expired
            die('CSRF token mismatch. Please refresh the page and try again.');
        }

        // CSRF token is valid, continue to next middleware/route
        return $next($request);
    }
}
