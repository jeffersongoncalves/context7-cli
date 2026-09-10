<?php

use App\Services\AuthService;
use App\Services\Context7Service;

it('submits a website when authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('addWebsite')
        ->once()
        ->with('https://example.com/docs', 'https://example.com/docs')
        ->andReturn(['message' => 'Website submitted successfully']);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:website https://example.com/docs --base-url=https://example.com/docs')
        ->expectsOutputToContain('Website submitted successfully')
        ->assertExitCode(0);
});

it('fails when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(false);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('addWebsite');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:website https://example.com/docs')
        ->expectsOutputToContain('No Context7 API key found')
        ->assertExitCode(1);
});
