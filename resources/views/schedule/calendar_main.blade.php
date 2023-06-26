<div id="reservations">
    <!-- start of one day reservation table -->
    @foreach($displayDates as $date)
    <table class="reservations">
        <tr class="">
            <td class="resdate">{{ $date->format($dailyDateFormat) }}</td>
            @foreach($layout->getPeriods($date, true) as $period) 
            <td class="reslabel" colspan="{{ $period->span() }}">{{ $period->label($date)}}</td>
            @endforeach
        </tr>
    @foreach($resources as $resource)
    @php $slots = $layout->getLayout($date, $resource->id) @endphp
        <tr class="slots">
            <td class="resourcename">
            </td>
        
        @foreach($slots as $slot)
            <!-- call SlotFactory::display($slot, $slotRef, true, $this); -->
        @endforeach
        </tr>
    @endforeach
    </table>
    @endforeach
    <!-- end of one day reservation table -->
</div>