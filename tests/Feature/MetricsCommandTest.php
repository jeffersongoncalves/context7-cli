<?php

use App\Services\Context7Service;

it('prints library metrics as json by default', function () {
    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('getLibraryMetrics')->with('/vercel/next.js', 30)->andReturn(['totalRequests' => 42]);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('metrics /vercel/next.js')
        ->expectsOutputToContain('"totalRequests": 42')
        ->assertExitCode(0);
});

it('prints library metrics as a table when --format=txt', function () {
    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('getLibraryMetrics')->with('/vercel/next.js', 7)->andReturn(['totalRequests' => 42]);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('metrics /vercel/next.js --days=7 --format=txt')
        ->expectsOutputToContain('42')
        ->assertExitCode(0);
});
