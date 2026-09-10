<?php

namespace App\Commands\Auth;

use App\DTOs\Credentials;
use App\Services\AuthService;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\password;

class SaveCommand extends Command
{
    /**
     * The key can come from an argument (scripts/AI agents), --from-env (CI,
     * reads $CONTEXT7_API_KEY explicitly), or an interactive password prompt
     * when neither is given. (--env is already a built-in Artisan option for
     * selecting the app environment, so it can't be reused here.)
     *
     * @var string
     */
    protected $signature = 'auth:save {api-key? : Context7 API key from https://context7.com/dashboard}
        {--from-env : Read the key from $CONTEXT7_API_KEY instead of prompting}';

    protected $description = 'Save a Context7 API key';

    public function handle(AuthService $authService): int
    {
        $apiKey = $this->argument('api-key');

        if (! $apiKey && $this->option('from-env')) {
            $apiKey = getenv('CONTEXT7_API_KEY') ?: null;

            if (! $apiKey) {
                $this->components->error('CONTEXT7_API_KEY is not set.');

                return self::FAILURE;
            }
        }

        if (! $apiKey) {
            $apiKey = password(
                label: 'Context7 API key',
                required: true,
            );
        }

        $authService->save(new Credentials($apiKey));

        $this->components->info("Credentials saved to {$authService->getConfigPath()}");

        return self::SUCCESS;
    }
}
