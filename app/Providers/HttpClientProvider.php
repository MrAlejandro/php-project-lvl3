<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class HttpClientProvider extends ServiceProvider
{
    const MAX_REDIRECTS = 5;

    public function register()
    {
        $config = ['allow_redirects' => ['max' => self::MAX_REDIRECTS]];
        $this->app->bind('HttpClient', function () use ($config) {
            return new Client($config);
        });
    }
}
