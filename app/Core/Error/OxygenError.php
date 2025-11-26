<?php

namespace Oxygen\Core\Error;

/**
 * OxygenError - Beautiful Error Pages
 * 
 * Better than Whoops, Laravel's error page, or any other framework.
 * 
 * @package    Oxygen\Core\Error
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenError
{
    /**
     * Render beautiful error page
     */
    public static function render($exception)
    {
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $code = self::getCodeSnippet($file, $line);

        http_response_code(500);

        echo self::getHTML($message, $file, $line, $code, $trace);
        exit;
    }

    /**
     * Get code snippet around error
     */
    protected static function getCodeSnippet($file, $line, $context = 5)
    {
        if (!file_exists($file)) {
            return [];
        }

        $lines = file($file);
        $start = max(0, $line - $context - 1);
        $end = min(count($lines), $line + $context);

        $snippet = [];
        for ($i = $start; $i < $end; $i++) {
            $snippet[$i + 1] = [
                'code' => rtrim($lines[$i]),
                'highlight' => ($i + 1) === $line
            ];
        }

        return $snippet;
    }

    /**
     * Generate beautiful HTML
     */
    protected static function getHTML($message, $file, $line, $code, $trace)
    {
        $codeHTML = '';
        foreach ($code as $num => $data) {
            $class = $data['highlight'] ? 'highlight' : '';
            $codeHTML .= "<div class='line $class'><span class='num'>$num</span><code>" . htmlspecialchars($data['code']) . "</code></div>";
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>OxygenFramework Error</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Monaco', 'Menlo', monospace;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 40px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #1f2937;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .header {
            background: #ef4444;
            padding: 30px;
            border-bottom: 3px solid #dc2626;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header .message {
            font-size: 18px;
            opacity: 0.9;
        }
        .location {
            background: #374151;
            padding: 20px 30px;
            font-size: 14px;
            border-bottom: 1px solid #4b5563;
        }
        .location strong { color: #fbbf24; }
        .code-section {
            padding: 30px;
        }
        .code-section h2 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #fbbf24;
        }
        .code {
            background: #111827;
            border-radius: 8px;
            padding: 20px;
            overflow-x: auto;
        }
        .line {
            display: flex;
            padding: 4px 0;
            font-size: 13px;
        }
        .line.highlight {
            background: rgba(239, 68, 68, 0.2);
            border-left: 3px solid #ef4444;
            padding-left: 10px;
        }
        .line .num {
            color: #6b7280;
            min-width: 50px;
            text-align: right;
            padding-right: 15px;
            user-select: none;
        }
        .line code {
            color: #e5e7eb;
            white-space: pre;
        }
        .trace {
            background: #111827;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 12px;
            color: #9ca3af;
            max-height: 300px;
            overflow-y: auto;
        }
        .footer {
            background: #111827;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #374151;
        }
        .footer strong { color: #fbbf24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💥 Oops! Something went wrong</h1>
            <div class="message">$message</div>
        </div>
        
        <div class="location">
            <strong>File:</strong> $file <strong>Line:</strong> $line
        </div>
        
        <div class="code-section">
            <h2>📝 Code Snippet</h2>
            <div class="code">
                $codeHTML
            </div>
            
            <h2 style="margin-top: 30px;">📚 Stack Trace</h2>
            <div class="trace">
                <pre>$trace</pre>
            </div>
        </div>
        
        <div class="footer">
            <strong>OxygenFramework 2.0</strong> - The most beautiful error pages in PHP
        </div>
    </div>
</body>
</html>
HTML;
    }
}
