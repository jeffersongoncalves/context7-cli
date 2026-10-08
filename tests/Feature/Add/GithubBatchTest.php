<?php

use App\Services\AuthService;
use App\Services\Context7Service;
use Illuminate\Support\Sleep;

beforeEach(function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('isAuthenticated')->andReturn(true);
    $this->app->instance(AuthService::class, $authService);
    Sleep::fake();
});

it('submits a single repository like before', function () {
    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('addGithubRepo')->once()->with('https://github.com/vercel/next.js', null)
        ->andReturn(['message' => 'Queued', 'libraryName' => '/vercel/next.js']);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:github https://github.com/vercel/next.js')
        ->expectsOutputToContain('Queued')
        ->assertExitCode(0);

    Sleep::assertNeverSlept();
});

it('expands owner/repo shorthand', function () {
    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('addGithubRepo')->once()->with('https://github.com/vercel/next.js', null)->andReturn([]);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:github vercel/next.js')->assertExitCode(0);
});

it('submits several repositories from arguments and a file, waiting between them', function () {
    $file = tempnam(sys_get_temp_dir(), 'ctx7');
    file_put_contents($file, "# kits\nacme/one\n\nacme/two  # second\nhttps://github.com/acme/three\nacme/one\n");

    $context7 = Mockery::mock(Context7Service::class);
    foreach (['zero', 'one', 'two', 'three'] as $name) {
        $context7->shouldReceive('addGithubRepo')->once()->with("https://github.com/acme/{$name}", null)->andReturn([]);
    }
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:github', ['repo-url' => ['acme/zero'], '--from' => $file, '--delay' => 3])
        ->expectsOutputToContain('[4/4] https://github.com/acme/three')
        ->expectsOutputToContain('4 submitted, 0 failed.')
        ->assertExitCode(0);

    Sleep::assertSleptTimes(3);
    unlink($file);
});

it('keeps going when one repository fails and exits with failure', function () {
    $context7 = Mockery::mock(Context7Service::class);
    $context7->shouldReceive('addGithubRepo')->with('https://github.com/acme/one', null)->andThrow(new RuntimeException('Library already exists'));
    $context7->shouldReceive('addGithubRepo')->with('https://github.com/acme/two', null)->andReturn(['libraryName' => '/acme/two']);
    $this->app->instance(Context7Service::class, $context7);

    $this->artisan('add:github acme/one acme/two --delay=0')
        ->expectsOutputToContain('Library already exists')
        ->expectsOutputToContain('1 submitted, 1 failed.')
        ->assertExitCode(1);
});

it('fails without repositories or with a missing file', function () {
    $this->app->instance(Context7Service::class, Mockery::mock(Context7Service::class));

    $this->artisan('add:github')->expectsOutputToContain('Pass at least one repository')->assertExitCode(1);
    $this->artisan('add:github --from=missing.txt')->expectsOutputToContain('File not found')->assertExitCode(1);
});
