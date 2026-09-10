<?php

use App\Services\AuthService;
use App\Services\Context7Service;

it('shows teamspace policies when authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('getPolicies')->andReturn([
        'accessibleLibraryCount' => 42,
        'sourceTypes' => [
            'public_repos' => ['enabled' => true],
            'websites' => ['enabled' => false],
        ],
        'libraryFilters' => [
            'quality' => [
                'repoFilters' => ['minStars' => 100],
            ],
        ],
    ]);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('policies:show')
        ->expectsOutputToContain('42')
        ->expectsOutputToContain('enabled')
        ->expectsOutputToContain('100')
        ->assertExitCode(0);
});

it('fails when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(false);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('getPolicies');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('policies:show')
        ->expectsOutputToContain('No Context7 API key found')
        ->assertExitCode(1);
});
