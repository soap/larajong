<?php

namespace App\Jongman\Contracts;

use Illuminate\Support\Carbon;

interface ReservationListingInterface extends ResourceReservationListingInterface
{
    /**
     * @param  Carbon  $date
     * @return ReservationListingInterface
     */
    public function onDate($date);

    /**
     * @param  int  $resourceId
     * @return ReservationListingInterface
     */
    public function forResource($resourceId);

    /**
     * @abstract
     *
     * @return array|ReservationListItem[]
     */
    public function onDateForResource(Carbon $date, int $resourceId);
}
