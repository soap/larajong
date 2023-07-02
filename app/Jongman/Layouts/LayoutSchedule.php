<?php

namespace App\Jongman\Layouts;

use App\Jongman\Contracts\LayoutCreationInterface;
use Exception;
use App\Jongman\Helpers\PeriodTypeEnum;
use App\Jongman\Helpers\DayOfWeek;
use App\Jongman\Contracts\LayoutScheduleInterface;
use App\Jongman\Schedules\SchedulePeriod;
use App\Jongman\Schedules\SchedulePeriodList;
use App\Jongman\Schedules\SchedulePeriodNone;
use App\Jongman\Time;
use Carbon\Exceptions\InvalidFormatException;
use Carbon\Exceptions\InvalidTypeException;
use Illuminate\Support\Carbon;
use Symfony\Component\Translation\Exception\InvalidArgumentException;

class LayoutSchedule implements LayoutScheduleInterface, LayoutCreationInterface
{
    /**
     * @var array|LayoutPeriod[]
     */
    private $_periods = [];

    /**
     * @var string
     */
    // private $targetTimezone;

    /**
     * @var bool
     */
    private $cached = false;

    private $cachedPeriods = [];

    /**
     * @var bool
     */
    private $usingDailyLayouts = false;

    /**
     * @var string
     */
    private $layoutTimezone;

    public function __construct(private string $targetTimezone = '')
    {
        if ($targetTimezone == null) {
            // Laravel 's application timezone configuration used
            $this->targetTimezone = config('app.timezone');
        }
    }

    public function useDailayLayouts()
    {
        return $this->usingDailyLayouts;
    }

    /**
     * @param  DayOfWeek|int|null  $dayOfWeek
     * @return LayoutPeriod[]|array
     *
     * @throws Exception
     */
    public function getSlots($dayOfWeek = null)
    {
        if (is_null($dayOfWeek)) {
            if ($this->usingDailyLayouts) {
                throw new Exception('LayoutSchedule->getSlots() $dayOfWeek required when using daily layouts');
            }
            $periods = $this->_periods;
        } else {
            if (! $this->usingDailyLayouts) {
                throw new Exception('LayoutSchedule->getSlots() $dayOfWeek cannot be provided when using single layout');
            }
            $periods = $this->_periods[$dayOfWeek];
        }

        $this->sortItems($periods);

        return $periods;
    }

    /**
     * Appends a period to the schedule layout
     *
     * @param  Time  $startTime starting time of the schedule in specified timezone
     * @param  Time  $endTime ending time of the schedule in specified timezone
     * @param  string  $label optional label for the period
     * @param  DayOfWeek|int|null  $dayOfWeek
     */
    public function appendPeriod(Time $startTime, Time $endTime, $label = null, $dayOfWeek = null)
    {
        $this->appendGenericPeriod($startTime, $endTime, PeriodTypeEnum::RESERVABLE, $label, $dayOfWeek);
    }

    /**
     * Appends a period that is not reservable to the schedule layout
     *
     * @param  Time  $startTime starting time of the schedule in specified timezone
     * @param  Time  $endTime ending time of the schedule in specified timezone
     * @param  string  $label optional label for the period
     * @param  DayOfWeek|int|null  $dayOfWeek
     * @return void
     */
    public function appendBlockedPeriod(Time $startTime, Time $endTime, $label = '', $dayOfWeek = null)
    {
        $this->appendGenericPeriod($startTime, $endTime, PeriodTypeEnum::NONRESERVABLE, $label, $dayOfWeek);
    }

    protected function appendGenericPeriod(Time $startTime, Time $endTime, $periodType, $label = '', $dayOfWeek = null)
    {
        $this->layoutTimezone = $startTime->timezone();
        $layoutPeriod = new LayoutPeriod($startTime, $endTime, $periodType, $label);
        if (! is_null($dayOfWeek)) {
            $this->usingDailyLayouts = true;
            $this->_periods[$dayOfWeek][] = $layoutPeriod;
        } else {
            $this->_periods[] = $layoutPeriod;
        }
    }

    /**
     * @param  Carbon  $start
     * @param  Carbon  $end
     * @return bool
     */
    protected function spansMidnight(Carbon $start, Carbon $end): bool
    {
        return ! $start->isSameDay($end) && ! $end->isMidnight();
    }

    /**
     * Get schedule layout after having completed appending periods
     *
     * @param  Carbon  $layoutDate	Layout for spefied date, you may use different layout for each day of week
     * @param  bool  $hideBlockedPeriods Get blocked period (unreservable) or not
     * @return array|SchedulePeriod[]
     */
    public function getLayout(Carbon $layoutDate, $hideBlockedPeriods = false)
    {
        if ($this->usingDailyLayouts) {
            return $this->getLayoutDaily($layoutDate, $hideBlockedPeriods);
        }
        $targetTimezone = $this->targetTimezone;
        $layoutDate = $layoutDate->setTimezone($targetTimezone);

        $cachedValues = $this->getCachedValuesForDate($layoutDate);
        if (! empty($cachedValues)) {
            return $cachedValues;
        }

        $list = new SchedulePeriodList();

        $periods = $this->getPeriods($layoutDate);

        $layoutTimezone = $periods[0]->timezone();
        $workingDate = Carbon::create($layoutDate->year, $layoutDate->month, $layoutDate->day, 0, 0, 0,
            $layoutTimezone);
        $midnight = $layoutDate->getDate();

        /* @var $period LayoutPeriod */
        foreach ($periods as $period) {
            if ($hideBlockedPeriods && ! $period->isReservable()) {
                continue;
            }
            $start = $period->start;
            $end = $period->end;
            $periodType = $period->periodTypeClass();
            $label = $period->label;
            $labelEnd = null;

            // convert to target timezone
            $periodStart = $workingDate->setTimeFromTimeString($start)->setTimezone($targetTimezone);
            $periodEnd = $workingDate->setTimeFromTimeString($end, true)->setTimezone($targetTimezone);

            if ($periodEnd->lessThan($periodStart)) {
                $periodEnd = $periodEnd->addDays(1);
            }

            $startTime = Time::parse($periodStart->toTimeString(), $periodStart->timezone);
            $endTime = Time::parse($periodEnd->toTimeString(), $periodEnd->timezone);

            if ($this->bothDatesAreOff($periodStart, $periodEnd, $layoutDate)) {
                $periodStart = $layoutDate->setTimeFromTimeString($startTime);
                // set end time
                $periodEnd = $layoutDate->setTimeFromTimeString($endTime)->addDays(1);
            }

            if ($this->spansMidnight($periodStart, $periodEnd, $layoutDate)) {
                if ($periodStart->lessThan($midnight)) {
                    // add compensating period at end
                    $start = $layoutDate->setTimeFromTimeString($startTime);
                    $end = $periodEnd->addDays(1);
                    $list->add($this->buildPeriod($periodType, $start, $end, $label, $labelEnd));
                } else {
                    // add compensating period at start
                    $start = $periodStart->addDays(-1);
                    $end = $layoutDate->setTime($endTime, true);
                    $list->add($this->buildPeriod($periodType, $start, $end, $label, $labelEnd));
                }
            }

            if (! $periodStart->isMidnight() && $periodStart->lessThan($layoutDate) && $periodEnd->dateEquals($layoutDate) && $periodEnd->isMidnight()) {
                $periodStart = $periodStart->addDays(1);
                $periodEnd = $periodEnd->addDays(1);
            }

            $list->add($this->buildPeriod($periodType, $periodStart, $periodEnd, $label, $labelEnd));
        }

        $layout = $list->getItems();
        $this->sortItems($layout);
        $this->addCached($layout, $workingDate);

        return $layout;
    }

    /**
     * Get layout for specified date (if configured to use)
     * @param  Carbon $requestedDate
     * @param  bool  $hideBlockedPeriods
     * @return mixed
     *
     * @throws Exception
     * @throws InvalidFormatException
     * @throws InvalidTypeException
     * @throws InvalidArgumentException
     */
    private function getLayoutDaily(Carbon $requestedDate, $hideBlockedPeriods = false)
    {
        if ($requestedDate->timezone != $this->targetTimezone) {
            throw new Exception('Target timezone and requested timezone do not match');
        }

        $cachedValues = $this->getCachedValuesForDate($requestedDate);
        if (! empty($cachedValues)) {
            return $cachedValues;
        }

        // check cache
        $baseDateInLayoutTz = Carbon::create($requestedDate->year, $requestedDate->month, $requestedDate->day,
            0, 0, 0, $this->layoutTimezone);

        $list = new SchedulePeriodList();
        $this->addDailyPeriods($requestedDate->weekday, $baseDateInLayoutTz, $requestedDate, $list, $hideBlockedPeriods);

        if ($this->layoutTimezone != $this->targetTimezone) {
            $requestedDateInTargetTz = $requestedDate->setTimezone($this->layoutTimezone);

            $adjustment = 0;
            if ($requestedDateInTargetTz->format('YmdH') < $requestedDate->format('YmdH')) {
                $adjustment = -1;
            } else {
                if ($requestedDateInTargetTz->format('YmdH') > $requestedDate->format('YmdH')) {
                    $adjustment = 1;
                }
            }

            if ($adjustment != 0) {
                $adjustedDate = $requestedDate->addDays($adjustment);
                $baseDateInLayoutTz = $baseDateInLayoutTz->addDays($adjustment);
                $this->addDailyPeriods($adjustedDate->dayOfWeek, $baseDateInLayoutTz, $requestedDate, $list);
            }
        }
        $layout = $list->getItems();

        $this->sortItems($layout);
        $this->addCached($layout, $requestedDate);

        return $layout;
    }

    /**
     * @param  Carbon  $requestedDate
     * @param  PeriodList  $list
     * @param  bool  $hideBlockedPeriods
     */
    private function addDailyPeriods(int $day, Carbon $baseDateInLayoutTz, $requestedDate, $list, $hideBlockedPeriods = false)
    {
        $periods = $this->_periods[$day];
        /** @var $period LayoutPeriod */
        foreach ($periods as $period) {
            if ($hideBlockedPeriods && ! $period->isReservable()) {
                continue;
            }
            $begin = $baseDateInLayoutTz->setTimeFromString($period->start)->toTimezone($this->targetTimezone);
            $end = $baseDateInLayoutTz->setTimeFromString($period->end)->toTimezone($this->targetTimezone);
            // only add this period if it occurs on the requested date
            if ($begin->isSameDay($requestedDate) || ($end->isSameDay($requestedDate) && ! $end->isMidnight())) {
                $built = $this->buildPeriod($period->periodTypeClass(), $begin, $end, $period->label);
                $list->add($built);
            }
        }
    }

    /**
     * @param  array|SchedulePeriod[]  $layout
     * @param  Carbon  $date
     */
    private function addCached($layout, $date)
    {
        $this->cached = true;
        $this->cachedPeriods[$date->format('Ymd')] = $layout;
    }

    /**
     * @param  Carbon  $date
     * @return array|SchedulePeriod[]
     */
    private function getCachedValuesForDate($date)
    {
        $key = $date->format('Ymd');
        if (array_key_exists($date->format('Ymd'), $this->cachedPeriods)) {
            return $this->cachedPeriods[$key];
        }

        return null;
    }

    private function bothDatesAreOff(Carbon $start, Carbon $end, Carbon $layoutDate)
    {
        return ! $start->isSameDay($layoutDate) && ! $end->isSameDay($layoutDate);
    }

    private function buildPeriod(string $periodType, Carbon $start, Carbon $end, $label, $labelEnd = null)
    {
        // How to do like this when Pint exists
        // return new $periodType($start, $end, $label, $labelEnd);
        if ($periodType == 'SchedulePeriodNone') {
            return new SchedulePeriodNone($start, $end, $label, $labelEnd);
        }

        if ($periodType == 'SchedulePeriod') {
            return new SchedulePeriod($start, $end, $label, $labelEnd);
        }

    }

    protected function sortItems(&$items)
    {
        usort($items, [$this, 'sortBeginTimes']);
    }

    public function timezone()
    {
        return $this->targetTimezone;
    }

    protected function addPeriod(SchedulePeriod $period)
    {
        $this->_periods[] = $period;
    }

    /**
     * @static
     *
     * @param  SchedulePeriod|LayoutPeriod  $period1
     * @param  SchedulePeriod|LayoutPeriod  $period2
     * @return int
     */
    public static function sortBeginTimes($period1, $period2)
    {
        return $period1->compare($period2);
    }

    /**
     * @param  string  $timezone
     * @param  mixed  $reservableSlots
     * @param  mixed  $blockedSlots
     * @return ScheduleLayout
     */
    public static function parse($timezone, $reservableSlots, $blockedSlots)
    {
        $parser = new LayoutParser($timezone);
        $parser->addReservable($reservableSlots);
        $parser->addBlocked($blockedSlots);

        return $parser->getLayout();
    }

    /**
     * @param  string  $timezone
     * @param  string[]|array  $reservableSlots
     * @param  string[]|array  $blockedSlots
     * @return ScheduleLayout
     *
     * @throws Exception
     */
    public static function parseDaily($timezone, $reservableSlots, $blockedSlots)
    {
        if (count($reservableSlots) != DayOfWeek::numberOfDays || count($blockedSlots) != DayOfWeek::numberOfDays) {
            throw new Exception(sprintf('LayoutParser parseDaily missing slots. $reservableSlots=%s, $blockedSlots=%s',
                count($reservableSlots), count($blockedSlots)));
        }
        $parser = new LayoutParser($timezone);

        foreach (DayOfWeek::days() as $day) {
            $parser->addReservable($reservableSlots[$day], $day);
            $parser->addBlocked($blockedSlots[$day], $day);
        }

        return $parser->getLayout();
    }

    /**
     * @return SchedulePeriod period which occurs at this datetime. Includes start time, excludes end time
     */
    public function getPeriod(Carbon $date)
    {
        $timezone = $this->layoutTimezone;
        $tempDate = $date->setTimezone($timezone);
        $periods = $this->getPeriods($tempDate);

        /** @var $period LayoutPeriod */
        foreach ($periods as $period) {
            $start = Carbon::create($tempDate->year, $tempDate->month, $tempDate->day, $period->start->hour,
                $period->start->minute, 0, $timezone);
            $end = Carbon::create($tempDate->year, $tempDate->month, $tempDate->day, $period->end->hour,
                $period->end->minute, 0, $timezone);

            if ($end->lessThan($start) || $end->isMidnight()) {
                $end = $end->addDays(1);
            }
            // $start is less than or equals date and $end is greather than
            if ($start->le($date) && $end->gt($date)) {
                return $this->buildPeriod($period->periodTypeClass(), $start, $end, $period->label);
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function usesDailyLayouts()
    {
        return $this->usingDailyLayouts;
    }

    public function getPeriods(Carbon $layoutDate)
    {
        if ($this->usingDailyLayouts) {
            $dayOfWeek = $layoutDate->weekday();

            return $this->_periods[$dayOfWeek];
        } else {
            return $this->_periods;
        }
    }
}
