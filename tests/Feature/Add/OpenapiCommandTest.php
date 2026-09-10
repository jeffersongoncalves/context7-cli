<?php

use App\Services\AuthService;
use App\Services\Context7Service;

it('submits an openapi spec when authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('addOpenApi')
        ->once()
        ->with('https://api.example.com/openapi.json')
        ->andReturn(['message' => 'OpenAPI spec submitted successfully']);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:openapi https://api.example.com/openapi.json')
        ->expectsOutputToContain('OpenAPI spec submitted successfully')
        ->assertExitCode(0);
});

it('fails when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(false);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('addOpenApi');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:openapi https://api.example.com/openapi.json')
        ->expectsOutputToContain('No Context7 API key found')
        ->assertExitCode(1);
});
