<?php

namespace App\Commands\Add;

use App\Exceptions\AuthenticationException;
use App\Services\AuthService;
use App\Services\Context7Service;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Sleep;
use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;
use LaravelZero\Framework\Commands\Command;
use Throwable;

class GithubCommand extends Command
{
    use HandlesApiErrors;

    protected $signature = 'add:github {repo-url?* : GitHub repository URL(s) or owner/repo, e.g. "https://github.com/vercel/next.js" or vercel/next.js}
        {--from= : File with one repository per line (blank lines and # comments are ignored)}
        {--delay=8 : Seconds to wait between submissions when sending several repositories}
        {--git-token= : Git access token, for private repositories}';

    protected $description = 'Submit one or more GitHub repositories to Context7 for indexing (requires an API key)';

    public function handle(Context7Service $context7, AuthService $authService): int
    {
        return $this->handleApiErrors(function () use ($context7, $authService) {
            if (! $authService->isAuthenticated()) {
                throw new AuthenticationException;
            }

            $repos = $this->repositories();

            if ($repos === []) {
                $this->components->error('Pass at least one repository URL or a --from file.');

                return self::FAILURE;
            }

            return count($repos) === 1 ? $this->submitOne($context7, $repos[0]) : $this->submitMany($context7, $repos);
        });
    }

    protected function submitOne(Context7Service $context7, string $repo): int
    {
        $response = $context7->addGithubRepo($repo, $this->option('git-token'));

        $this->components->info($response['message'] ?? 'Repository submitted successfully.');

        if (! empty($response['libraryName'])) {
            $this->components->twoColumnDetail('Library ID', $response['libraryName']);
        }

        return self::SUCCESS;
    }

    /**
     * Keeps going when one repository fails (already indexed, rate limit, typo) and reports them at the end.
     *
     * @param  list<string>  $repos
     */
    protected function submitMany(Context7Service $context7, array $repos): int
    {
        $total = count($repos);
        $failed = [];

        foreach ($repos as $index => $repo) {
            $label = sprintf('[%d/%d] %s', $index + 1, $total, $repo);

            try {
                $response = $context7->addGithubRepo($repo, $this->option('git-token'));
                $this->components->twoColumnDetail($label, $response['libraryName'] ?? '<fg=green>submitted</>');
            } catch (Throwable $e) {
                $failed[$repo] = $e->getMessage();
                $this->components->twoColumnDetail($label, '<fg=red>'.$e->getMessage().'</>');
            }

            if ($index < $total - 1) {
                Sleep::for(max(0, (int) $this->option('delay')))->seconds();
            }
        }

        $this->newLine();
        $this->components->info(sprintf('%d submitted, %d failed.', $total - count($failed), count($failed)));

        if ($failed !== []) {
            $this->components->bulletList(array_keys($failed));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Arguments plus the --from file, as full GitHub URLs, without duplicates.
     *
     * @return list<string>
     */
    protected function repositories(): array
    {
        $entries = (array) $this->argument('repo-url');

        if (filled($from = $this->option('from'))) {
            if (! File::exists($from)) {
                throw new \RuntimeException("File not found: {$from}");
            }

            foreach (preg_split('/\R/', File::get($from)) ?: [] as $line) {
                $line = trim(preg_replace('/#.*$/', '', $line) ?? '');
                if ($line !== '') {
                    $entries[] = $line;
                }
            }
        }

        return array_values(array_unique(array_map(fn (string $entry) => preg_match('#^[\w.-]+/[\w.-]+$#', $entry) === 1
            ? "https://github.com/{$entry}"
            : $entry, $entries)));
    }
}
