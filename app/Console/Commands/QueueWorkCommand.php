<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;
use Oxygen\Core\Queue\OxygenQueue;

/**
 * QueueWorkCommand - Process Queue Jobs
 */
class QueueWorkCommand extends Command
{
    protected $name = 'queue:work';
    protected $description = 'Process jobs in the queue';

    public function execute($args)
    {
        $this->info("Starting queue worker...");
        $this->line("");

        $processed = 0;

        while (true) {
            if (OxygenQueue::work()) {
                $processed++;
                $this->success("✓ Job processed ($processed total)");
            } else {
                sleep(1); // Wait for new jobs
            }
        }
    }
}
