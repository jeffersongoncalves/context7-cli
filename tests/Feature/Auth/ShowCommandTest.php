<?php

use App\DTOs\Credentials;
use App\Services\AuthService;

it('shows the masked api key', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('load')->andReturn(new Credentials('ctx7sk-abcd1234'));
    $authService->shouldReceive('getConfigPath')->andReturn('/home/user/.context7-cli/config.json');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:show')
        ->expectsOutputToContain('1234')
        ->assertExitCode(0);
});

it('warns when no api key is saved', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('load')->andReturn(null);
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:show')
        ->expectsOutputToContain('No Context7 API key saved')
        ->assertExitCode(0);
});
