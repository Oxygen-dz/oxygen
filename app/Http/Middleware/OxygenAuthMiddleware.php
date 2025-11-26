<?php

namespace Oxygen\Http\Middleware;

use Oxygen\Core\Middleware\Middleware;
use Oxygen\Core\Request;
use Oxygen\Core\Auth;
use Oxygen\Core\Application;
use Oxygen\Core\OxygenConfig;
use Closure;

/**
 * OxygenAuthMiddleware - Authentication Middleware
 * 
 * This middleware ensures that only authenticated users can access
 * protected routes. If the user is not authenticated, they will be
 * redirected to the login page.
 * 
 * @package    Oxygen\Http\Middleware
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * // In your routes:
 * $router->before('GET|POST', '/dashboard', function() {
 *     $middleware = new OxygenAuthMiddleware();
 *     $middleware->handle(Request::capture(), function($req) {
 *         // Continue to route
 *     });
 * });
 */
class OxygenAuthMiddleware implements Middleware
{
    /**
     * Handle an incoming request
     * 
     * @param Request $request The incoming HTTP request
     * @param Closure $next The next middleware in the pipeline
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the Auth instance from the container
        $auth = Application::getInstance()->make(Auth::class);

        // Check if user is authenticated
        if (!$auth->check()) {
            // User is not authenticated, redirect to login
            $loginUrl = OxygenConfig::get('app.APP_URL', '') . '/auth/login';
            header('Location: ' . $loginUrl);
            exit();
        }

        // User is authenticated, continue to next middleware/route
        return $next($request);
    }
}
