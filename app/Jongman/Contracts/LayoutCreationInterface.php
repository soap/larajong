<?php

namespace App\Jongman\Contracts;

use App\Jongman\Time;

interface LayoutCreationInterface extends LayoutTimezoneInterface
{
    function appendPeriod(Time $startTime, Time $endTime, $label = null, $labelEnd = null);

    function appendBlockedPeriod(Time $startTime, Time $endTime, $label = null, $labelEnd = null);

    /**
     * @return LayoutPeriod[] array of LayoutPeriod
     */
    function getSlots();
}
