<?php

namespace App\Jongman\Reservations;

use App\Jongman\Contracts\MutableReservationListingInterface;
use Illuminate\Support\Carbon;

class ReservationListing implements MutableReservationListingInterface
{
    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var array|ReservationItemView[]
     */
    protected $_reservations = [];

    /**
     * @var array|ReservationItemView[]
     */
    protected $_reservationByResource = [];

    /**
     * @var array|ReservationItemView[]
     */
    protected $_reservationsByDate = [];

    /**
     * @var array|ReservationItemView[]
     */
    protected $_reservationsByDateAndResource = [];

    /**
     * @param  string  $targetTimezone
     */
    public function __construct($targetTimezone)
    {
        $this->timezone = $targetTimezone;
    }

    public function add($reservation)
    {
        $this->addItem(new ReservationListItem($reservation));
    }

    public function addBlackout($blackout)
    {
        $this->addItem(new BlackoutListItem($blackout));
    }

    protected function addItem(ReservationListItem $item)
    {
        $currentDate = $item->startDate()->toTimezone($this->timezone);
        $lastDate = $item->endDate()->toTimezone($this->timezone);

        if ($currentDate->dateEquals($lastDate)) {
            $this->addOnDate($item, $currentDate);
        } else {
            while (! $currentDate->dateEquals($lastDate)) {
                $this->addOnDate($item, $currentDate);
                $currentDate = $currentDate->addDays(1);
            }
            $this->addOnDate($item, $lastDate);
        }

        $this->_reservations[] = $item;
        $this->_reservationByResource[$item->resourceId()][] = $item;
    }

    protected function addOnDate(ReservationListItem $item, Carbon $date)
    {
        //		Log::Debug('Adding id %s on %s', $item->Id(), $date);
        $this->_reservationsByDate[$date->format('Ymd')][] = $item;
        $this->_reservationsByDateAndResource[$date->format('Ymd').'|'.$item->resourceId()][] = $item;
    }

    public function count()
    {
        return count($this->_reservations);
    }

    public function reservations()
    {
        return $this->_reservations;
    }

    /**
     * @param  array|ReservationListItem[]  $reservations
     * @return ReservationListing
     */
    private function create($reservations)
    {
        $reservationListing = new ReservationListing($this->timezone);

        if ($reservations != null) {
            foreach ($reservations as $reservation) {
                $reservationListing->addItem($reservation);
            }
        }

        return $reservationListing;
    }

    /**
     * @param  Date  $date
     * @return ReservationListing
     */
    public function onDate($date)
    {
        //		Log::Debug('Found %s reservations on %s', count($this->_reservationsByDate[$date->Format('Ymd')]), $date);

        $key = $date->Format('Ymd');
        $reservations = [];
        if (array_key_exists($key, $this->_reservationsByDate)) {
            $reservations = $this->_reservationsByDate[$key];
        }

        return $this->create($reservations);
    }

    public function forResource($resourceId)
    {
        if (array_key_exists($resourceId, $this->_reservationByResource)) {
            return $this->create($this->_reservationByResource[$resourceId]);
        }

        return new ReservationListing($this->timezone);
    }

    public function onDateForResource(Carbon $date, int $resourceId)
    {
        $key = $date->format('Ymd').'|'.$resourceId;

        if (! array_key_exists($key, $this->_reservationsByDateAndResource)) {
            return [];
        }

        return $this->_reservationsByDateAndResource[$key];
    }
}
