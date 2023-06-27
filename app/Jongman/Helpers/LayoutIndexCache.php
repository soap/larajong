<?php

namespace App\Jongman\Helpers;

use App\Jongman\Helpers\CachedLayoutIndex;
use Illuminate\Support\Carbon;

class LayoutIndexCache
{
	/**
	 * @var CachedLayoutIndex[]
	 */
	private static $_cache = array();

	/**
	 * @param Carbon $date
	 * @return bool
	 */
	public static function contains(Carbon $date)
	{
		return array_key_exists($date->timestamp(), self::$_cache);
	}

	/**
	 * @param Carbon $date
	 * @param SchedulePeriod[] $schedulePeriods
	 * @param Carbon $startDate
	 * @param Carbon $endDate
	 */
	public static function add(Carbon $date, $schedulePeriods, Carbon $startDate, Carbon $endDate)
	{
		self::$_cache[$date->timestamp] = new CachedLayoutIndex($schedulePeriods, $startDate, $endDate);
	}

	public static function get(Carbon $date)
	{
		return self::$_cache[$date->timestamp];
	}

	public static function clear() { self::$_cache = array(); }
}    