<?php

namespace App\Jongman\Contracts;

use Illuminate\Support\Carbon;

interface LayoutDailyInterface
{
    /**
     * @return array|ReservationSlotInterface[]
     */
    public function getLayout(Carbon $date, int $resourceId);

    /**
     * @return bool
     */
    public function isDateReservable(Carbon $date);

    /**
     * @return string[]
     */
    public function getLabels(Carbon $displayDate);

    /**
     * @return mixed
     */
    public function getPeriods(Carbon $displayDate);
}
