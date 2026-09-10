<?php

use App\DTOs\Credentials;
use App\Services\AuthService;
use Laravel\Prompts\Prompt;

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

it('saves credentials from the CONTEXT7_API_KEY env var when --from-env is passed', function () {
    putenv('CONTEXT7_API_KEY=ctx7sk-env-key');

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('save')->once()->with(Mockery::on(
        fn (Credentials $credentials): bool => $credentials->apiKey === 'ctx7sk-env-key'
    ));
    $authService->shouldReceive('getConfigPath')->andReturn('/home/user/.context7-cli/config.json');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:save --from-env')->assertExitCode(0);

    putenv('CONTEXT7_API_KEY');
});

it('fails when --from-env is passed but CONTEXT7_API_KEY is not set', function () {
    putenv('CONTEXT7_API_KEY');

    $this->artisan('auth:save --from-env')
        ->expectsOutputToContain('CONTEXT7_API_KEY is not set')
        ->assertExitCode(1);
});

it('prompts for the key when no argument or --from-env is given', function () {
    Prompt::fallbackWhen(true);

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('save')->once()->with(Mockery::on(
        fn (Credentials $credentials): bool => $credentials->apiKey === 'ctx7sk-prompted-key'
    ));
    $authService->shouldReceive('getConfigPath')->andReturn('/home/user/.context7-cli/config.json');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:save')
        ->expectsQuestion('Context7 API key', 'ctx7sk-prompted-key')
        ->expectsOutputToContain('Credentials saved')
        ->assertExitCode(0);
});
