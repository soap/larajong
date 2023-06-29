<?php

namespace App\Jongman\Repositories;

use App\Jongman\Contracts\LayoutFactoryInterface;
use App\Jongman\Contracts\ScheduleRepositoryInterface;
use App\Models\Schedule as EloquentSchedule;
use App\Jongman\Time;
use App\Jongman\Schedules\Schedule;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    public function loadById($scheduleId)
    {
        $row = EloquentSchedule::where('id', $scheduleId)->first();
        $schedule = Schedule::fromRow($row);

        return $schedule;
    }

    public function getAll()
    {
        $schedules = [];
        $rows = EloquentSchedule::with('scheduleLayout')->get();
        foreach($rows as $row) {
            $schedules[] = Schedule::fromRow($row);
        }

        return $schedules;
    }

    public function getLayout($scheduleId, LayoutFactoryInterface $layoutFactory)
    {
        /**
         * @var $layout LayoutSchedule
         */
        $layout = $layoutFactory->createLayout();
        $schedule = EloquentSchedule::with('scheduleLayout.timeBlocks')->where('id', $scheduleId)->first();
        $blocks = $schedule->scheduleLayout->timeBlocks;

        foreach ($blocks as $period) {
            if ($period->availability_code == 1) {
                $layout->appendPeriod(
                    Time::parse($period->start_time), Time::parse($period->end_time),
                    (string) $period->label, $period->day_of_week
                );
            } else {
                $layout->appendBlockedPeriod(
                    Time::parse($period->start_time), Time::parse($period->end_time),
                    (string) $period->label, $period->day_of_week
                );
            }
        }
        return $layout;
    }
}