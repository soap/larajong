<?php

namespace App\Jongman\Helpers;

use Illuminate\Support\Carbon;

class CachedLayoutIndex
{
    private $_firstLayoutTime;

    private $_layoutByStartTime = [];

    private $_layoutIndexByEndTime = [];

    /**
     * @param  SchedulePeriod[]  $schedulePeriods
     */
    public function __construct($schedulePeriods, Carbon $startDate, Carbon $endDate)
    {
        $this->_firstLayoutTime = $endDate;

        for ($i = 0; $i < count($schedulePeriods); $i++) {
            /** @var Carbon $itemBegin */
            $itemBegin = $schedulePeriods[$i]->beginDate();
            if ($itemBegin->LessThan($this->_firstLayoutTime)) {
                $this->_firstLayoutTime = $schedulePeriods[$i]->beginDate();
            }

            /** @var Carbon $endTime */
            $endTime = $schedulePeriods[$i]->endDate();
            if (! $schedulePeriods[$i]->endDate()->eq($startDate)) {
                $endTime = $endDate;
            }

            $this->_layoutByStartTime[$itemBegin->timestamp] = $schedulePeriods[$i];
            $this->_layoutIndexByEndTime[$endTime->timestamp] = $i;
        }
    }

    public function getFirstLayoutTime()
    {
        return $this->_firstLayoutTime;
    }

    public function layoutByStartTime()
    {
        return $this->_layoutByStartTime;
    }

    public function layoutIndexByEndTime()
    {
        return $this->_layoutIndexByEndTime;
    }
}
