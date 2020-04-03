<?php

namespace App;

class OperationResult
{
    protected $result;
    protected $isSuccessful;

    public function succeed(): void
    {
        $this->isSuccessful = true;
    }

    public function fail(): void
    {
        $this->isSuccessful = false;
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    public function setResult($result): void
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }
}
