<?php

use App\DTOs\Credentials;

it('creates credentials from constructor', function () {
    $credentials = new Credentials('ctx7sk-test-key');

    expect($credentials->apiKey)->toBe('ctx7sk-test-key');
});

it('creates credentials from array', function () {
    $credentials = Credentials::fromArray(['api_key' => 'ctx7sk-test-key']);

    expect($credentials->apiKey)->toBe('ctx7sk-test-key');
});

it('converts to array', function () {
    $credentials = new Credentials('ctx7sk-test-key');

    expect($credentials->toArray())->toBe(['api_key' => 'ctx7sk-test-key']);
});

it('is invalid without an api key', function () {
    $credentials = Credentials::fromArray([]);

    expect($credentials->isValid())->toBeFalse();
});

it('is valid with an api key', function () {
    $credentials = new Credentials('ctx7sk-test-key');

    expect($credentials->isValid())->toBeTrue();
});
