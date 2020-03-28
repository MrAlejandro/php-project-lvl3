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
        static::$factory = new FactoryMuffin();
        static::$factory->loadFactories(__DIR__ . '/Factories');
    }

    /* public function tearDown(): void */
    /* { */
    /*     static::$fm->deleteSaved(); */
    /*     dump(123123); */
    /*     die; */
    /*     parent::tearDown(); */
    /* } */
    /* public static function tearDownAfterClass(): void */
    /* { */
    /*     static::$fm->deleteSaved(); */
    /* } */
}
