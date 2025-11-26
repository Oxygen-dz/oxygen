<?php

namespace Oxygen\Core\Database;

use Oxygen\Core\Application;

/**
 * OxygenMigrator - Migration Runner
 * 
 * Handles running and rolling back database migrations.
 * 
 * @package    Oxygen\Core\Database
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenMigrator
{
    protected $db;
    protected $migrationsPath;

    public function __construct()
    {
        $this->db = Application::getInstance()->make('db');
        $this->migrationsPath = Application::getInstance()->basePath('database/migrations');
        $this->ensureMigrationsTableExists();
    }

    /**
     * Create migrations tracking table if it doesn't exist
     */
    protected function ensureMigrationsTableExists()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `migrations` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `migration` VARCHAR(255) NOT NULL,
                `batch` INT NOT NULL,
                `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Run all pending migrations
     */
    public function migrate()
    {
        $files = $this->getMigrationFiles();
        echo "Found " . count($files) . " migration files.\n";

        $ran = $this->getRanMigrations();
        echo "Found " . count($ran) . " already ran migrations.\n";

        $batch = $this->getNextBatchNumber();

        $pending = array_diff($files, $ran);
        echo "Pending migrations: " . count($pending) . "\n";

        if (empty($pending)) {
            return ['message' => 'Nothing to migrate'];
        }

        $migrated = [];
        foreach ($pending as $file) {
            echo "Migrating: $file\n";
            $this->runMigration($file, $batch);
            $migrated[] = $file;
        }

        return ['migrated' => $migrated];
    }

    /**
     * Rollback the last batch of migrations
     */
    public function rollback()
    {
        $lastBatch = $this->getLastBatchNumber();

        if ($lastBatch === 0) {
            return ['message' => 'Nothing to rollback'];
        }

        $migrations = $this->db->query(
            "SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC",
            $lastBatch
        )->fetchAll();

        $rolledBack = [];
        foreach ($migrations as $migration) {
            $this->rollbackMigration($migration['migration']);
            $rolledBack[] = $migration['migration'];
        }

        return ['rolled_back' => $rolledBack];
    }

    /**
     * Run a single migration
     */
    protected function runMigration($file, $batch)
    {
        require_once $this->migrationsPath . '/' . $file;

        $className = $this->getClassNameFromFile($file);
        echo "Resolved class name: $className\n";

        if (!class_exists($className)) {
            echo "Error: Class '$className' not found in $file\n";
            return;
        }

        $migration = new $className();
        $migration->up();

        $this->db->query(
            "INSERT INTO migrations (migration, batch) VALUES (?, ?)",
            $file,
            $batch
        );
    }

    /**
     * Rollback a single migration
     */
    protected function rollbackMigration($file)
    {
        require_once $this->migrationsPath . '/' . $file;

        $className = $this->getClassNameFromFile($file);
        $migration = new $className();
        $migration->down();

        $this->db->query("DELETE FROM migrations WHERE migration = ?", $file);
    }

    /**
     * Get all migration files
     */
    protected function getMigrationFiles()
    {
        $files = glob($this->migrationsPath . '/*.php');
        return array_map('basename', $files);
    }

    /**
     * Get migrations that have already been run
     */
    protected function getRanMigrations()
    {
        return $this->db->query("SELECT migration FROM migrations")->fetchPairs(null, 'migration');
    }

    /**
     * Get the next batch number
     */
    protected function getNextBatchNumber()
    {
        $max = $this->db->query("SELECT MAX(batch) as max_batch FROM migrations")->fetch();
        return ($max['max_batch'] ?? 0) + 1;
    }

    /**
     * Get the last batch number
     */
    protected function getLastBatchNumber()
    {
        $max = $this->db->query("SELECT MAX(batch) as max_batch FROM migrations")->fetch();
        return $max['max_batch'] ?? 0;
    }

    /**
     * Extract class name from migration file
     */
    protected function getClassNameFromFile($file)
    {
        // Remove timestamp and .php extension
        // e.g., "2024_01_01_000000_create_users_table.php" -> "CreateUsersTable"
        $parts = explode('_', $file);
        array_shift($parts); // Remove year
        array_shift($parts); // Remove month
        array_shift($parts); // Remove day
        array_shift($parts); // Remove time
        $name = implode('_', $parts);
        $name = str_replace('.php', '', $name);

        return str_replace('_', '', ucwords($name, '_'));
    }
}
