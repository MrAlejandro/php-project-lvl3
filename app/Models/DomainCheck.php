<?php

namespace App\Models;

use Carbon\Carbon;

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

    public static function fromArray(array $domainCheck)
    {
        $model = new self();

        $model->id = $domainCheck['id'] ?? null;
        $model->domainId = $domainCheck['domainId'] ?? null;
        $model->statusCode = $domainCheck['statusCode'] ?? null;
        $model->keywords = $domainCheck['keywords'] ?? null;
        $model->description = $domainCheck['description'] ?? null;
        $model->h1 = $domainCheck['h1'] ?? null;
        $model->createdAt = $domainCheck['createdAt'] ?? Carbon::now();
        $model->updatedAt = $domainCheck['updatedAt'] ?? Carbon::now();

        return $model;
    }
}
