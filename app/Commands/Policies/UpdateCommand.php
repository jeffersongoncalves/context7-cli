<?php

namespace App\Commands\Policies;

use App\Exceptions\AuthenticationException;
use App\Services\AuthService;
use App\Services\Context7Service;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class UpdateCommand extends Command
{
    use HandlesApiErrors;

    /**
     * The payload is raw JSON (inline or "@path/to/file.json") rather than
     * flags, since Context7's policy schema (sourceTypes/libraryFilters) is
     * a deeply nested partial-update object not worth flattening into flags.
     *
     * @var string
     */
    protected $signature = 'policies:update {json : Partial policy payload as inline JSON, or "@path/to/file.json"}';

    protected $description = "Update the current teamspace's Context7 library policies (requires an API key)";

    public function handle(Context7Service $context7, AuthService $authService): int
    {
        return $this->handleApiErrors(function () use ($context7, $authService) {
            if (! $authService->isAuthenticated()) {
                throw new AuthenticationException;
            }

            $input = $this->argument('json');
            $raw = str_starts_with($input, '@') ? file_get_contents(substr($input, 1)) : $input;

            if ($raw === false) {
                $this->components->error("Could not read file: {$input}");

                return self::FAILURE;
            }

            $payload = json_decode($raw, true);

            if (! is_array($payload)) {
                $this->components->error('Invalid JSON payload.');

                return self::FAILURE;
            }

            $policies = $context7->updatePolicies($payload);

            $this->line((string) json_encode($policies, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        });
    }
}
