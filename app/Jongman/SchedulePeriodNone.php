<?php

namespace App\Jongman;

class SchedulePeriodNone extends SchedulePeriod
{
    public function isReservable(): bool
    {
        return false;
    }

    public function toUtc(): SchedulePeriod
    {
        return new SchedulePeriodNone($this->begin->toUtc(), $this->end->toUtc(), $this->label);
    }

    public function toTimezone($timezone): SchedulePeriod
    {
        return new SchedulePeriodNone($this->begin->toTimezone($timezone), $this->end->toTimezone($timezone), $this->label);
    }
}
