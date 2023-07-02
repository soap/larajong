<?php

namespace App\Helpers;

use Carbon\CarbonInterval;

class TimeSlotGenerator
{
    protected $interval;

    public function __construct($startTime, $endTime, int $minutes = 60)
    {
        $this->interval = CarbonInterval::minutes($minutes)
            ->toPeriod($startTime, $endTime);
    }

    public function get()
    {
        return $this->interval;
    }
}
