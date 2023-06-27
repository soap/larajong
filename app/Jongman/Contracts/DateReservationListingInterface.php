<?php

namespace App\Jongman\Contracts;

interface IDateReservationListing extends ResourceReservationListingInterface
{
	/**
	 * @param int $resourceId
	 * @return ResourceReservationListingInterface
	 */
	public function forResource($resourceId);
}
