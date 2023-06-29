<?php

namespace App\Jongman\Schedules;

class SchedulePeriodSpanable extends SchedulePeriod
{
    public function __construct(private SchedulePeriod $period, private int $span = 1)
    {
        $this->span = $span;
        $this->period = $period;

        parent::__construct($period->beginDate(), $period->endDate(), $period->label());
    }

    public function span(): int
    {
        return $this->span;
    }

    public function setSpan($span): void
    {
        $this->span = $span;
    }

    public function isReservable(): bool
    {
        return $this->period->isReservable();
    }
}
