<?php

use App\Services\AuthService;
use App\Services\Context7Service;

it('submits a gitlab repository when authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('addGitlabRepo')
        ->once()
        ->with('https://gitlab.com/gitlab-org/gitlab', null)
        ->andReturn(['message' => 'Repository submitted successfully']);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:gitlab https://gitlab.com/gitlab-org/gitlab')
        ->expectsOutputToContain('Repository submitted successfully')
        ->assertExitCode(0);
});

it('fails when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(false);
    $this->app->instance(AuthService::class, $authService);

    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldNotReceive('addGitlabRepo');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:gitlab https://gitlab.com/gitlab-org/gitlab')
        ->expectsOutputToContain('No Context7 API key found')
        ->assertExitCode(1);
});
