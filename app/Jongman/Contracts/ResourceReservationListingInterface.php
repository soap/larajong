<?php

namespace App\Jongman\Contracts;

interface ResourceReservationListingInterface
{
    /**
     * @return int
     */
    public function count();

    /**
     * @return array|ReservationListItem[]
     */
    public function reservations();
}
