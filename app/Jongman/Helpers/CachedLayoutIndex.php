<?php

namespace App\Jongman\Helpers;

use Illuminate\Support\Carbon;

class CachedLayoutIndex
{
	private $_firstLayoutTime;
	private $_layoutByStartTime = array();
	private $_layoutIndexByEndTime = array();

	/**
	 * @param SchedulePeriod[] $schedulePeriods
	 * @param Carbon $startDate
	 * @param Carbon $endDate
	 */
	public function __construct($schedulePeriods, Carbon $startDate, Carbon $endDate)
	{
		$this->_firstLayoutTime = $endDate;

		for ($i = 0; $i < count($schedulePeriods); $i++)
		{
			/** @var Carbon $itemBegin */
			$itemBegin = $schedulePeriods[$i]->BeginDate();
			if ($itemBegin->LessThan($this->_firstLayoutTime))
			{
				$this->_firstLayoutTime = $schedulePeriods[$i]->BeginDate();
			}

			/** @var Carbon $endTime */
			$endTime = $schedulePeriods[$i]->endDate();
			if (!$schedulePeriods[$i]->endDate()->dateEquals($startDate))
			{
				$endTime = $endDate;
			}

			$this->_layoutByStartTime[$itemBegin->timestamp] = $schedulePeriods[$i];
			$this->_layoutIndexByEndTime[$endTime->timestamp] = $i;
		}
	}

	public function getFirstLayoutTime() { return $this->_firstLayoutTime; }

	public function layoutByStartTime() { return $this->_layoutByStartTime; }

	public function layoutIndexByEndTime() { return $this->_layoutIndexByEndTime; }
}
