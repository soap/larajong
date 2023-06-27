<?php

namespace App\Jongman\Layouts;

use App\Jongman\Contracts\LayoutScheduleInterface;

class LayoutReservation extends LayoutSchedule implements LayoutScheduleInterface
{
    /**
     * @param  Carbon  $start
     * @param  Carbon  $end
     * @return bool
     */
	protected function spansMidnight(Carbon $start, Carbon $end)
	{
		return false;
	}
}
