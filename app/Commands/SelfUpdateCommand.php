<?php

namespace App\Commands;

use JeffersonGoncalves\LaravelZero\SelfUpdate\PharUpdater;
use JeffersonGoncalves\LaravelZero\SelfUpdate\SelfUpdateCommand as BaseSelfUpdateCommand;

class SelfUpdateCommand extends BaseSelfUpdateCommand
{
    protected $description = 'Update the context7 CLI to the latest version';

    protected function githubRepo(): string
    {
        return 'jeffersongoncalves/context7-cli';
    }

    protected function assetName(): string
    {
        return 'context7.phar';
    }

    protected function tempPrefix(): string
    {
        return 'context7_';
    }

    protected function currentVersion(): string
    {
        return (string) config('app.version', 'unreleased');
    }

    protected function makeUpdater(): PharUpdater
    {
        return $this->getLaravel()->make(PharUpdater::class);
    }
}
