<?php

namespace App\Commands\Auth;

use App\Services\AuthService;
use LaravelZero\Framework\Commands\Command;

class ForgetCommand extends Command
{
    protected $signature = 'auth:forget';

    protected $description = 'Remove the saved Context7 API key';

    public function handle(AuthService $authService): int
    {
        $authService->forget();

        $this->components->info('Context7 API key removed.');

        return self::SUCCESS;
    }
}
