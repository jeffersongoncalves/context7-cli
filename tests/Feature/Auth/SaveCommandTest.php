<?php

use App\DTOs\Credentials;
use App\Services\AuthService;

it('saves credentials from the api-key argument', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('save')->once()->with(Mockery::on(
        fn (Credentials $credentials): bool => $credentials->apiKey === 'ctx7sk-test-key'
    ));
    $authService->shouldReceive('getConfigPath')->andReturn('/home/user/.context7-cli/config.json');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:save ctx7sk-test-key')
        ->expectsOutputToContain('Credentials saved')
        ->assertExitCode(0);
});

it('saves credentials from the CONTEXT7_API_KEY env var when no argument is given', function () {
    putenv('CONTEXT7_API_KEY=ctx7sk-env-key');

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('save')->once()->with(Mockery::on(
        fn (Credentials $credentials): bool => $credentials->apiKey === 'ctx7sk-env-key'
    ));
    $authService->shouldReceive('getConfigPath')->andReturn('/home/user/.context7-cli/config.json');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:save')->assertExitCode(0);

    putenv('CONTEXT7_API_KEY');
});

it('fails when no api key is available', function () {
    putenv('CONTEXT7_API_KEY');

    $this->artisan('auth:save')
        ->expectsOutputToContain('Provide an API key')
        ->assertExitCode(1);
});
