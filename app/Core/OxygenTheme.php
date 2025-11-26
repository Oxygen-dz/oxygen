<?php

namespace Oxygen\Core;

/**
 * OxygenTheme - Theme Management System
 * 
 * Manage multiple themes for your application with easy switching
 * and theme-specific assets.
 * 
 * @package    Oxygen\Core
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * // Set active theme
 * OxygenTheme::set('dark');
 * 
 * // Get current theme
 * $theme = OxygenTheme::current();
 * 
 * // Get theme path
 * $path = OxygenTheme::path('views/layout.twig');
 */
class OxygenTheme
{
    protected static $currentTheme = 'default';
    protected static $themesPath = 'resources/themes/';

    /**
     * Set the active theme
     * 
     * @param string $theme Theme name
     * @return void
     */
    public static function set($theme)
    {
        static::$currentTheme = $theme;
        OxygenSession::put('oxygen_theme', $theme);
    }

    /**
     * Get the current theme
     * 
     * @return string
     */
    public static function current()
    {
        return OxygenSession::get('oxygen_theme', static::$currentTheme);
    }

    /**
     * Get path to a theme file
     * 
     * @param string $file File path within theme
     * @return string
     */
    public static function path($file = '')
    {
        $theme = static::current();
        $basePath = Application::getInstance()->basePath();
        return $basePath . '/' . static::$themesPath . $theme . '/' . $file;
    }

    /**
     * Get theme asset URL
     * 
     * @param string $asset Asset path
     * @return string
     */
    public static function asset($asset)
    {
        $theme = static::current();
        $appUrl = OxygenConfig::get('app.APP_URL', '');
        return $appUrl . '/' . static::$themesPath . $theme . '/assets/' . $asset;
    }

    /**
     * Check if theme exists
     * 
     * @param string $theme Theme name
     * @return bool
     */
    public static function exists($theme)
    {
        $path = static::path('');
        return is_dir(str_replace(static::current(), $theme, $path));
    }

    /**
     * Get all available themes
     * 
     * @return array
     */
    public static function all()
    {
        $basePath = Application::getInstance()->basePath();
        $themesDir = $basePath . '/' . static::$themesPath;

        if (!is_dir($themesDir)) {
            return [];
        }

        $themes = [];
        $dirs = scandir($themesDir);

        foreach ($dirs as $dir) {
            if ($dir !== '.' && $dir !== '..' && is_dir($themesDir . $dir)) {
                $themes[] = $dir;
            }
        }

        return $themes;
    }
}
