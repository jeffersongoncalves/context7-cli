<?php

namespace App\Commands;

use App\Services\Context7Service;
use JeffersonGoncalves\LaravelZero\Console\FormatsOutput;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class SearchCommand extends Command
{
    use FormatsOutput, HandlesApiErrors;

    protected $signature = 'search {library-name : Library name to search for, e.g. "react"}
        {query : Your question or task, used to rank results by relevance}
        {--fast : Skip the slower, more thorough ranking pass}';

    protected $description = 'Search Context7 for libraries matching a name';

    public function handle(Context7Service $context7): int
    {
        return $this->handleApiErrors(function () use ($context7) {
            $response = $context7->searchLibraries(
                $this->argument('library-name'),
                $this->argument('query'),
                (bool) $this->option('fast'),
            );

            $this->renderTable(
                ['ID', 'Title', 'Trust Score', 'Snippets', 'Stars'],
                array_map(fn (array $library): array => [
                    $library['id'] ?? '-',
                    $library['title'] ?? '-',
                    $library['trustScore'] ?? '-',
                    $library['totalSnippets'] ?? '-',
                    $library['stars'] ?? '-',
                ], $response['results'] ?? []),
            );

            return self::SUCCESS;
        });
    }
}
