<?php

namespace App\Http\Controllers;

use App\Jongman\DateRange;
use App\Jongman\Layouts\LayoutDaily;
use App\Jongman\Layouts\LayoutSchedule;
use App\Jongman\Reservations\ReservationListing;
use App\Jongman\Time;
use App\Models\Schedule;
use App\Models\TimeBlock;
use Illuminate\Support\Carbon;
use stdClass;

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

        $date = empty($date) ? Carbon::now($timezone) : Carbon::parse($date, $timezone);
        
        $displayDates = $this->getScheduleDates($schedule, $date, $timezone);
        $navigationLinks = $this->getNavigationLinks($schedule, $displayDates);
        $dailyDateFormat = 'Y-m-d';
        $layout = null; // $this->getDailyLayout($schedule, $displayDates, $timezone);

        return view('schedule.calendar',
            compact('schedule', 'displayDates', 'navigationLinks',
                'dailyDateFormat', 'layout')
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
        
        if ($startDay == 7) {
            $startDate = $selectedDate;
        } else {
            $adjustedDays = ($startDay - $selectedWeekday);
            if ($selectedWeekday < $startDay) {
                $adjustedDays = $adjustedDays - 7;
            }
            $startDate = $selectedDate->add('day', $adjustedDays);
        }
        $endDate = $startDate->clone()->addDays($scheduleLength - 1);
        return new DateRange($startDate, $endDate);
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


    protected function getNavigationLinks($schedule, DateRange $displayDates)
    {
        $startDate = $displayDates->getBegin()->clone();
        $endDate = $displayDates->getEnd()->clone();

        $startDay = $schedule->weekday_start;
    	$scheduleLength = $schedule->days_visible;
    	if ($startDay == 7)
    	{
    		$adjustment = $scheduleLength;
    		$prevAdjustment = $scheduleLength;
    	}else{
    		$adjustment = max($scheduleLength, 7);
            // ie, if 10, we only want to go back 7 days so there is overlap
    		$prevAdjustment = 7 * floor($adjustment / 7); 
    	}

        $obj = new stdClass();
        $obj->previousLink = route('schedule.calendar', [
            'schedule' => $schedule->id,
            'date' => $startDate->addDays(-$prevAdjustment)->format('Y-m-d')
        ]);

        $obj->nextLink = route('schedule.calendar', [
            'schedule' => $schedule->id,
            'date' => $endDate->addDays($adjustment)->format('Y-m-d')
        ]);

        return $obj;
    }
}
