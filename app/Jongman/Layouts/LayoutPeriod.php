<?php

namespace App\Jongman\Layouts;

use App\Jongman\Time;
use App\Enums\PeriodTypeEnum;

class LayoutPeriod
{
    public function __construct(public Time $start, public Time $end, public $periodType = PeriodTypeEnum::RESERVABLE, public string $label = '')
	{
	}
    
    public function periodTypeClass()
    {
        if ($this->periodType == PeriodTypeEnum::RESERVABLE){
            return 'SchedulePeriod';
        }

        return 'SchedulePeriodNone';
    }

    public function isReservable()
    {
        return $this->periodType == PeriodTypeEnum::RESERVABLE;
    }

    public function isLabeled()
    {
        return !empty($this->label);
    }

    public function timezone()
    {
        return $this->start->timezone();
    }

    public function compare(LayoutPeriod $other)
    {
        return $this->start->compare($other->start);
    }
}