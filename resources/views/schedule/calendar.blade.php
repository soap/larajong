<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Schedule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="schedule_title">
                        <span>{{ $schedule->name }}</span>
                    </div>
                    <div class="schedule_dates">
                        @include('schedule.calendar_legend')
                        @include('schedule.calendar_main', 
                            compact('displayDates', 'dailyDateFormat', 'schedule')
                            )    
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php dump($displayDates) ?>
</x-app-layout>