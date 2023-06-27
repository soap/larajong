<?php

namespace App\Jongman\Contracts;

interface ScheduleReservationListInterface
{
    /**
     * @return array[int] ReservationSlotInterface
     */
    public function buildSlots();
}
