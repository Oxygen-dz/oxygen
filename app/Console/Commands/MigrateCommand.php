<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;
use Oxygen\Core\Database\OxygenMigrator;

/**
 * MigrateCommand - Run database migrations
 * 
 * Usage: php oxygen migrate
 */
class MigrateCommand extends Command
{
    public function execute($arguments)
    {
        $this->info("Running migrations...");

        try {
            $migrator = new OxygenMigrator();
            $result = $migrator->migrate();

            if (isset($result['message'])) {
                $this->warning($result['message']);
            } else {
                foreach ($result['migrated'] as $migration) {
                    $this->success("Migrated: {$migration}");
                }
            }
        } catch (\Exception $e) {
            $this->error("Migration Failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
