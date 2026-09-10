<?php

namespace App\Commands;

use App\Exceptions\AuthenticationException;
use App\Services\AuthService;
use App\Services\Context7Service;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class RefreshCommand extends Command
{
    use HandlesApiErrors;

    protected $signature = 'refresh {library-id : Context7 library ID to refresh, e.g. "/vercel/next.js"}
        {--branch= : Branch to refresh (defaults to the indexed branch)}
        {--git-token= : Git access token, for private repositories}';

    protected $description = 'Trigger a documentation refresh for a library on Context7 (requires an API key)';

    public function handle(Context7Service $context7, AuthService $authService): int
    {
        return $this->handleApiErrors(function () use ($context7, $authService) {
            if (! $authService->isAuthenticated()) {
                throw new AuthenticationException;
            }

            $libraryId = $this->argument('library-id');

            $context7->refreshLibrary($libraryId, $this->option('branch'), $this->option('git-token'));

            $this->components->info("Refresh requested for {$libraryId}.");

            return self::SUCCESS;
        });
    }
}
