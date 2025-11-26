<?php

namespace Oxygen\Console;

/**
 * OxygenKernel - Console Command Handler
 * 
 * This class manages all console commands and routes CLI input to the
 * appropriate command handlers.
 * 
 * @package    Oxygen\Console
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenKernel
{
    /**
     * Registered commands
     * 
     * @var array
     */
    protected $commands = [];

    /**
     * Constructor - Register all commands
     */
    public function __construct()
    {
        $this->registerCommands();
    }

    /**
     * Register all available commands
     */
    protected function registerCommands()
    {
        $this->commands = [
            'make:controller' => \Oxygen\Console\Commands\MakeControllerCommand::class,
            'make:model' => \Oxygen\Console\Commands\MakeModelCommand::class,
            'make:middleware' => \Oxygen\Console\Commands\MakeMiddlewareCommand::class,
            'make:service' => \Oxygen\Console\Commands\MakeServiceCommand::class,
            'make:migration' => \Oxygen\Console\Commands\MakeMigrationCommand::class,
            'migrate' => \Oxygen\Console\Commands\MigrateCommand::class,
            'migrate:rollback' => \Oxygen\Console\Commands\MigrateRollbackCommand::class,
            'serve' => \Oxygen\Console\Commands\ServeCommand::class,
            'websocket:serve' => \Oxygen\Console\Commands\WebSocketCommand::class,
            'queue:work' => \Oxygen\Console\Commands\QueueWorkCommand::class,
            'docs:generate' => \Oxygen\Console\Commands\DocsGenerateCommand::class,
            'list' => \Oxygen\Console\Commands\ListCommand::class,
        ];
    }

    /**
     * Handle the console command
     */
    public function handle($argv)
    {
        // Remove script name
        array_shift($argv);

        // Get command name
        $commandName = $argv[0] ?? 'list';

        // Check if command exists
        if (!isset($this->commands[$commandName])) {
            $this->error("Command '{$commandName}' not found.");
            $this->info("Run 'php oxygen list' to see all available commands.");
            return;
        }

        // Get command arguments
        $arguments = array_slice($argv, 1);

        // Execute command
        $commandClass = $this->commands[$commandName];
        $command = new $commandClass();
        $command->execute($arguments);
    }

    /**
     * Display an error message
     * 
     * @param string $message Error message
     * @return void
     */
    protected function error($message)
    {
        echo "\033[31m✗ {$message}\033[0m\n";
    }

    /**
     * Display an info message
     * 
     * @param string $message Info message
     * @return void
     */
    protected function info($message)
    {
        echo "\033[36mℹ {$message}\033[0m\n";
    }
}
