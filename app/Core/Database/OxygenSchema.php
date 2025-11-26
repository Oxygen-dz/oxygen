<?php

namespace Oxygen\Core\Database;

/**
 * OxygenSchema - Database Schema Builder
 * 
 * Provides a fluent API for building database table schemas.
 * 
 * @package    Oxygen\Core\Database
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenSchema
{
    protected $table;
    protected $columns = [];
    protected $engine = 'InnoDB';
    protected $charset = 'utf8mb4';

    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * Add an auto-incrementing ID column
     */
    public function id($name = 'id')
    {
        $this->columns[] = "`{$name}` INT AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    /**
     * Add a string/varchar column
     */
    public function string($name, $length = 255)
    {
        $this->columns[] = "`{$name}` VARCHAR({$length})";
        return $this;
    }

    /**
     * Add a text column
     */
    public function text($name)
    {
        $this->columns[] = "`{$name}` TEXT";
        return $this;
    }

    /**
     * Add an integer column
     */
    public function integer($name)
    {
        $this->columns[] = "`{$name}` INT";
        return $this;
    }

    public function medium($name)
    {
        $this->columns[] = "`{$name}` MEDIUMINT";
        return $this;
    }

    public function long($name)
    {
        $this->columns[] = "`{$name}` BIGINT";
        return $this;
    }

    public function double($name)
    {
        $this->columns[] = "`{$name}` DOUBLE";
        return $this;
    }

    public function decimal($name)
    {
        $this->columns[] = "`{$name}` DECIMAL";
        return $this;
    }

    public function float($name)
    {
        $this->columns[] = "`{$name}` FLOAT";
        return $this;
    }

    /**
     * Add a boolean column
     */
    public function boolean($name)
    {
        $this->columns[] = "`{$name}` TINYINT(1) DEFAULT 0";
        return $this;
    }

    /**
     * Add timestamp columns (created_at, updated_at)
     */
    public function timestamps()
    {
        $this->columns[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    /**
     * Add UNIQUE constraint
     */
    public function unique()
    {
        $last = array_pop($this->columns);
        $this->columns[] = $last . ' UNIQUE';
        return $this;
    }

    /**
     * Allow NULL values
     */
    public function nullable()
    {
        $last = array_pop($this->columns);
        $this->columns[] = $last . ' NULL';
        return $this;
    }

    /**
     * Generate the CREATE TABLE SQL
     */
    public function toSQL()
    {
        $columns = implode(",\n    ", $this->columns);
        return "CREATE TABLE `{$this->table}` (\n    {$columns}\n) ENGINE={$this->engine} DEFAULT CHARSET={$this->charset}";
    }
}
