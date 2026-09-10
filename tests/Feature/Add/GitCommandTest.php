<?php

use App\Services\AuthService;
use App\Services\Context7Service;

it('submits a generic git repository with flags when authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('addGitRepo')
        ->once()
        ->with('https://git.example.com/acme/repo', 'token', true, true, true)
        ->andReturn(['message' => 'Repository submitted successfully']);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:git https://git.example.com/acme/repo --git-token=token --private --skip-version-filtering --generate-docs')
        ->expectsOutputToContain('Repository submitted successfully')
        ->assertExitCode(0);
});

it('fails when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(false);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('addGitRepo');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:git https://git.example.com/acme/repo')
        ->expectsOutputToContain('No Context7 API key found')
        ->assertExitCode(1);
});
