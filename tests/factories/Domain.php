<?php

use League\FactoryMuffin\Faker\Facade as Faker;
use App\Models\Domain;
use Carbon\Carbon;

$fm->define(Domain::class)->setDefinitions([
    'name' => Faker::domainName(),
    'createdAt' => Carbon::now(),
    'updatedAt' => Carbon::now(),
]);
