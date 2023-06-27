<?php

namespace App\Jongman\Contracts;

use Illuminate\Support\Carbon;

interface ReservedItemInterface
{
	/**
	 * @abstract
	 * @return Carbon
	 */
	public function getStartDate();

	/**
	 * @abstract
	 * @return Carbon
	 */
	public function getEndDate();

	/**
	 * @abstract
	 * @return int
	 */
	public function getResourceId();

	/**
	 * @abstract
	 * @return int
	 */
	public function getId();

	/**
	 * @abstract
	 * @param Carbon $date
	 * @return bool
	 */
	public function occursOn(Carbon $date);
}
