<?php

namespace App\Commands\Add;

use App\Exceptions\AuthenticationException;
use App\Services\AuthService;
use App\Services\Context7Service;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class GitCommand extends Command
{
    use HandlesApiErrors;

    protected $signature = 'add:git {repo-url : Repository URL, for any self-hosted or other git host}
        {--git-token= : Git access token, for private repositories}
        {--private : Mark the repository as private}
        {--skip-version-filtering : Skip filtering out version-specific documentation pages}
        {--generate-docs : Generate documentation from the repository source code}';

    protected $description = 'Submit a repository from any git host to Context7 for indexing (requires an API key)';

    public function handle(Context7Service $context7, AuthService $authService): int
    {
        return $this->handleApiErrors(function () use ($context7, $authService) {
            if (! $authService->isAuthenticated()) {
                throw new AuthenticationException;
            }

            $response = $context7->addGitRepo(
                $this->argument('repo-url'),
                $this->option('git-token'),
                $this->option('private') ? true : null,
                $this->option('skip-version-filtering') ? true : null,
                $this->option('generate-docs') ? true : null,
            );

            $this->components->info($response['message'] ?? 'Repository submitted successfully.');

            if (! empty($response['libraryName'])) {
                $this->components->twoColumnDetail('Library ID', $response['libraryName']);
            }

            return self::SUCCESS;
        });
    }
}
