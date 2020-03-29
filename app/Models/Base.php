<?php

namespace App\Models;

use stdClass;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

abstract class Base
{
    abstract public static function initializeWith(Collection $data);

    public static function fromStdObject(stdClass $rawData)
    {
        $modelData = collect(get_object_vars($rawData));
        $camelizedModelData = $modelData
            ->keys()
            ->mapWithKeys(function ($attrName) use ($modelData) {
                return [Str::camel($attrName) => $modelData->get($attrName)];
            });

        $model = static::initializeWith($camelizedModelData);

        return $model;
    }

    public function save()
    {
        $repository = $this->repositoryClassName();
        $model = $repository::store($this);

        return $model;
    }

    private function repositoryClassName(): string
    {
        $modelClassName = collect(explode('\\', get_class($this)))->last();
        $repositoryClassName = '\App\Repositories\\' . $modelClassName . 'Repository';

        return $repositoryClassName;
    }
}
