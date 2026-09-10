<?php

use App\Services\AuthService;

it('removes the saved api key', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('forget')->once();
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:forget')
        ->expectsOutputToContain('removed')
        ->assertExitCode(0);
});
