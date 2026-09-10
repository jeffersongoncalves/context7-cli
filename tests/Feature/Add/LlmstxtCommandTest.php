<?php

use App\Services\AuthService;
use App\Services\Context7Service;

it('submits an llms.txt file when authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('addLlmsTxt')
        ->once()
        ->with('https://example.com/llms.txt')
        ->andReturn(['message' => 'llms.txt submitted successfully']);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:llmstxt https://example.com/llms.txt')
        ->expectsOutputToContain('llms.txt submitted successfully')
        ->assertExitCode(0);
});

it('fails when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(false);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('addLlmsTxt');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:llmstxt https://example.com/llms.txt')
        ->expectsOutputToContain('No Context7 API key found')
        ->assertExitCode(1);
});
