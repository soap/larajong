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
                    <div id="schedule_title" class="flex justify-center">
                        <span class="px-2">{{ $schedule->name }}</span>
                        <a href="#" id="calendar_toggle"><i class="fa fa-calendar" aria-hidden="true"></i></a>
                    </div>
                    <div id="schedule_dates" class="flex justify-center">
                        <a href="{{ $navigationLinks->previousLink }}"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i></a>
                        {{ $displayDates->getBegin()->format('d/m/Y') }} - {{ $displayDates->getEnd()->format('d/m/Y') }}
                        <a href="{{ $navigationLinks->nextLink }} "><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                    </div>
                    <div id="datepicker" style="display:none"></div>
                    <div id="schedule_calendar">
                        @include('schedule.calendar_legend')
                        {{--@include('schedule.calendar_main', 
                            compact('displayDates', 'dailyDateFormat', 'schedule')
                            )    --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    <script>
        var picker = new Pikaday({ 
            field: document.getElementById('datepicker'),
            trigger: document.getElementById('calendar_toggle'),
            format: 'YYYY-MM-DD',
            showDaysInNextAndPreviousMonths: true,

            onSelect: function (selectedDate) {
                window.location.href = route('schedule.calendar', {
                    schedule: 1, date: moment(selectedDate).format('YYYY-MM-DD')
                });    
            } 
        });
    </script>
    
    @endpush
</x-app-layout>