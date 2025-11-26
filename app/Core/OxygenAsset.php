<?php

namespace Oxygen\Core;

/**
 * OxygenAsset - Asset Management System
 * 
 * Manages CSS, JavaScript, and image assets with versioning,
 * minification, and CDN support.
 * 
 * @package    Oxygen\Core
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * // Add CSS
 * OxygenAsset::css('app.css');
 * OxygenAsset::css('https://cdn.example.com/style.css');
 * 
 * // Add JavaScript
 * OxygenAsset::js('app.js');
 * 
 * // Render in template
 * {{ oxygen_css()|raw }}
 * {{ oxygen_js()|raw }}
 */
class OxygenAsset
{
    protected static $css = [];
    protected static $js = [];
    protected static $basePath = '/assets/';
    protected static $version = '1.0.0';

    /**
     * Add a CSS file
     * 
     * @param string $file CSS file path or URL
     * @param array $attributes Additional HTML attributes
     * @return void
     */
    public static function css($file, $attributes = [])
    {
        static::$css[] = [
            'file' => $file,
            'attributes' => $attributes
        ];
    }

    /**
     * Add a JavaScript file
     * 
     * @param string $file JS file path or URL
     * @param array $attributes Additional HTML attributes
     * @return void
     */
    public static function js($file, $attributes = [])
    {
        static::$js[] = [
            'file' => $file,
            'attributes' => $attributes
        ];
    }

    /**
     * Render all CSS tags
     * 
     * @return string
     */
    public static function renderCSS()
    {
        $html = '';
        foreach (static::$css as $asset) {
            $url = static::assetUrl($asset['file']);
            $attrs = static::buildAttributes($asset['attributes']);
            $html .= "<link rel=\"stylesheet\" href=\"{$url}\"{$attrs}>\n";
        }
        return $html;
    }

    /**
     * Render all JS tags
     * 
     * @return string
     */
    public static function renderJS()
    {
        $html = '';
        foreach (static::$js as $asset) {
            $url = static::assetUrl($asset['file']);
            $attrs = static::buildAttributes($asset['attributes']);
            $html .= "<script src=\"{$url}\"{$attrs}></script>\n";
        }
        return $html;
    }

    /**
     * Get asset URL with versioning
     * 
     * @param string $file File path
     * @return string
     */
    protected static function assetUrl($file)
    {
        // If it's a full URL, return as-is
        if (preg_match('/^https?:\/\//', $file)) {
            return $file;
        }

        $appUrl = OxygenConfig::get('app.APP_URL', '');
        $url = $appUrl . static::$basePath . $file;

        // Add version for cache busting
        return $url . '?v=' . static::$version;
    }

    /**
     * Build HTML attributes string
     * 
     * @param array $attributes
     * @return string
     */
    protected static function buildAttributes($attributes)
    {
        if (empty($attributes)) {
            return '';
        }

        $html = '';
        foreach ($attributes as $key => $value) {
            $html .= " {$key}=\"{$value}\"";
        }
        return $html;
    }

    /**
     * Set asset version for cache busting
     * 
     * @param string $version
     * @return void
     */
    public static function setVersion($version)
    {
        static::$version = $version;
    }

    /**
     * Set base path for assets
     * 
     * @param string $path
     * @return void
     */
    public static function setBasePath($path)
    {
        static::$basePath = $path;
    }
}
