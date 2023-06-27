<?php

namespace App\Jongman\Helpers;

use Illuminate\Support\Carbon;

class LayoutIndexCache
{
    /**
     * @var CachedLayoutIndex[]
     */
    private static $_cache = [];

    /**
     * @return bool
     */
    public static function contains(Carbon $date)
    {
        return array_key_exists($date->timestamp(), self::$_cache);
    }

    /**
     * @param  SchedulePeriod[]  $schedulePeriods
     */
    public static function add(Carbon $date, $schedulePeriods, Carbon $startDate, Carbon $endDate)
    {
        self::$_cache[$date->timestamp] = new CachedLayoutIndex($schedulePeriods, $startDate, $endDate);
    }

    public static function get(Carbon $date)
    {
        return self::$_cache[$date->timestamp];
    }

    public static function clear()
    {
        self::$_cache = [];
    }
}
