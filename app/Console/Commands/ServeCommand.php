<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;

/**
 * ServeCommand - Start the PHP development server
 * 
 * @package    Oxygen\Console\Commands
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * Usage:
 *   php oxygen serve
 *   php oxygen serve --port=8080
 */
class ServeCommand extends Command
{
    public function execute($arguments)
    {
        // Parse arguments for custom port
        $port = 8000;
        foreach ($arguments as $arg) {
            if (strpos($arg, '--port=') === 0) {
                $port = (int) str_replace('--port=', '', $arg);
            }
        }

        $host = '127.0.0.1';
        $docRoot = __DIR__ . '/../../../';

        $this->info("OxygenFramework development server started");
        $this->success("Server running at: http://{$host}:{$port}");
        $this->warning("Press Ctrl+C to stop the server");
        echo "\n";

        // Start the PHP built-in server
        $command = "php -S {$host}:{$port} -t \"{$docRoot}\"";
        passthru($command);
    }
}
