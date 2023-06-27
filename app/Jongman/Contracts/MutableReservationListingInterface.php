<?php

namespace App\Jongman\Contracts;

interface MutableReservationListingInterface extends ReservationListingInterface
{
	/**
	 * @abstract
	 * @param ReservationItemView $reservation
	 * @return void
	 */
	public function add($reservation);

	/**
	 * @abstract
	 * @param BlackoutItemView $blackout
	 * @return void
	 */
	public function addBlackout($blackout);
}