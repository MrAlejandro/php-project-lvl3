<?php

namespace App\Models;

use Carbon\Carbon;

class Domain extends Base
{
    public $id;
    public $name;
    public $createdAt;
    public $updatedAt;

    public static function fromArray(array $domain)
    {
        $model = new self();

        $model->id = $domain['id'] ?? null;
        $model->name = $domain['name'] ?? null;
        $model->createdAt = $domain['createdAt'] ?? Carbon::now();
        $model->updatedAt = $domain['updatedAt'] ?? Carbon::now();

        return $model;
    }
}
