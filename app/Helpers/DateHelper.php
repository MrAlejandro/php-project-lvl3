<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function format(string $date, string $format = 'Y-m-d H:i:s'): string
    {
        return Carbon::parse($date)->format($format);
    }
}
