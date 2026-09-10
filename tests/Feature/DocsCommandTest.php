<?php

use App\Services\Context7Service;

it('prints the plain-text response by default', function () {
    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('getContext')->with('/vercel/next.js', 'useState', 'txt', false)
        ->andReturn("========================\nuseState returns a stateful value.\n");
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('docs /vercel/next.js useState')
        ->expectsOutputToContain('useState returns a stateful value')
        ->assertExitCode(0);
});

it('prints the decoded json when --type=json', function () {
    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('getContext')->with('/vercel/next.js', 'useState', 'json', false)
        ->andReturn(['snippets' => []]);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('docs /vercel/next.js useState --type=json')
        ->expectsOutputToContain('"snippets"')
        ->assertExitCode(0);
});

it('reports when no documentation is found', function () {
    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('getContext')->andReturn('');
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('docs /vercel/next.js useState')
        ->expectsOutputToContain('No documentation found')
        ->assertExitCode(0);
});
