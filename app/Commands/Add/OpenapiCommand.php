<?php

namespace App\Commands\Add;

use App\Exceptions\AuthenticationException;
use App\Services\AuthService;
use App\Services\Context7Service;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class OpenapiCommand extends Command
{
    use HandlesApiErrors;

    protected $signature = 'add:openapi {openapi-url : URL of the OpenAPI spec to index}';

    protected $description = 'Submit an OpenAPI spec to Context7 for indexing (requires an API key)';

    public function handle(Context7Service $context7, AuthService $authService): int
    {
        return $this->handleApiErrors(function () use ($context7, $authService) {
            if (! $authService->isAuthenticated()) {
                throw new AuthenticationException;
            }

            $response = $context7->addOpenApi($this->argument('openapi-url'));

            $this->components->info($response['message'] ?? 'OpenAPI spec submitted successfully.');

            if (! empty($response['libraryName'])) {
                $this->components->twoColumnDetail('Library ID', $response['libraryName']);
            }

            return self::SUCCESS;
        });
    }
}
