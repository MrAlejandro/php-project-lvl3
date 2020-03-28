<?php

namespace App\Models;

use stdClass;

abstract class Base
{
    abstract public static function fromArray(array $data);

    public static function fromStdObject(stdClass $row)
    {
        $rawData = get_object_vars($row);
        $camelizedRawData = collect($rawData)
            ->keys()
            ->mapWithKeys(function ($attrName) use ($rawData) {
                return [self::camelize($attrName) => $rawData[$attrName]];
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

    public function delete()
    {
        $repository = $this->repositoryClassName();
        return $repository::delete($this);
    }

    private function repositoryClassName()
    {
        $modelClassName = collect(explode('\\', get_class($this)))->last();
        $repositoryClassName = '\App\Repositories\\' . $modelClassName . 'Repository';

        return $repositoryClassName;
    }
}
