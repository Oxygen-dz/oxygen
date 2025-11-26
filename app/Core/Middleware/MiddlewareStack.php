<?php

namespace Oxygen\Core\Middleware;

use Oxygen\Core\Request;
use Closure;

/**
 * MiddlewareStack - Middleware Pipeline Handler
 * 
 * This class manages the execution of middleware in a pipeline pattern.
 * It allows multiple middleware to be chained together, each having the
 * opportunity to inspect and modify the request/response.
 * 
 * @package    Oxygen\Core\Middleware
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class MiddlewareStack
{
    /**
     * Registered middleware classes
     * 
     * @var array
     */
    protected $middleware = [];

    /**
     * Add a middleware to the stack
     * 
     * @param string|Middleware $middleware Middleware class name or instance
     * @return self
     */
    public function add($middleware)
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * Execute the middleware pipeline
     * 
     * This method creates a nested chain of closures, each representing
     * a middleware layer. The innermost closure is the final destination.
     * 
     * @param Request $request The incoming request
     * @param Closure $destination The final destination (usually the route handler)
     * @return mixed
     */
    public function handle(Request $request, Closure $destination)
    {
        // Build the middleware pipeline from the inside out
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            $this->carry(),
            $destination
        );

        // Execute the pipeline
        return $pipeline($request);
    }

    /**
     * Get a closure that wraps the next middleware layer
     * 
     * This creates the "onion" structure of middleware, where each layer
     * wraps the next one.
     * 
     * @return Closure
     */
    protected function carry()
    {
        return function ($stack, $middleware) {
            return function ($request) use ($stack, $middleware) {
                // Instantiate middleware if it's a class name
                if (is_string($middleware)) {
                    $middleware = new $middleware();
                }

                // Execute the middleware
                return $middleware->handle($request, $stack);
            };
        };
    }

    /**
     * Get all registered middleware
     * 
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Clear all middleware
     * 
     * @return void
     */
    public function clear()
    {
        $this->middleware = [];
    }
}
