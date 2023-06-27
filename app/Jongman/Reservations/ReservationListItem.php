<?php

namespace App\Jongman\Reservations;

use App\Jongman\Contracts\ReservedItemInterface;
use App\Jongman\SchedulePeriod;
use Illuminate\Support\Carbon;

class ReservationListItem
{
    /**
     * @var IReservedItemView
     */
    protected $item;

    public function __construct(ReservedItemInterface $reservedItem)
    {
        $this->item = $reservedItem;
    }

    /**
     * @return Carbon
     */
    public function startDate()
    {
        return $this->item->getStartDate();
    }

    /**
     * @return Carbon
     */
    public function endDate()
    {
        return $this->item->getEndDate();
    }

    public function occursOn(Carbon $date)
    {
        return $this->item->occursOn($date);
    }

    /**
     * @param  int  $span
     * @return IReservationSlot
     */
    public function buildSlot(SchedulePeriod $start, SchedulePeriod $end, Carbon $displayDate, $span)
    {
        return new ReservationSlot($start, $end, $displayDate, $span, $this->item);
    }

    /**
     * @return int
     */
    public function resourceId()
    {
        return $this->item->getResourceId();
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->item->getId();
    }
}
