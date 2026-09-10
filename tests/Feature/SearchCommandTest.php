<?php

use App\Services\Context7Service;

it('lists matching libraries', function () {
    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('searchLibraries')->with('next.js', 'app router', false)->andReturn([
        'results' => [
            ['id' => '/vercel/next.js', 'title' => 'Next.js', 'trustScore' => 9.8, 'totalSnippets' => 1234, 'stars' => 120000],
        ],
    ]);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('search next.js "app router"')
        ->expectsOutputToContain('/vercel/next.js')
        ->assertExitCode(0);
});

it('passes the --fast flag through', function () {
    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('searchLibraries')->with('react', 'state management', true)->andReturn(['results' => []]);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('search react "state management" --fast')
        ->expectsOutputToContain('No results found')
        ->assertExitCode(0);
});
