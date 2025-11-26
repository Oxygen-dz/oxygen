<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;
use Oxygen\Core\WebSocket\OxygenWebSocket;

/**
 * WebSocketCommand - Start WebSocket Server
 * 
 * @package    Oxygen\Console\Commands
 * @author     Redwan Aouni <aouniradouan@gmail.com>
<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;
use Oxygen\Core\WebSocket\OxygenWebSocket;

/**
 * WebSocketCommand - Start WebSocket Server
 * 
 * @package    Oxygen\Console\Commands
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 */
class WebSocketCommand extends Command
{
    protected $name = 'websocket:serve';
    protected $description = 'Start the OxygenWebSocket server for real-time features';

    public function execute($args)
    {
        $port = 8080;

        // Check for --port option
        foreach ($args as $arg) {
            if (strpos($arg, '--port=') === 0) {
                $port = (int) substr($arg, 7);
            }
        }

        $this->info("Starting OxygenWebSocket server...");
        $this->info("Port: $port");
        $this->line("");

        $server = new OxygenWebSocket();
        $server->start($port);
    }
}
