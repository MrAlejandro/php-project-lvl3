<?php

namespace App\Models;

use stdClass;
use Illuminate\Support\Collection;

abstract class Base
{
    abstract public static function initializeWith(Collection $data);

    public static function fromStdObject(stdClass $rawData)
    {
        $modelData = collect(get_object_vars($rawData));
        $camelizedRawData = $modelData
            ->keys()
            ->mapWithKeys(function ($attrName) use ($modelData) {
                return [self::camelize($attrName) => $modelData->get($attrName)];
            });

        $model = static::initializeWith($camelizedRawData);

        return $model;
    }

    public static function camelize($attribute, $separator = '_')
    {
        return lcfirst(str_replace($separator, '', ucwords($attribute, $separator)));
    }

    public function save()
    {
        $repository = $this->repositoryClassName();
        $model = $repository::store($this);

        return $model;
    }

    private function repositoryClassName()
    {
        $modelClassName = collect(explode('\\', get_class($this)))->last();
        $repositoryClassName = '\App\Repositories\\' . $modelClassName . 'Repository';

        return $repositoryClassName;
    }
}
