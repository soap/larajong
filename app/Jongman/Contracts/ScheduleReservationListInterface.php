<?php

namespace App\Jongman\Contracts;

interface ScheduleReservationListInterface
{
	/**
	 * @return array[int] ReservationSlotInterface
	 */
	function buildSlots();
}
