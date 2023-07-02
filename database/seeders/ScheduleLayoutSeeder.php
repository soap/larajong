<?php

namespace Database\Seeders;

use App\Helpers\TimeSlotGenerator;
use App\Models\ScheduleLayout;
use Illuminate\Database\Seeder;

class ScheduleLayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $layout = ScheduleLayout::create([
            'title' => 'default',
            'is_default' => 1,
            'timezone' => 'Asia/Bangkok',
        ]);
        $startTime = '08:00:00';
        $endTime = '17:00:00';
        $slots = (new TimeSlotGenerator($startTime, $endTime, 30))->get();
        $timeBlocks = [
            ['availability_code' => 2, 'start_time' => '00:00:00', 'end_time' => $startTime],
        ];

        foreach ($slots as $slot) {
            $blocks[] = $slot;
        }

        for ($i = 0; $i <= count($blocks) - 2; $i++) {

            $timeBlocks[] = [
                'availability_code' => 1,
                'start_time' => $blocks[$i]->format('H:i:s'),
                'end_time' => $blocks[$i + 1]->format('H:i:s'),
            ];

        }

        $timeBlocks[] = [
            'availability_code' => 2,
            'start_time' => $endTime,
            'end_time' => '00:00:00',
        ];

        $layout->timeBlocks()->createMany($timeBlocks);

    }
}
