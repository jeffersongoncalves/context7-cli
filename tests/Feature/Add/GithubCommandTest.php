<?php

use App\Services\AuthService;
use App\Services\Context7Service;

it('submits a github repository when authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('addGithubRepo')
        ->once()
        ->with('https://github.com/vercel/next.js', null)
        ->andReturn(['libraryName' => '/vercel/next.js', 'message' => 'Repository submitted successfully']);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:github https://github.com/vercel/next.js')
        ->expectsOutputToContain('Repository submitted successfully')
        ->expectsOutputToContain('/vercel/next.js')
        ->assertExitCode(0);
});

it('fails when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(false);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('addGithubRepo');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:github https://github.com/vercel/next.js')
        ->expectsOutputToContain('No Context7 API key found')
        ->assertExitCode(1);
});
