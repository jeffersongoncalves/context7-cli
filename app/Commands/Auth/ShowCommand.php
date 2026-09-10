<?php

namespace App\Commands\Auth;

use App\Services\AuthService;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class ShowCommand extends Command
{
    use HandlesApiErrors;

    protected $signature = 'auth:show';

    protected $description = 'Show the saved Context7 API key';

    public function handle(AuthService $authService): int
    {
        return $this->handleApiErrors(function () use ($authService) {
            $credentials = $authService->load();

            if (! $credentials) {
                $this->components->warn('No Context7 API key saved. Run "context7 auth:save <api-key>".');

                return self::SUCCESS;
            }

            $this->components->twoColumnDetail('API Key', str_repeat('*', 8).substr($credentials->apiKey, -4));
            $this->components->twoColumnDetail('Config Path', $authService->getConfigPath());

            return self::SUCCESS;
        });
    }
}
