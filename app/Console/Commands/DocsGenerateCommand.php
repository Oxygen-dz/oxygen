<?php

namespace Oxygen\Console\Commands;

use Oxygen\Console\Command;
use Oxygen\Core\Docs\OxygenDocs;

/**
 * DocsGenerateCommand - Generate API Documentation
 */
class DocsGenerateCommand extends Command
{
    protected $name = 'docs:generate';
    protected $description = 'Generate API documentation';

    public function execute($args)
    {
        $this->info("Generating API documentation...");

        $html = OxygenDocs::generate();
        $file = __DIR__ . '/../../../public/docs.html';

        file_put_contents($file, $html);

        $this->success("✓ Documentation generated!");
        $this->info("View at: http://localhost:8000/docs.html");
    }
}
