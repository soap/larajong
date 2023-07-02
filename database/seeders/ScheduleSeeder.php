<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScheduleLayout;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $layoutId = ScheduleLayout::where('title', 'default')->value('id');
        if (!is_null($layoutId)) {
            $schedule = Schedule::create([
                'name' => 'Meeting Rooms',
                'slug' => 'meeting-rooms',
                'weekday_start' => 1,
                'days_visible' => 5,
                'time_on' => 480,
                'time_off' => 1020,
                'is_default' => 1,
                'timezone' => 'Asia/Bangkok',
                'time_format' => 24,
                'schedule_layout_id' => $layoutId,
            ]);

            $schedule->resources()->createMany([
                [
                    'name' => 'Meeting Room 1',
                    'slug' => 'meeting-room-1',
                    'ordering' => 1,
                ],
                [
                    'name' => 'Meeting Room 2',
                    'slug' => 'meeting-room-2',
                    'ordering' => 2,
                ]
            ]);
        }
    }
}
