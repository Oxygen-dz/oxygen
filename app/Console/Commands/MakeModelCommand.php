<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;

/**
 * MakeModelCommand - Generate a new model class
 * 
 * This command creates a new model file with the proper namespace
 * and extends the base Model class.
 * 
 * @package    Oxygen\Console\Commands
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * Usage:
 *   php oxygen make:model User
 *   php oxygen make:model Blog/Post
 */
class MakeModelCommand extends Command
{
    /**
     * Execute the command
     * 
     * @param array $arguments Command arguments
     * @return void
     */
    public function execute($arguments)
    {
        // Check if name argument is provided
        if (empty($arguments[0])) {
            $this->error('Model name is required.');
            $this->info('Usage: php oxygen make:model ModelName');
            return;
        }

        $name = $arguments[0];
        $className = $name;

        // Determine the path
        $basePath = __DIR__ . '/../../../app/Models/';

        // Handle subdirectories (e.g., Blog/Post)
        if (strpos($name, '/') !== false) {
            $parts = explode('/', $name);
            $className = array_pop($parts);
            $namespace = 'Oxygen\\Models\\' . implode('\\', $parts);
            $path = $basePath . implode('/', $parts) . '/' . $className . '.php';
        } else {
            $namespace = 'Oxygen\\Models';
            $path = $basePath . $className . '.php';
        }

        // Generate model content
        $content = $this->getStub($className, $namespace);

        // Create the file
        if ($this->createFile($path, $content)) {
            $this->success("Model created successfully: {$className}");
            $this->info("Location: {$path}");
            $this->warning("Don't forget to:");
            $this->info("  1. Set the \$table property if it doesn't follow convention");
            $this->info("  2. Define \$fillable or \$guarded properties for mass assignment");
            $this->info("  3. Create a migration for the database table");
        }
    }

    /**
     * Get the model stub/template
     * 
     * @param string $className Class name
     * @param string $namespace Namespace
     * @return string
     */
    protected function getStub($className, $namespace)
    {
        $tableName = strtolower($className) . 's'; // Simple pluralization

        return <<<PHP
<?php

namespace {$namespace};

use Oxygen\Core\Model;

/**
 * {$className} Model
 * 
 * Represents a {$className} in the database.
 * 
 * @package    {$namespace}
 * @author     OxygenFramework
 * @copyright  2024 - OxygenFramework
 */
class {$className} extends Model
{
    /**
     * The table associated with the model
     * 
     * @var string
     */
    protected \$table = '{$tableName}';

    /**
     * The primary key for the model
     * 
     * @var string
     */
    protected \$primaryKey = 'id';

    /**
     * The attributes that are mass assignable
     * 
     * Define which attributes can be filled via mass assignment (create, update)
     * 
     * @var array
     */
    protected \$fillable = [
        // 'name',
        // 'email',
        // Add your fillable fields here
    ];

    /**
     * The attributes that should be hidden for arrays
     * 
     * @var array
     */
    protected \$hidden = [
        // 'password',
        // Add fields to hide from JSON/array output
    ];

    // Add your custom methods here
    
    /**
     * Example: Get all active records
     * 
     * @return array
     */
    // public static function active()
    // {
    //     return static::where('status', '=', 'active');
    // }
}

PHP;
    }
}
