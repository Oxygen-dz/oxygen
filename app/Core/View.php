<?php

namespace Oxygen\Core;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFunction;

/**
 * View - Twig Template Engine Wrapper
 * 
 * Supports both .twig and .twig.html extensions
 * 
 * @package    Oxygen\Core
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class View
{
    protected $twig;
    protected $app;

    public function __construct()
    {
        $this->app = Application::getInstance();
        $this->setupTwig();
    }

    protected function setupTwig()
    {
        $defaultTemplate = OxygenConfig::get('config.default_template', 'Tabler');

        $templatePaths = [
            $this->app->basePath('resources/views/templates/' . $defaultTemplate),
            $this->app->basePath('resources/views/templates/' . $defaultTemplate . '/mobile'),
            $this->app->basePath('resources/views/templates/' . $defaultTemplate . '/desktop'),
            $this->app->basePath('resources/views'),
            $this->app->basePath('resources/views/admin'),
        ];

        $validPaths = array_filter($templatePaths, 'is_dir');

        if (empty($validPaths)) {
            $validPaths = [$this->app->basePath('resources/views')];
        }

        $loader = new FilesystemLoader($validPaths);

        $this->twig = new Environment($loader, [
            'debug' => OxygenConfig::get('app.APP_DEBUG', true),
            'cache' => false,
            'auto_reload' => true,
            'autoescape' => 'html',
        ]);

        $this->addGlobals();
        $GLOBALS['twig'] = $this->twig;
    }

    protected function addGlobals()
    {
        $appUrl = OxygenConfig::get('app.APP_URL', '');

        // App globals
        $this->twig->addGlobal('APP_URL', $appUrl);
        $this->twig->addGlobal('APP_NAME', OxygenConfig::get('app.APP_NAME', 'OxygenFramework'));

        // CSRF
        $csrf = $this->app->make(CSRF::class);
        $this->twig->addGlobal('csrf_token', $csrf->token());
        $this->twig->addGlobal('csrf_field', $csrf->field());

        // Auth
        $auth = $this->app->make(Auth::class);
        $this->twig->addGlobal('auth', [
            'check' => $auth->check(),
            'user' => $auth->user()
        ]);

        // Detect base path (for subdirectory installations like localhost/oxygenframework)
        $basePath = '';
        if (isset($_SERVER['REQUEST_URI']) && isset($_SERVER['SCRIPT_NAME'])) {
            $scriptName = dirname($_SERVER['SCRIPT_NAME']);
            if ($scriptName !== '/' && $scriptName !== '\\') {
                $basePath = $scriptName;
            }
        }

        // SHORT STORAGE FUNCTIONS - Laravel style
        $this->twig->addFunction(new TwigFunction('storage', function ($path) use ($appUrl, $basePath) {
            // If APP_URL is set and not localhost, use it
            if ($appUrl && !str_contains($appUrl, 'localhost')) {
                return $appUrl . '/storage/' . ltrim($path, '/');
            }
            // Otherwise use base path
            return $basePath . '/storage/' . ltrim($path, '/');
        }));

        $this->twig->addFunction(new TwigFunction('asset', function ($path) use ($appUrl, $basePath) {
            // If APP_URL is set and not localhost, use it
            if ($appUrl && !str_contains($appUrl, 'localhost')) {
                return $appUrl . '/' . ltrim($path, '/');
            }
            // Otherwise use base path
            return $basePath . '/' . ltrim($path, '/');
        }));

        $this->twig->addFunction(new TwigFunction('url', function ($path) use ($appUrl, $basePath) {
            // If APP_URL is set and not localhost, use it
            if ($appUrl && !str_contains($appUrl, 'localhost')) {
                return $appUrl . '/' . ltrim($path, '/');
            }
            // Otherwise use base path
            return $basePath . '/' . ltrim($path, '/');
        }));

        // Asset functions
        $this->twig->addFunction(new TwigFunction('oxygen_css', function () {
            return OxygenAsset::renderCSS();
        }));

        $this->twig->addFunction(new TwigFunction('oxygen_js', function () {
            return OxygenAsset::renderJS();
        }));

        $this->twig->addFunction(new TwigFunction('theme_asset', function ($path) {
            return OxygenTheme::asset($path);
        }));
    }

    /**
     * Render a template
     * Supports both .twig and .twig.html extensions
     * 
     * @param string $template Template file name
     * @param array $data Data to pass to template
     * @return string
     */
    public function render($template, $data = [])
    {
        // Auto-add .html if template ends with .twig
        if (substr($template, -5) === '.twig') {
            $htmlTemplate = $template . '.html';
            // Check if .twig.html version exists
            try {
                return $this->twig->render($htmlTemplate, $data);
            } catch (\Exception $e) {
                // Fall back to .twig
                return $this->twig->render($template, $data);
            }
        }

        return $this->twig->render($template, $data);
    }

    public function getTwig()
    {
        return $this->twig;
    }
}
