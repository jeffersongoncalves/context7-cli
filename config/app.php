<?php

use App\Providers\AppServiceProvider;

return [

    'name' => 'Context7 CLI',

    'version' => app('git.version'),

    'env' => 'development',

    'providers' => [
        AppServiceProvider::class,
    ],

];
