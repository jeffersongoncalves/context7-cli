<?php

namespace App\Commands;

use App\Services\Context7Service;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;

class MetricsCommand extends Command
{
    use HandlesApiErrors;

    protected $signature = 'metrics {library-id : Context7 library ID, e.g. "/vercel/next.js"}
        {--days=30 : Number of days of daily history to return (1-365)}
        {--format=json : Output format: txt or json}';

    protected $description = 'Show usage metrics for a library on Context7 (requires an API key)';

    public function handle(Context7Service $context7): int
    {
        return $this->handleApiErrors(function () use ($context7) {
            $metrics = $context7->getLibraryMetrics(
                $this->argument('library-id'),
                (int) $this->option('days'),
            );

            if ($this->option('format') === 'txt') {
                foreach ($metrics as $key => $value) {
                    if (is_scalar($value)) {
                        $this->components->twoColumnDetail((string) $key, (string) $value);
                    }
                }

                return self::SUCCESS;
            }

            $this->line((string) json_encode($metrics, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        });
    }
}
