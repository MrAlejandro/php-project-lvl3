<?php

namespace App\Models;

use stdClass;

abstract class Base
{
    abstract public static function fromArray(array $data);

    public static function fromStdObject(stdClass $rawData)
    {
        $modelData = collect(get_object_vars($rawData));
        $camelizedRawData = $modelData
            ->keys()
            ->mapWithKeys(function ($attrName) use ($modelData) {
                return [self::camelize($attrName) => $modelData->get($attrName)];
            })
            ->toArray();

        $model = static::fromArray($camelizedRawData);

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
