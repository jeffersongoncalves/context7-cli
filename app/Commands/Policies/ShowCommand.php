<?php

namespace App\Commands\Policies;

use App\Exceptions\AuthenticationException;
use App\Services\AuthService;
use App\Services\Context7Service;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class ShowCommand extends Command
{
    use HandlesApiErrors;

    protected $signature = 'policies:show {--format=txt : Output format: txt or json}';

    protected $description = "Show the current teamspace's Context7 library policies (requires an API key)";

    public function handle(Context7Service $context7, AuthService $authService): int
    {
        return $this->handleApiErrors(function () use ($context7, $authService) {
            if (! $authService->isAuthenticated()) {
                throw new AuthenticationException;
            }

            $policies = $context7->getPolicies();

            if ($this->option('format') === 'json') {
                $this->line((string) json_encode($policies, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

                return self::SUCCESS;
            }

            $this->components->twoColumnDetail('Accessible Libraries', (string) ($policies['accessibleLibraryCount'] ?? '-'));

            foreach ((array) ($policies['sourceTypes'] ?? []) as $source => $settings) {
                $enabled = is_array($settings) && ($settings['enabled'] ?? false) ? 'enabled' : 'disabled';
                $this->components->twoColumnDetail((string) $source, $enabled);
            }

            $quality = (array) ($policies['libraryFilters']['quality'] ?? []);

            if (! empty($quality['repoFilters']['minStars'])) {
                $this->components->twoColumnDetail('Min Stars', (string) $quality['repoFilters']['minStars']);
            }

            return self::SUCCESS;
        });
    }
}
