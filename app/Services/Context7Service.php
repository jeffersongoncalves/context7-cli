<?php

namespace App\Services;

use App\Exceptions\Context7ApiException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use JeffersonGoncalves\LaravelZero\ApiClient\AbstractApiClient;
use JeffersonGoncalves\LaravelZero\ApiClient\ApiException;
use JeffersonGoncalves\LaravelZero\ApiClient\Auth;

class Context7Service extends AbstractApiClient
{
    public function __construct(AuthService $authService)
    {
        $credentials = $authService->load();

        parent::__construct(
            (string) config('context7.api_base_url'),
            $credentials ? Auth::bearer($credentials->apiKey) : null,
        );
    }

    /**
     * Locate Context7 libraries matching a name, ranked against a query.
     *
     * @return array<string, mixed>
     */
    public function searchLibraries(string $libraryName, string $query, bool $fast = false): array
    {
        return $this->get('libs/search', $this->filteredQuery([
            'libraryName' => $libraryName,
            'query' => $query,
            'fast' => $fast ? 'true' : 'false',
        ]));
    }

    /**
     * Fetch documentation for a resolved library ID.
     *
     * type=txt (the API default) returns a plain-text body, not JSON, so it
     * is fetched and returned as a raw string; type=json returns the usual
     * JSON-decoded array.
     */
    public function getContext(string $libraryId, string $query, string $type = 'txt', bool $fast = false): array|string
    {
        $params = $this->filteredQuery([
            'libraryId' => $libraryId,
            'query' => $query,
            'type' => $type,
            'fast' => $fast ? 'true' : 'false',
        ]);

        return $type === 'json' ? $this->get('context', $params) : $this->getText('context', $params);
    }

    /**
     * Cumulative and daily usage metrics for a library.
     *
     * @return array<string, mixed>
     */
    public function getLibraryMetrics(string $libraryId, ?int $days = null): array
    {
        return $this->get('libs/metrics', $this->filteredQuery([
            'libraryId' => $libraryId,
            'days' => $days,
        ]));
    }

    /**
     * Trigger a documentation refresh for an already-indexed library. Lives
     * under /api/v1 (not /api/v2 like every other endpoint), so the leading
     * slash is required: it makes Guzzle resolve the path against the host,
     * discarding the v2 base path instead of appending to it.
     *
     * @return array<string, mixed>
     */
    public function refreshLibrary(string $libraryName, ?string $branch = null, ?string $gitToken = null): array
    {
        return $this->post('/api/v1/refresh', $this->filteredQuery([
            'libraryName' => $libraryName,
            'branch' => $branch,
            'gitToken' => $gitToken,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public function addGithubRepo(string $docsRepoUrl, ?string $gitToken = null): array
    {
        return $this->post('add/repo/github', $this->filteredQuery([
            'docsRepoUrl' => $docsRepoUrl,
            'gitToken' => $gitToken,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public function addGitlabRepo(string $docsRepoUrl, ?string $gitToken = null): array
    {
        return $this->post('add/repo/gitlab', $this->filteredQuery([
            'docsRepoUrl' => $docsRepoUrl,
            'gitToken' => $gitToken,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public function addBitbucketRepo(string $docsRepoUrl, ?string $gitToken = null): array
    {
        return $this->post('add/repo/bitbucket', $this->filteredQuery([
            'docsRepoUrl' => $docsRepoUrl,
            'gitToken' => $gitToken,
        ]));
    }

    /**
     * Add a repository from any other (self-hosted) git host.
     *
     * @return array<string, mixed>
     */
    public function addGitRepo(
        string $docsRepoUrl,
        ?string $gitToken = null,
        ?bool $private = null,
        ?bool $skipVersionFiltering = null,
        ?bool $generateDocs = null,
    ): array {
        return $this->post('add/repo/git', $this->filteredQuery([
            'docsRepoUrl' => $docsRepoUrl,
            'gitToken' => $gitToken,
            'private' => $private,
            'skipVersionFiltering' => $skipVersionFiltering,
            'generateDocs' => $generateDocs,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public function addOpenApi(string $openApiUrl): array
    {
        return $this->post('add/openapi', ['openApiUrl' => $openApiUrl]);
    }

    /**
     * @return array<string, mixed>
     */
    public function addLlmsTxt(string $llmstxtUrl): array
    {
        return $this->post('add/llmstxt', ['llmstxtUrl' => $llmstxtUrl]);
    }

    /**
     * @return array<string, mixed>
     */
    public function addWebsite(string $websiteUrl, ?string $websiteBaseUrl = null): array
    {
        return $this->post('add/website', $this->filteredQuery([
            'websiteUrl' => $websiteUrl,
            'websiteBaseUrl' => $websiteBaseUrl,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public function getPolicies(): array
    {
        return $this->get('policies');
    }

    /**
     * Merge-update the current teamspace's policies.
     *
     * @param  array<string, mixed>  $policies  Partial payload, e.g. ['sourceTypes' => [...]] and/or ['libraryFilters' => [...]].
     * @return array<string, mixed>
     */
    public function updatePolicies(array $policies): array
    {
        return $this->request('PATCH', 'policies', ['json' => $policies]);
    }

    protected function newApiException(int $statusCode, array $body): ApiException
    {
        return Context7ApiException::fromResponse($statusCode, $body);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    protected function filteredQuery(array $query): array
    {
        return array_filter($query, fn (mixed $value): bool => $value !== null && $value !== '');
    }

    /**
     * GET a plain-text (non-JSON) endpoint.
     *
     * @param  array<string, mixed>  $query
     */
    protected function getText(string $path, array $query): string
    {
        try {
            $response = $this->client->request('GET', $path, [
                'query' => $query,
                'headers' => array_merge($this->defaultHeaders(), $this->authHeaders(), ['Accept' => 'text/plain']),
            ]);

            return $response->getBody()->getContents();
        } catch (BadResponseException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body = json_decode($e->getResponse()->getBody()->getContents(), true) ?? [];

            throw $this->newApiException($statusCode, $body);
        } catch (GuzzleException $e) {
            throw $this->newApiException(0, ['message' => "HTTP request failed: {$e->getMessage()}"]);
        }
    }
}
