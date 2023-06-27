<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jongman\Layouts\LayoutSchedule;
use App\Jongman\Layouts\LayoutDaily;
use App\Models\Schedule;
use App\Models\TimeBlock;
use App\Jongman\Time;
use App\Jongman\DateRange;
use App\Jongman\Reservations\ReservationListing;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        return view('schedule.calendar', 
            compact('schedule')
        );
    }


    /**
     * Display the specified schedule in custom calendar mode.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar(Schedule $schedule, $date = null)
    {
        $timezone = 'Asia/Bangkok';

        $date = empty($date) ? Carbon::now() : Carbon::parse($date, $timezone);

        $displayDates = $this->getScheduleDates($schedule, $date, $timezone);

        $dailyDateFormat = 'Y-m-d';
        $layout = $this->getDailyLayout($schedule, $displayDates, $timezone);

        return view('schedule.calendar', 
            compact('schedule', 'displayDates', 'dailyDateFormat', 'layout')
        );
    }


    protected function getDailyLayout($schedule, $displayDates, $timezone = null)
    {
        $scheduleLayout = $this->loadScheduleLayout($schedule, $timezone);
        $reservationList = new ReservationListing($timezone);
        $layout = new LayoutDaily($reservationList, $scheduleLayout);

        return $layout;
    }

    protected function getScheduleDates(Schedule $schedule, Carbon $date, $timezone)
    {
        $selectedDate = $date->setTimezone($timezone)->setTime(0, 0, 0);
        $selectedWeekday = $date->dayOfWeek;
        $scheduleLength = $schedule->days_visible;

        $startDay = $schedule->weekday_start;
        if ($startDay == 7){
            $startDate = $selectedDate;
        }else{
            $adjustedDays = ($startDay - $selectedWeekday);
            if ($selectedWeekday < $startDay) {
                $adjustedDays = $adjustedDays - 7;
            }

            $startDate = $selectedDate->add('day', $adjustedDays);
        }

        return new DateRange($startDate, $startDate->addDays($scheduleLength-1));
    }

    protected function loadScheduleLayout(Schedule $schedule, $timezone = '')
    {
        $blocks = TimeBlock::where('schedule_layout_id', $schedule->schedule_layout_id)->get();
        if (empty($timezone)) {
            $timezone = $schedule->timezone;
        }

        $layout = new LayoutSchedule($timezone);

        foreach ($blocks as $period) {
            if ($period->availability_code == 1) {
                $layout->appendPeriod(
                    Time::parse($period->start_time), Time::parse($period->end_time),
                    (string)$period->label, $period->day_of_week
                );
            }else{
                $layout->appendBlockedPeriod(
                    Time::parse($period->start_time), Time::parse($period->end_time),
                    (string)$period->label, $period->day_of_week
                );
            }
        }

        return $layout;
    }
}
