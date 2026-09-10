<?php

namespace App\Commands\Add;

use App\Exceptions\AuthenticationException;
use App\Services\AuthService;
use App\Services\Context7Service;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class BitbucketCommand extends Command
{
    use HandlesApiErrors;

    protected $signature = 'add:bitbucket {repo-url : Bitbucket repository URL}
        {--git-token= : Git access token, for private repositories}';

    protected $description = 'Submit a Bitbucket repository to Context7 for indexing (requires an API key)';

    public function handle(Context7Service $context7, AuthService $authService): int
    {
        return $this->handleApiErrors(function () use ($context7, $authService) {
            if (! $authService->isAuthenticated()) {
                throw new AuthenticationException;
            }

            $response = $context7->addBitbucketRepo($this->argument('repo-url'), $this->option('git-token'));

            $this->components->info($response['message'] ?? 'Repository submitted successfully.');

            if (! empty($response['libraryName'])) {
                $this->components->twoColumnDetail('Library ID', $response['libraryName']);
            }

            return self::SUCCESS;
        });
    }
}
