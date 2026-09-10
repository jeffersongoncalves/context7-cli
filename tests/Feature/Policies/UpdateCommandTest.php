<?php

use App\Services\AuthService;
use App\Services\Context7Service;

it('updates policies from an inline json payload', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('updatePolicies')
        ->once()
        ->with(['sourceTypes' => ['websites' => ['enabled' => false]]])
        ->andReturn(['sourceTypes' => ['websites' => ['enabled' => false]]]);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('policies:update \'{"sourceTypes":{"websites":{"enabled":false}}}\'')
        ->expectsOutputToContain('"enabled": false')
        ->assertExitCode(0);
});

it('fails on invalid json', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('updatePolicies');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('policies:update "not json"')
        ->expectsOutputToContain('Invalid JSON payload')
        ->assertExitCode(1);
});

it('fails when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(false);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('updatePolicies');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('policies:update \'{}\'')
        ->expectsOutputToContain('No Context7 API key found')
        ->assertExitCode(1);
});
