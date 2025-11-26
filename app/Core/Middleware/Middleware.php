<?php

namespace Oxygen\Core\Middleware;

use Oxygen\Core\Request;
use Closure;

/**
 * Middleware Interface
 * 
 * All middleware classes must implement this interface. Middleware allows you to
 * filter HTTP requests entering your application. Common use cases include:
 * - Authentication
 * - CSRF protection
 * - Logging
 * - Rate limiting
 * 
 * @package    Oxygen\Core\Middleware
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * class MyMiddleware implements Middleware
 * {
 *     public function handle(Request $request, Closure $next)
 *     {
 *         // Do something before the request
 *         
 *         $response = $next($request);
 *         
 *         // Do something after the request
 *         
 *         return $response;
 *     }
 * }
 */
interface Middleware
{
    /**
     * Handle an incoming request
     * 
     * @param Request $request The incoming HTTP request
     * @param Closure $next The next middleware in the pipeline
     * @return mixed
     */
    public function handle(Request $request, Closure $next);
}
