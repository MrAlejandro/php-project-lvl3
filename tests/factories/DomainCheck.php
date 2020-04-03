<?php

use League\FactoryMuffin\Faker\Facade as Faker;
use App\Models\DomainCheck;
use Carbon\Carbon;

$fm->define(DomainCheck::class)->setDefinitions([
    'domain' => null,
    'description' => Faker::text(100),
    'keywords' => Faker::text(100),
    'h1' => Faker::text(100),
    'createdAt' => Carbon::now(),
    'updatedAt' => Carbon::now(),
]);
