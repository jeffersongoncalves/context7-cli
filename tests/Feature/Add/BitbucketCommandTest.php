<?php

use App\Services\AuthService;
use App\Services\Context7Service;

it('submits a bitbucket repository when authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('addBitbucketRepo')
        ->once()
        ->with('https://bitbucket.org/acme/repo', null)
        ->andReturn(['message' => 'Repository submitted successfully']);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:bitbucket https://bitbucket.org/acme/repo')
        ->expectsOutputToContain('Repository submitted successfully')
        ->assertExitCode(0);
});

it('fails when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(false);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('addBitbucketRepo');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:bitbucket https://bitbucket.org/acme/repo')
        ->expectsOutputToContain('No Context7 API key found')
        ->assertExitCode(1);
});
