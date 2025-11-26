<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;

/**
 * MakeServiceCommand - Generate a new service class
 * 
 * @package    Oxygen\Console\Commands
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * Usage:
 *   php oxygen make:service PaymentService
 */
class MakeServiceCommand extends Command
{
    public function execute($arguments)
    {
        if (empty($arguments[0])) {
            $this->error('Service name is required.');
            $this->info('Usage: php oxygen make:service ServiceName');
            return;
        }

        $name = $arguments[0];
        $className = str_replace('Service', '', $name) . 'Service';
        $namespace = 'Oxygen\\Services';
        $path = __DIR__ . '/../../../app/Services/' . $className . '.php';

        $content = $this->getStub($className, $namespace);

        if ($this->createFile($path, $content)) {
            $this->success("Service created successfully: {$className}");
            $this->info("Location: {$path}");
        }
    }

    protected function getStub($className, $namespace)
    {
        return <<<PHP
<?php

namespace {$namespace};

/**
 * {$className}
 * 
 * Service class for handling business logic.
 * 
 * @package    {$namespace}
 * @author     OxygenFramework
 * @copyright  2024 - OxygenFramework
 */
class {$className}
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize service dependencies
    }

    /**
     * Example method
     * 
     * @return mixed
     */
    public function handle()
    {
        // Implement your service logic here
    }
}

PHP;
    }
}
