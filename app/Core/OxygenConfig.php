<?php

namespace Oxygen\Core;

/**
 * OxygenConfig - Configuration Management System
 * 
 * This class provides a centralized, elegant way to access configuration values
 * throughout your OxygenFramework application. It supports dot notation for
 * nested array access and caching for performance.
 * 
 * @package    Oxygen\Core
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * // Get a config value
 * $appName = OxygenConfig::get('app.name');
 * 
 * // Get with default value
 * $debug = OxygenConfig::get('app.debug', false);
 * 
 * // Set a config value at runtime
 * OxygenConfig::set('app.timezone', 'UTC');
 * 
 * // Check if config exists
 * if (OxygenConfig::has('database.connections.mysql')) {
 *     // Do something
 * }
 */
class OxygenConfig
{
    /**
     * Loaded configuration data
     * 
     * @var array
     */
    protected static $config = [];

    /**
     * Base path for configuration files
     * 
     * @var string
     */
    protected static $configPath;

    /**
     * Initialize the configuration system
     * 
     * @param string $configPath Path to the config directory
     * @return void
     */
    public static function init($configPath)
    {
        static::$configPath = $configPath;
        static::loadAllConfigs();
    }

    /**
     * Load all configuration files from the config directory
     * 
     * @return void
     */
    protected static function loadAllConfigs()
    {
        $configFiles = glob(static::$configPath . '/*.php');

        foreach ($configFiles as $file) {
            $key = basename($file, '.php');
            static::$config[$key] = require $file;
        }
    }

    /**
     * Get a configuration value using dot notation
     * 
     * Examples:
     * - get('app.name') returns $config['app']['name']
     * - get('database.connections.mysql.host') returns nested value
     * 
     * @param string $key Configuration key in dot notation
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = static::$config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Set a configuration value using dot notation
     * 
     * Note: This only affects runtime configuration, not the actual config files
     * 
     * @param string $key Configuration key in dot notation
     * @param mixed $value Value to set
     * @return void
     */
    public static function set($key, $value)
    {
        $keys = explode('.', $key);
        $config = &static::$config;

        while (count($keys) > 1) {
            $segment = array_shift($keys);

            if (!isset($config[$segment]) || !is_array($config[$segment])) {
                $config[$segment] = [];
            }

            $config = &$config[$segment];
        }

        $config[array_shift($keys)] = $value;
    }

    /**
     * Check if a configuration value exists
     * 
     * @param string $key Configuration key in dot notation
     * @return bool
     */
    public static function has($key)
    {
        $keys = explode('.', $key);
        $value = static::$config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return false;
            }
            $value = $value[$segment];
        }

        return true;
    }

    /**
     * Get all configuration data
     * 
     * @return array
     */
    public static function all()
    {
        return static::$config;
    }

    /**
     * Get all configuration for a specific file
     * 
     * @param string $file Configuration file name (without .php)
     * @return array|null
     */
    public static function file($file)
    {
        return static::$config[$file] ?? null;
    }
}
