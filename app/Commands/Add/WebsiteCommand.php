<?php

namespace App\Commands\Add;

use App\Exceptions\AuthenticationException;
use App\Services\AuthService;
use App\Services\Context7Service;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class WebsiteCommand extends Command
{
    use HandlesApiErrors;

    protected $signature = 'add:website {website-url : URL of the website to index}
        {--base-url= : Restrict crawling to pages under this base URL}';

    protected $description = 'Submit a website to Context7 for indexing (requires an API key)';

    public function handle(Context7Service $context7, AuthService $authService): int
    {
        return $this->handleApiErrors(function () use ($context7, $authService) {
            if (! $authService->isAuthenticated()) {
                throw new AuthenticationException;
            }

            $response = $context7->addWebsite($this->argument('website-url'), $this->option('base-url'));

            $this->components->info($response['message'] ?? 'Website submitted successfully.');

            if (! empty($response['libraryName'])) {
                $this->components->twoColumnDetail('Library ID', $response['libraryName']);
            }

            return self::SUCCESS;
        });
    }
}
