<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;

/**
 * MakeMigrationCommand - Generate a new migration file
 * 
 * Usage: php oxygen make:migration create_users_table
 */
class MakeMigrationCommand extends Command
{
    public function execute($arguments)
    {
        if (empty($arguments[0])) {
            $this->error('Migration name is required.');
            $this->info('Usage: php oxygen make:migration create_users_table');
            return;
        }

        $name = $arguments[0];
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_{$name}.php";
        $className = str_replace('_', '', ucwords($name, '_'));

        $path = __DIR__ . '/../../../database/migrations/' . $filename;
        $content = $this->getStub($className);

        if ($this->createFile($path, $content)) {
            $this->success("Migration created: {$filename}");
            $this->info("Location: {$path}");
        }
    }

    protected function getStub($className)
    {
        return <<<PHP
<?php

use Oxygen\Core\Database\OxygenMigration;

/**
 * {$className} Migration
 */
class {$className} extends OxygenMigration
{
    /**
     * Run the migration
     */
    public function up()
    {
        // Example: Create a table
        \$this->createTable('table_name', function(\$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('email')->unique();
            \$table->timestamps();
        });
        
        // Or use raw SQL
        // \$this->execute("CREATE TABLE ...");
    }

    /**
     * Reverse the migration
     */
    public function down()
    {
        \$this->dropTable('table_name');
    }
}

PHP;
    }
}
