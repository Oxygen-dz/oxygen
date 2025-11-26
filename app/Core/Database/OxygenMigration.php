<?php

namespace Oxygen\Core\Database;

use Oxygen\Core\Application;

/**
 * OxygenMigration - Base Migration Class
 * 
 * All database migrations should extend this class. Migrations allow you to
 * version control your database schema and easily rollback changes.
 * 
 * @package    Oxygen\Core\Database
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * class CreateUsersTable extends OxygenMigration
 * {
 *     public function up()
 *     {
 *         $this->execute("CREATE TABLE users (
 *             id INT AUTO_INCREMENT PRIMARY KEY,
 *             name VARCHAR(255),
 *             email VARCHAR(255) UNIQUE,
 *             password VARCHAR(255),
 *             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 *         )");
 *     }
 * 
 *     public function down()
 *     {
 *         $this->execute("DROP TABLE users");
 *     }
 * }
 */
abstract class OxygenMigration
{
    /**
     * Database connection
     * 
     * @var \Nette\Database\Connection
     */
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Application::getInstance()->make('db');
    }

    /**
     * Run the migration (create tables, add columns, etc.)
     * 
     * @return void
     */
    abstract public function up();

    /**
     * Reverse the migration (drop tables, remove columns, etc.)
     * 
     * @return void
     */
    abstract public function down();

    /**
     * Execute a SQL query
     * 
     * @param string $sql SQL query
     * @return mixed
     */
    protected function execute($sql)
    {
        return $this->db->query($sql);
    }

    /**
     * Create a table
     * 
     * @param string $table Table name
     * @param callable $callback Callback to define columns
     * @return void
     */
    protected function createTable($table, $callback)
    {
        $schema = new OxygenSchema($table);
        $callback($schema);
        $this->execute($schema->toSQL());
    }

    /**
     * Drop a table
     * 
     * @param string $table Table name
     * @return void
     */
    protected function dropTable($table)
    {
        $this->execute("DROP TABLE IF EXISTS `{$table}`");
    }

    /**
     * Check if table exists
     * 
     * @param string $table Table name
     * @return bool
     */
    protected function tableExists($table)
    {
        $result = $this->db->query("SHOW TABLES LIKE ?", $table)->fetch();
        return !empty($result);
    }
}
