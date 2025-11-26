<?php

namespace Oxygen\Core\Docs;

/**
 * OxygenDocs - Automatic API Documentation
 * 
 * Generates beautiful API documentation automatically.
 * Laravel doesn't have this built-in.
 * 
 * @package    Oxygen\Core\Docs
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenDocs
{
    protected static $routes = [];
    protected static $models = [];

    /**
     * Scan and generate documentation
     */
    public static function generate()
    {
        self::scanRoutes();
        self::scanModels();

        return self::renderHTML();
    }

    /**
     * Scan routes
     */
    protected static function scanRoutes()
    {
        // Get all routes from router
        $routeFile = __DIR__ . '/../../../routes/api.php';
        if (file_exists($routeFile)) {
            $content = file_get_contents($routeFile);

            // Parse OxygenAPI::resource calls
            preg_match_all('/OxygenAPI::resource\([\'"](\w+)[\'"],\s*(\w+)::class\)/', $content, $matches);

            for ($i = 0; $i < count($matches[0]); $i++) {
                $resource = $matches[1][$i];
                $model = $matches[2][$i];

                self::$routes[] = [
                    'resource' => $resource,
                    'model' => $model,
                    'endpoints' => [
                        ['method' => 'GET', 'path' => "/api/$resource", 'desc' => 'List all'],
                        ['method' => 'GET', 'path' => "/api/$resource/{id}", 'desc' => 'Get one'],
                        ['method' => 'POST', 'path' => "/api/$resource", 'desc' => 'Create'],
                        ['method' => 'PUT', 'path' => "/api/$resource/{id}", 'desc' => 'Update'],
                        ['method' => 'DELETE', 'path' => "/api/$resource/{id}", 'desc' => 'Delete'],
                    ]
                ];
            }
        }
    }

    /**
     * Scan models
     */
    protected static function scanModels()
    {
        $modelsDir = __DIR__ . '/../../../app/Models';
        if (!is_dir($modelsDir))
            return;

        $files = glob($modelsDir . '/*.php');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $name = basename($file, '.php');

            // Get fillable fields
            preg_match('/protected\s+\$fillable\s*=\s*\[(.*?)\]/s', $content, $matches);
            $fillable = [];
            if (isset($matches[1])) {
                preg_match_all('/[\'"](\w+)[\'"]/', $matches[1], $fields);
                $fillable = $fields[1];
            }

            self::$models[$name] = [
                'name' => $name,
                'fields' => $fillable
            ];
        }
    }

    /**
     * Render HTML documentation
     */
    protected static function renderHTML()
    {
        $routesHTML = '';
        foreach (self::$routes as $route) {
            $resource = $route['resource'];
            $model = $route['model'];
            $fields = self::$models[$model]['fields'] ?? [];
            $fieldsHTML = implode(', ', array_map(fn($f) => "<code>$f</code>", $fields));

            $endpointsHTML = '';
            foreach ($route['endpoints'] as $endpoint) {
                $method = $endpoint['method'];
                $path = $endpoint['path'];
                $desc = $endpoint['desc'];
                $methodClass = strtolower($method);

                $endpointsHTML .= <<<HTML
                <div class="endpoint">
                    <span class="method $methodClass">$method</span>
                    <span class="path">$path</span>
                    <span class="desc">$desc</span>
                </div>
HTML;
            }

            $routesHTML .= <<<HTML
            <div class="resource">
                <h3>$resource</h3>
                <div class="model-info">
                    <strong>Model:</strong> $model<br>
                    <strong>Fields:</strong> $fieldsHTML
                </div>
                <div class="endpoints">
                    $endpointsHTML
                </div>
            </div>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>API Documentation - OxygenFramework</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f9fafb;
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            font-size: 36px;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #6b7280;
            margin-bottom: 40px;
        }
        .resource {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .resource h3 {
            font-size: 24px;
            color: #667eea;
            margin-bottom: 15px;
        }
        .model-info {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #374151;
        }
        .model-info code {
            background: #e5e7eb;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
        .endpoints {
            margin-top: 20px;
        }
        .endpoint {
            display: flex;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .endpoint:last-child {
            border-bottom: none;
        }
        .method {
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            margin-right: 15px;
            min-width: 60px;
            text-align: center;
        }
        .method.get { background: #10b981; color: white; }
        .method.post { background: #3b82f6; color: white; }
        .method.put { background: #f59e0b; color: white; }
        .method.delete { background: #ef4444; color: white; }
        .path {
            font-family: monospace;
            color: #374151;
            flex: 1;
        }
        .desc {
            color: #6b7280;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            margin-top: 40px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 API Documentation</h1>
        <div class="subtitle">Auto-generated by OxygenFramework</div>
        
        $routesHTML
        
        <div class="footer">
            <strong>OxygenFramework 2.0</strong> - Automatic API Documentation
        </div>
    </div>
</body>
</html>
HTML;
    }
}
