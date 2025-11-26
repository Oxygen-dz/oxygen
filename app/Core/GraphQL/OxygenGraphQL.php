<?php

namespace Oxygen\Core\GraphQL;

/**
 * OxygenGraphQL - GraphQL Support
 * 
 * Modern API alternative to REST.
 * Laravel doesn't have this built-in.
 * 
 * @package    Oxygen\Core\GraphQL
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenGraphQL
{
    protected static $types = [];
    protected static $queries = [];
    protected static $mutations = [];

    /**
     * Define a GraphQL type
     */
    public static function type($name, $fields)
    {
        self::$types[$name] = $fields;
    }

    /**
     * Define a query
     */
    public static function query($name, $resolver)
    {
        self::$queries[$name] = $resolver;
    }

    /**
     * Define a mutation
     */
    public static function mutation($name, $resolver)
    {
        self::$mutations[$name] = $resolver;
    }

    /**
     * Execute GraphQL query
     */
    public static function execute($query, $variables = [])
    {
        $parsed = self::parseQuery($query);

        if ($parsed['type'] === 'query') {
            return self::executeQuery($parsed, $variables);
        } elseif ($parsed['type'] === 'mutation') {
            return self::executeMutation($parsed, $variables);
        }

        return ['errors' => ['Invalid query type']];
    }

    /**
     * Parse GraphQL query
     */
    protected static function parseQuery($query)
    {
        $query = trim($query);

        // Simple parser for basic queries
        if (strpos($query, 'mutation') === 0) {
            preg_match('/mutation\s*{\s*(\w+)/', $query, $matches);
            return [
                'type' => 'mutation',
                'name' => $matches[1] ?? ''
            ];
        }

        preg_match('/query\s*{\s*(\w+)|{\s*(\w+)/', $query, $matches);
        return [
            'type' => 'query',
            'name' => $matches[1] ?? $matches[2] ?? ''
        ];
    }

    /**
     * Execute query
     */
    protected static function executeQuery($parsed, $variables)
    {
        $name = $parsed['name'];

        if (!isset(self::$queries[$name])) {
            return ['errors' => ["Query '$name' not found"]];
        }

        $resolver = self::$queries[$name];
        $result = $resolver($variables);

        return ['data' => [$name => $result]];
    }

    /**
     * Execute mutation
     */
    protected static function executeMutation($parsed, $variables)
    {
        $name = $parsed['name'];

        if (!isset(self::$mutations[$name])) {
            return ['errors' => ["Mutation '$name' not found"]];
        }

        $resolver = self::$mutations[$name];
        $result = $resolver($variables);

        return ['data' => [$name => $result]];
    }

    /**
     * Generate schema
     */
    public static function schema()
    {
        $schema = "type Query {\n";
        foreach (self::$queries as $name => $resolver) {
            $schema .= "  $name: [String]\n";
        }
        $schema .= "}\n\n";

        $schema .= "type Mutation {\n";
        foreach (self::$mutations as $name => $resolver) {
            $schema .= "  $name: String\n";
        }
        $schema .= "}\n";

        return $schema;
    }
}
