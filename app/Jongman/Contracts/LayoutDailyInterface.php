<?php

namespace App\Jongman\Contracts;

use Illuminate\Support\Carbon;

interface LayoutDailyInterface {
    
    /**
	 * @param Carbon $date
	 * @param int $resourceId
	 * @return array|IReservationSlot[]
	 */
	function getLayout(Carbon $date, int $resourceId);

	/**
	 * @param Carbon $date
	 * @return bool
	 */
	function isDateReservable(Carbon $date);

	/**
	 * @param Carbon $displayDate
	 * @return string[]
	 */
	function getLabels(Carbon $displayDate);

	/**
	 * @param Carbon $displayDate
	 * @return mixed
	 */
	function getPeriods(Carbon $displayDate);
}