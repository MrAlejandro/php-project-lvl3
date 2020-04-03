<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class Domain extends Base
{
    public $id;
    public $name;
    public $createdAt;
    public $updatedAt;

    public static function initializeWith(Collection $domain): Domain
    {
        $model = new self();

        $model->id = $domain->get('id', null);
        $model->name = $domain->get('name', null);
        $model->createdAt = $domain->get('createdAt', Carbon::now());
        $model->updatedAt = $domain->get('updatedAt', Carbon::now());

        return $model;
    }
}
