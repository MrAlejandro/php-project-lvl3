<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MacroServiceProvider extends ServiceProvider
{
    public function register()
    {
        Builder::macro('findOrFail', function () {
            if ($record = $this->first()) {
                return $record;
            }

            throw new ModelNotFoundException('No records found');
        });
    }
}
