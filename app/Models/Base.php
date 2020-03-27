<?php

namespace App\Models;

use stdClass;

class Base
{
    public static function fromStdObject(stdClass $row)
    {
        $rawData = get_object_vars($row);
        $camelizedRawData = collect($rawData)
            ->keys()
            ->mapWithKeys(function ($attrName) use ($rawData) {
                return [self::camelize($attrName) => $rawData[$attrName]];
            });

        $model = new static();
        $camelizedRawData->each(function ($attrValue, $attrName) use ($model) {
            $model->$attrName = $attrValue;
        });

        return $model;
    }

    public static function camelize($attribute, $separator = '_')
    {
        return lcfirst(str_replace($separator, '', ucwords($attribute, $separator)));
    }
}
