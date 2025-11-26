<?php

namespace Oxygen\Console;

/**
 * Command - Base class for all console commands
 * 
 * All console commands should extend this class and implement the execute() method.
 * 
 * @package    Oxygen\Console
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
abstract class Command
{
    /**
     * Execute the command
     * 
     * @param array $arguments Command arguments
     * @return void
     */
    abstract public function execute($arguments);

    /**
     * Display a success message
     * 
     * @param string $message Success message
     * @return void
     */
    protected function success($message)
    {
        echo "\033[32m✓ {$message}\033[0m\n";
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

    /**
     * Display a warning message
     * 
     * @param string $message Warning message
     * @return void
     */
    protected function warning($message)
    {
        echo "\033[33m⚠ {$message}\033[0m\n";
    }

    /**
     * Create a file with content
     * 
     * @param string $path File path
     * @param string $content File content
     * @return bool
     */
    protected function createFile($path, $content)
    {
        // Create directory if it doesn't exist
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Check if file already exists
        if (file_exists($path)) {
            $this->error("File already exists: {$path}");
            return false;
        }

        // Write file
        file_put_contents($path, $content);
        return true;
    }
}
