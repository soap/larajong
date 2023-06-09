<?php

namespace App\Jongman\Schedules;

use Illuminate\Support\Carbon;

class SchedulePeriodList
{
    private $items;

    private $addedStarts = [];

    private $addedTimes = [];

    private $addedEnds = [];

    public function add(SchedulePeriod $period)
    {
        if (! $period->isReservable()) {

        }

        if ($this->alreadyAdded($period->beginDate(), $period->endDate())) {
            return;
        }

        $this->items[] = $period;
    }

    public function getItems()
    {
        return $this->items;
    }

    private function alreadyAdded(Carbon $start, Carbon $end): bool
    {
        $startExists = false;
        $endExists = false;

        if (array_key_exists($start->timestamp, $this->addedStarts)) {
            $startExists = true;
        }

        if (array_key_exists($end->timestamp, $this->addedEnds)) {
            $endExists = true;
        }

        $this->addedTimes[$start->timestamp] = true;
        $this->addedEnds[$end->timestamp] = true;

        return $startExists || $endExists;
    }
}
