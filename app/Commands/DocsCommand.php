<?php

namespace App\Commands;

use App\Services\Context7Service;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class DocsCommand extends Command
{
    use HandlesApiErrors;

    protected $signature = 'docs {library-id : Context7 library ID, e.g. "/vercel/next.js" (see "search")}
        {query : Your question or task, used to select the most relevant snippets}
        {--type=txt : Response type: txt (concatenated docs text) or json (structured snippets)}
        {--fast : Skip the slower, more thorough ranking pass}';

    protected $description = 'Fetch up-to-date documentation for a library from Context7';

    public function handle(Context7Service $context7): int
    {
        return $this->handleApiErrors(function () use ($context7) {
            $response = $context7->getContext(
                $this->argument('library-id'),
                $this->argument('query'),
                (string) $this->option('type'),
                (bool) $this->option('fast'),
            );

            if (is_array($response)) {
                $this->line((string) json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

                return self::SUCCESS;
            }

            if (trim($response) === '') {
                $this->components->info('No documentation found.');

                return self::SUCCESS;
            }

            $this->line($response);

            return self::SUCCESS;
        });
    }
}
