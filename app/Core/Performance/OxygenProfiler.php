<?php

namespace Oxygen\Core\Performance;

/**
 * OxygenProfiler - Performance Monitoring
 * 
 * Track execution time, memory usage, and database queries.
 * Better than Laravel Debugbar - built-in and lightweight.
 * 
 * @package    Oxygen\Core\Performance
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenProfiler
{
    protected static $enabled = false;
    protected static $startTime = 0;
    protected static $startMemory = 0;
    protected static $queries = [];
    protected static $markers = [];

    /**
     * Start profiling
     */
    public static function start()
    {
        self::$enabled = true;
        self::$startTime = microtime(true);
        self::$startMemory = memory_get_usage();
    }

    /**
     * Add marker
     */
    public static function mark($name)
    {
        if (!self::$enabled)
            return;

        self::$markers[] = [
            'name' => $name,
            'time' => microtime(true) - self::$startTime,
            'memory' => memory_get_usage() - self::$startMemory
        ];
    }

    /**
     * Log database query
     */
    public static function logQuery($sql, $time)
    {
        if (!self::$enabled)
            return;

        self::$queries[] = [
            'sql' => $sql,
            'time' => $time
        ];
    }

    /**
     * Get performance report
     */
    public static function report()
    {
        if (!self::$enabled) {
            return ['enabled' => false];
        }

        $totalTime = microtime(true) - self::$startTime;
        $totalMemory = memory_get_usage() - self::$startMemory;
        $peakMemory = memory_get_peak_usage();

        return [
            'enabled' => true,
            'total_time' => round($totalTime * 1000, 2) . 'ms',
            'total_memory' => self::formatBytes($totalMemory),
            'peak_memory' => self::formatBytes($peakMemory),
            'queries_count' => count(self::$queries),
            'queries_time' => round(array_sum(array_column(self::$queries, 'time')) * 1000, 2) . 'ms',
            'markers' => self::$markers,
            'queries' => self::$queries
        ];
    }

    /**
     * Display performance bar (HTML)
     */
    public static function renderBar()
    {
        if (!self::$enabled)
            return '';

        $report = self::report();

        return <<<HTML
<div style="position: fixed; bottom: 0; left: 0; right: 0; background: #1f2937; color: white; padding: 10px 20px; font-family: monospace; font-size: 12px; z-index: 99999; display: flex; justify-content: space-between; box-shadow: 0 -2px 10px rgba(0,0,0,0.3);">
    <div><strong>⚡ OxygenProfiler</strong></div>
    <div>⏱️ {$report['total_time']}</div>
    <div>💾 {$report['total_memory']}</div>
    <div>📊 Peak: {$report['peak_memory']}</div>
    <div>🗄️ Queries: {$report['queries_count']} ({$report['queries_time']})</div>
</div>
HTML;
    }

    /**
     * Format bytes
     */
    protected static function formatBytes($bytes)
    {
        if ($bytes < 1024)
            return $bytes . 'B';
        if ($bytes < 1048576)
            return round($bytes / 1024, 2) . 'KB';
        return round($bytes / 1048576, 2) . 'MB';
    }
}
