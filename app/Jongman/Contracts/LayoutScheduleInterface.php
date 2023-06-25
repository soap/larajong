<?php

namespace App\Jongman\Contracts;

use Illuminate\Support\Carbon;

interface LayoutScheduleInterface extends LayoutDailyScheduleInterface, LayoutTimezoneInterface
{
    /**
	 * @param Carbon $layoutDate
	 * @param bool $hideBlockedPeriods
	 * @return SchedulePeriod[]|array of SchedulePeriod objects
	 */
	public function getLayout(Carbon $layoutDate, $hideBlockedPeriods = false);

	/**
	 * @abstract
	 * @param Date $date
	 * @return SchedulePeriod|null period which occurs at this datetime. Includes start time, excludes end time. null if no match is found
	 */
	public function getPeriod(Carbon $date);
}