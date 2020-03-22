<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tests\Support\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use App;

class HttpClientProvider extends ServiceProvider
{
    const MAX_REDIRECTS = 5;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (App::environment() === 'testing') {
            $handler = new MockHandler();
            $stack = HandlerStack::create($handler);
            $config = ['handler' => $stack];
        } else {
            $config = ['allow_redirects' => ['max' => self::MAX_REDIRECTS]];
        }

        $this->app->bind('HttpClient', function () use ($config) {
            return new Client($config);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
