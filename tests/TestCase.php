<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use League\FactoryMuffin\FactoryMuffin;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $factory;

    public static function setupBeforeClass(): void
    {
        self::initFactories();
    }

    private static function initFactories(): void
    {
        static::$factory = new FactoryMuffin();
        static::$factory->loadFactories(__DIR__ . '/factories');
    }
}
