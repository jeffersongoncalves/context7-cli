<?php

namespace App\Commands\Auth;

use App\DTOs\Credentials;
use App\Services\AuthService;
use LaravelZero\Framework\Commands\Command;

class SaveCommand extends Command
{
    /**
     * Non-interactive by design (see ExampleCommand): the key is an argument,
     * with an env-var fallback, so scripts and AI agents never need a TTY.
     *
     * @var string
     */
    protected $signature = 'auth:save {api-key? : Context7 API key from https://context7.com/dashboard (defaults to $CONTEXT7_API_KEY)}';

    protected $description = 'Save a Context7 API key';

    public function handle(AuthService $authService): int
    {
        $apiKey = $this->argument('api-key') ?? (getenv('CONTEXT7_API_KEY') ?: null);

        if (! $apiKey) {
            $this->components->error('Provide an API key as an argument or set CONTEXT7_API_KEY.');

            return self::FAILURE;
        }

        $authService->save(new Credentials($apiKey));

        $this->components->info("Credentials saved to {$authService->getConfigPath()}");

        return self::SUCCESS;
    }
}
