<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class DomainCheck extends Base
{
    public $id;
    public $domainId;
    public $statusCode;
    public $keywords;
    public $description;
    public $h1;
    public $createdAt;
    public $updatedAt;

    public static function initializeWith(Collection $domainCheck)
    {
        $model = new self();

        $model->id = $domainCheck->get('id', null);
        $model->h1 = $domainCheck->get('h1', null);
        $model->domainId = $domainCheck->get('domainId', null);
        $model->statusCode = $domainCheck->get('statusCode', null);
        $model->description = $domainCheck->get('description', null);
        $model->keywords = $domainCheck->get('keywords', null);
        $model->createdAt = $domainCheck->get('createdAt', Carbon::now());
        $model->updatedAt = $domainCheck->get('updatedAt', Carbon::now());

        return $model;
    }
}
