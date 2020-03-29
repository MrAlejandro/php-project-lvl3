<?php

namespace App;

class OperationResult
{
    protected $result;
    protected $isSuccessful;

    public function succeed()
    {
        $this->isSuccessful = true;
    }

    public function fail()
    {
        $this->isSuccessful = false;
    }

    public function isSuccessful()
    {
        return $this->isSuccessful;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }
}
