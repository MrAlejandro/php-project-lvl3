<?php

namespace Tests\Support;

use Tests\Support\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;

trait HttpRequestMockSupport
{
    public function mockHttpRequest()
    {
        $handler = new MockHandler();
        $stack = HandlerStack::create($handler);
        $config = ['handler' => $stack];

        $this->app->bind('HttpClient', function () use ($config) {
            return new Client($config);
        });
    }
}
