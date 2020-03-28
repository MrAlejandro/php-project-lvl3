<?php

namespace App\Models;

use Carbon\Carbon;

class Domain extends Base
{
    public $id;
    public $name;
    public $createdAt;
    public $updatedAt;

    public function __construct(array $domain)
    {
        $this->id = $domain['id'] ?? null;
        $this->name = $domain['name'] ?? null;
        $this->createdAt = $domain['createdAt'] ?? Carbon::now();
        $this->updatedAt = $domain['updatedAt'] ?? Carbon::now();
    }
}
