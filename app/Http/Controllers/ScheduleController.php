<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Jongman\Layouts\LayoutSchedule;
use App\Models\Schedule;
use App\Models\TimeBlock;
use App\Jongman\Time;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreScheduleRequest $request)
    {
        //
    }

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

        $displayDates = CarbonInterval::days(1)
            ->toPeriod($startDate, $startDate->add('days', $scheduleLength-1));
        $dailyDateFormat = 'Y-m-d';
        $layout = $this->loadScheduleLayout($schedule, $timezone);

        return view('schedule.calendar', 
            compact('schedule', 'displayDates', 'dailyDateFormat', 'layout')
        );
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        //
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
