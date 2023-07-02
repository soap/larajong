<?php

namespace App\Jongman\Contracts;

interface DateReservationListingInterface extends ResourceReservationListingInterface
{
    /**
     * @param  int  $resourceId
     * @return ResourceReservationListingInterface
     */
    public function forResource($resourceId);
}
