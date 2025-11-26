<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;
use Oxygen\Core\Database\OxygenMigrator;

/**
 * MigrateRollbackCommand - Rollback last batch of migrations
 * 
 * Usage: php oxygen migrate:rollback
 */
class MigrateRollbackCommand extends Command
{
    public function execute($arguments)
    {
        $this->info("Rolling back migrations...");

        $migrator = new OxygenMigrator();
        $result = $migrator->rollback();

        if (isset($result['message'])) {
            $this->warning($result['message']);
        } else {
            foreach ($result['rolled_back'] as $migration) {
                $this->success("Rolled back: {$migration}");
            }
        }
    }
}
