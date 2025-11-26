<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;

/**
 * MakeControllerCommand - Generate a new controller class
 * 
 * This command creates a new controller file with the proper namespace
 * and boilerplate code.
 * 
 * @package    Oxygen\Console\Commands
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * Usage:
 *   php oxygen make:controller UserController
 *   php oxygen make:controller Admin/PostController
 */
class MakeControllerCommand extends Command
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
            $this->error('Controller name is required.');
            $this->info('Usage: php oxygen make:controller ControllerName');
            return;
        }

        $name = $arguments[0];

        // Remove "Controller" suffix if provided, we'll add it back
        $name = str_replace('Controller', '', $name);
        $className = $name . 'Controller';

        // Determine the path
        $basePath = __DIR__ . '/../../../app/Controllers/';

        // Handle subdirectories (e.g., Admin/PostController)
        if (strpos($name, '/') !== false) {
            $parts = explode('/', $name);
            $className = array_pop($parts) . 'Controller';
            $namespace = 'App\\Controllers\\' . implode('\\', $parts);
            $path = $basePath . implode('/', $parts) . '/' . $className . '.php';
        } else {
            $namespace = 'App\\Controllers';
            $path = $basePath . $className . '.php';
        }

        // Generate controller content
        $content = $this->getStub($className, $namespace);

        // Create the file
        if ($this->createFile($path, $content)) {
            $this->success("Controller created successfully: {$className}");
            $this->info("Location: {$path}");
        }
    }

    /**
     * Get the controller stub/template
     * 
     * @param string $className Class name
     * @param string $namespace Namespace
     * @return string
     */
    protected function getStub($className, $namespace)
    {
        return <<<PHP
<?php

namespace {$namespace};

use Controller;
use Oxygen\Core\Request;

/**
 * {$className}
 * 
 * Controller for handling {$className} related requests.
 * 
 * @package    {$namespace}
 * @author     OxygenFramework
 * @copyright  2024 - OxygenFramework
 */
class {$className} extends Controller
{
    /**
     * Display a listing of the resource
     * 
     * @return mixed
     */
    public function index()
    {
        // TODO: Implement index method
        return \$this->view('index.twig', [
            'title' => '{$className}'
        ]);
    }

    /**
     * Show the form for creating a new resource
     * 
     * @return mixed
     */
    public function create()
    {
        // TODO: Implement create method
        return \$this->view('create.twig');
    }

    /**
     * Store a newly created resource
     * 
     * @param Request \$request
     * @return mixed
     */
    public function store(Request \$request)
    {
        // TODO: Implement store method
        // Validate and save data
    }

    /**
     * Display the specified resource
     * 
     * @param int \$id Resource ID
     * @return mixed
     */
    public function show(\$id)
    {
        // TODO: Implement show method
        return \$this->view('show.twig', [
            'id' => \$id
        ]);
    }

    /**
     * Show the form for editing the specified resource
     * 
     * @param int \$id Resource ID
     * @return mixed
     */
    public function edit(\$id)
    {
        // TODO: Implement edit method
        return \$this->view('edit.twig', [
            'id' => \$id
        ]);
    }

    /**
     * Update the specified resource
     * 
     * @param Request \$request
     * @param int \$id Resource ID
     * @return mixed
     */
    public function update(Request \$request, \$id)
    {
        // TODO: Implement update method
        // Validate and update data
    }

    /**
     * Remove the specified resource
     * 
     * @param int \$id Resource ID
     * @return mixed
     */
    public function destroy(\$id)
    {
        // TODO: Implement destroy method
        // Delete the resource
    }
}

PHP;
    }
}
