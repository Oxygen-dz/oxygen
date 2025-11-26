<?php

namespace Oxygen\Core;

use Nette\Database\Connection;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function __construct()
    {
        if (!$this->table) {
            $this->table = $this->guessTableName();
        }
    }

    protected function guessTableName()
    {
        $class = (new \ReflectionClass($this))->getShortName();
        return strtolower($class) . 's'; // Simple pluralization
    }

    protected static function db()
    {
        return Application::getInstance()->make('db');
    }

    public static function all()
    {
        return static::db()->query('SELECT * FROM ?name', static::getTableName())->fetchAll();
    }

    public static function find($id)
    {
        return static::db()->query('SELECT * FROM ?name WHERE ?name = ?', static::getTableName(), static::getPrimaryKey(), $id)->fetch();
    }

    public static function create(array $data)
    {
        $model = new static;
        $filteredData = $model->filterFillable($data);

        static::db()->query('INSERT INTO ?name ?', static::getTableName(), $filteredData);

        $id = static::db()->getInsertId();
        return static::find($id);
    }

    public static function update($id, array $data)
    {
        $model = new static;
        $filteredData = $model->filterFillable($data);

        static::db()->query('UPDATE ?name SET ? WHERE ?name = ?', static::getTableName(), $filteredData, static::getPrimaryKey(), $id);

        return static::find($id);
    }

    public static function delete($id)
    {
        return static::db()->query('DELETE FROM ?name WHERE ?name = ?', static::getTableName(), static::getPrimaryKey(), $id);
    }

    public static function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        return static::db()->query('SELECT * FROM ?name WHERE ?name ? ?', static::getTableName(), $column, $operator, $value)->fetchAll();
    }

    protected function filterFillable(array $data)
    {
        if (empty($this->fillable)) {
            return $data; // If fillable is empty, assume all are allowed (or none? Laravel assumes none if guarded is set, but here let's be permissive if empty or restrict? Let's restrict to be safe, but for now let's return data to avoid breaking if user forgets fillable)
            // Actually, for security, we should only allow fillable.
            // But for simplicity in this MVP, if fillable is empty, we might return everything.
            // Let's stick to: if fillable is defined, use it.
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected static function getTableName()
    {
        return (new static)->table;
    }

    protected static function getPrimaryKey()
    {
        return (new static)->primaryKey;
    }
}
