<?php

namespace App\Jongman;

use Illuminate\Support\Carbon;

class SchedulePeriod
{
    public function __construct(
        protected Carbon $begin,
        protected Carbon $end,
        protected string $label = null)
    {
    }

    public function begin(): string
    {
        return $this->begin->toTimeString();
    }

    public function end(): string
    {
        return $this->end->toTimeString();
    }

    public function beginDate(): Carbon
    {
        return $this->begin;
    }

    public function endDate(): Carbon
    {
        return $this->end;
    }

    public function label(Carbon $dateOverride = null): string
    {
        if (empty($this->label)) {
            $format = config('jongman.time_format', 'H:i');
            if (isset($dateOverride) && ! $this->begin->eq($dateOverride)) {
                return $dateOverride->format($format);
            }

            return $this->begin->format($format);
        }

        return $this->label;
    }

    public function labelEnd(): string
    {
        if (empty($this->label)) {
            $format = config('jongman.time_format', 'H:i');

            return $this->end->format($format);
        }

        return '('.$this->label.')';
    }

    public function isReservable(): bool
    {
        return true;
    }

    public function isLabeled(): bool
    {
        return ! empty($this->label);
    }

    public function toUtc(): SchedulePeriod
    {
        return $this->toTimezone('UTC');
    }

    public function toTimezone($timezone): SchedulePeriod
    {
        return new SchedulePeriod(
            $this->begin->timezone($timezone),
            $this->end->timezone($timezone),
            $this->label
        );
    }

    /**
     * Compares the starting datetimes
     */
    public function compare(SchedulePeriod $other)
    {
        if ($this->begin->lt($other)) {
            return -1;
        } elseif ($this->begin->gt($other)) {
            return 1;
        }

        return 0;
    }

    public function beginsBefore(Carbon $date): bool
    {
        return $this->begin->lessThan($date);
    }

    public function __toString()
    {
        return sprintf('Begin: %s End: %s Label: %s', $this->begin, $this->end, $this->label());
    }
}
