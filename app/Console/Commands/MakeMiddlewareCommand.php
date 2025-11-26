<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;

/**
 * MakeMiddlewareCommand - Generate a new middleware class
 * 
 * @package    Oxygen\Console\Commands
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * Usage:
 *   php oxygen make:middleware CheckAge
 */
class MakeMiddlewareCommand extends Command
{
    public function execute($arguments)
    {
        if (empty($arguments[0])) {
            $this->error('Middleware name is required.');
            $this->info('Usage: php oxygen make:middleware MiddlewareName');
            return;
        }

        $name = $arguments[0];
        $className = str_replace('Middleware', '', $name) . 'Middleware';
        $namespace = 'Oxygen\\Http\\Middleware';
        $path = __DIR__ . '/../../../app/Http/Middleware/' . $className . '.php';

        $content = $this->getStub($className, $namespace);

        if ($this->createFile($path, $content)) {
            $this->success("Middleware created successfully: {$className}");
            $this->info("Location: {$path}");
        }
    }

    protected function getStub($className, $namespace)
    {
        return <<<PHP
<?php

namespace {$namespace};

use Oxygen\Core\Middleware\Middleware;
use Oxygen\Core\Request;
use Closure;

/**
 * {$className}
 * 
 * Custom middleware for OxygenFramework.
 * 
 * @package    {$namespace}
 * @author     OxygenFramework
 * @copyright  2024 - OxygenFramework
 */
class {$className} implements Middleware
{
    /**
     * Handle an incoming request
     * 
     * @param Request \$request The incoming HTTP request
     * @param Closure \$next The next middleware in the pipeline
     * @return mixed
     */
    public function handle(Request \$request, Closure \$next)
    {
        // Add your middleware logic here
        // Example: Check if user has permission
        
        // Before the request is handled
        // ...

        // Continue to next middleware/route
        \$response = \$next(\$request);

        // After the request is handled
        // ...

        return \$response;
    }
}

PHP;
    }
}
