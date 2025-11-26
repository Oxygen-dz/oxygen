<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;

/**
 * ListCommand - Display all available commands
 * 
 * @package    Oxygen\Console\Commands
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class ListCommand extends Command
{
    public function execute($arguments)
    {
        echo "\n";
        echo "\033[36m╔═══════════════════════════════════════════════════════════╗\033[0m\n";
        echo "\033[36m║         OxygenFramework CLI - Available Commands         ║\033[0m\n";
        echo "\033[36m╚═══════════════════════════════════════════════════════════╝\033[0m\n";
        echo "\n";

        $commands = [
            'make:controller' => 'Create a new controller class',
            'make:model' => 'Create a new model class',
            'make:middleware' => 'Create a new middleware class',
            'make:service' => 'Create a new service class',
            'make:migration' => 'Create a new migration file',
            'migrate' => 'Run database migrations',
            'migrate:rollback' => 'Rollback the last migration batch',
            'serve' => 'Start the development server',
            'list' => 'Show this help message',
        ];

        foreach ($commands as $command => $description) {
            printf("  \033[32m%-20s\033[0m %s\n", $command, $description);
        }

        echo "\n";
        echo "\033[33mUsage:\033[0m\n";
        echo "  php oxygen <command> [arguments]\n";
        echo "\n";
        echo "\033[33mExamples:\033[0m\n";
        echo "  php oxygen make:controller UserController\n";
        echo "  php oxygen make:model Post\n";
        echo "  php oxygen serve --port=8080\n";
        echo "\n";
    }
}
