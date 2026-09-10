<?php

use App\Services\AuthService;
use App\Services\Context7Service;

it('requests a refresh when authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('refreshLibrary')->once()->with('/vercel/next.js', null, null)->andReturn([]);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('refresh /vercel/next.js')
        ->expectsOutputToContain('Refresh requested')
        ->assertExitCode(0);
});

it('passes branch and git-token options through', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('refreshLibrary')->once()->with('/vercel/next.js', 'canary', 'gh-token')->andReturn([]);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('refresh /vercel/next.js --branch=canary --git-token=gh-token')
        ->assertExitCode(0);
});

it('fails when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(false);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('refreshLibrary');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('refresh /vercel/next.js')
        ->expectsOutputToContain('No Context7 API key found')
        ->assertExitCode(1);
});
