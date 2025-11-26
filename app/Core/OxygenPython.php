<?php

namespace Oxygen\Core;

/**
 * OxygenPython - Python Integration for AI
 * 
 * Execute Python scripts from PHP, perfect for integrating AI/ML models,
 * data science libraries, and Python-based tools.
 * 
 * @package    Oxygen\Core
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * // Execute a Python script
 * $result = OxygenPython::execute('script.py', ['arg1', 'arg2']);
 * 
 * // Execute Python code directly
 * $result = OxygenPython::run('print("Hello from Python")');
 * 
 * // Call a Python function with JSON data
 * $result = OxygenPython::call('ai_model.py', 'predict', [
 *     'input' => 'some data'
 * ]);
 */
class OxygenPython
{
    /**
     * Python executable path
     * 
     * @var string
     */
    protected static $pythonPath = 'python';

    /**
     * Set custom Python executable path
     * 
     * @param string $path Path to Python executable
     * @return void
     */
    public static function setPythonPath($path)
    {
        static::$pythonPath = $path;
    }

    /**
     * Execute a Python script file
     * 
     * @param string $scriptPath Path to Python script
     * @param array $arguments Command line arguments
     * @return array ['output' => string, 'error' => string, 'code' => int]
     */
    public static function execute($scriptPath, $arguments = [])
    {
        if (!file_exists($scriptPath)) {
            return [
                'output' => '',
                'error' => "Script not found: {$scriptPath}",
                'code' => 1
            ];
        }

        $args = implode(' ', array_map('escapeshellarg', $arguments));
        $command = static::$pythonPath . ' ' . escapeshellarg($scriptPath) . ' ' . $args;

        return static::executeCommand($command);
    }

    /**
     * Execute Python code directly
     * 
     * @param string $code Python code to execute
     * @return array ['output' => string, 'error' => string, 'code' => int]
     */
    public static function run($code)
    {
        $command = static::$pythonPath . ' -c ' . escapeshellarg($code);
        return static::executeCommand($command);
    }

    /**
     * Call a Python function with JSON data
     * 
     * This expects the Python script to accept JSON input and return JSON output
     * 
     * @param string $scriptPath Path to Python script
     * @param string $function Function name to call
     * @param array $data Data to pass as JSON
     * @return mixed Decoded JSON response
     */
    public static function call($scriptPath, $function, $data = [])
    {
        $payload = json_encode([
            'function' => $function,
            'data' => $data
        ]);

        $command = static::$pythonPath . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($payload);
        $result = static::executeCommand($command);

        if ($result['code'] === 0) {
            return json_decode($result['output'], true);
        }

        return [
            'error' => $result['error'],
            'code' => $result['code']
        ];
    }

    /**
     * Execute a shell command and capture output
     * 
     * @param string $command Command to execute
     * @return array ['output' => string, 'error' => string, 'code' => int]
     */
    protected static function executeCommand($command)
    {
        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w']   // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            return [
                'output' => '',
                'error' => 'Failed to start process',
                'code' => 1
            ];
        }

        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $code = proc_close($process);

        return [
            'output' => trim($output),
            'error' => trim($error),
            'code' => $code
        ];
    }

    /**
     * Check if Python is available
     * 
     * @return bool
     */
    public static function isAvailable()
    {
        $result = static::run('print("OK")');
        return $result['code'] === 0 && $result['output'] === 'OK';
    }

    /**
     * Get Python version
     * 
     * @return string|null
     */
    public static function version()
    {
        $result = static::run('import sys; print(sys.version)');
        return $result['code'] === 0 ? $result['output'] : null;
    }
}
