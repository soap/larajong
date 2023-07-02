<?php

namespace App\Jongman\Reservations;

use App\Jongman\Contracts\ReservationSlotInterface;
use App\Jongman\Factories\SlotLabelFactory;
use App\Jongman\Schedules\SchedulePeriod;
use Illuminate\Support\Carbon;

class ReservationSlot implements ReservationSlotInterface
{
    /**
     * @var Carbon
     */
    protected $_begin;

    /**
     * @var Carbon
     */
    protected $_end;

    /**
     * @var Carbon
     */
    protected $_displayDate;

    /**
     * @var int
     */
    protected $_periodSpan;

    /**
     * @var ReservationItemView
     */
    private $_reservation;

    /**
     * @var string
     */
    protected $_beginSlotId;

    /**
     * @var string
     */
    protected $_endSlotId;

    /**
     * @param  int  $periodSpan
     * @param  ReservationItemView  $reservation
     */
    public function __construct(SchedulePeriod $begin, SchedulePeriod $end, Carbon $displayDate, $periodSpan,
        ReservationItem $reservation)
    {
        $this->_reservation = $reservation;
        $this->_begin = $begin->beginDate();
        $this->_displayDate = $displayDate;
        $this->_end = $end->endDate();
        $this->_periodSpan = $periodSpan;

        $this->_beginSlotId = $begin->id();
        $this->_endSlotId = $end->id();
    }

    /**
     * @return Time
     */
    public function begin()
    {
        return $this->_begin->getTime();
    }

    /**
     * @return Carbon
     */
    public function beginDate()
    {
        return $this->_begin;
    }

    /**
     * @return Time
     */
    public function end()
    {
        return $this->_end->getTime();
    }

    /**
     * @return Carbon
     */
    public function endDate()
    {
        return $this->_end;
    }

    /**
     * @return Carbon
     */
    public function date()
    {
        return $this->_displayDate;
    }

    /**
     * @return int
     */
    public function periodSpan()
    {
        return $this->_periodSpan;
    }

    public function label($factory = null)
    {
        if (empty($factory)) {
            return SlotLabelFactory::create($this->_reservation);
        }

        return $factory->format($this->_reservation);
    }

    public function isReservable()
    {
        return false;
    }

    public function isReserved()
    {
        return true;
    }

    public function isPending()
    {
        return $this->_reservation->requiresApproval;
    }

    public function isPastDate(Carbon $date)
    {
        return $this->_displayDate->setTime($this->begin())->lessThan($date);
    }

    public function toTimezone($timezone)
    {
        return new ReservationSlot(
            $this->beginDate()->toTimezone($timezone),
            $this->endDate()->toTimezone($timezone),
            $this->date(), $this->periodSpan(), $this->_reservation);
    }

    /**
     * get reservation Id
     */
    public function getReservationId()
    {
        return $this->_reservation->reservationId;
    }

    /**
     * get reservation id (reference number)
     *
     * @deprecated 2.5
     */
    public function id()
    {
        return $this->_reservation->referenceNumber;
    }

    public function getInstanceId()
    {
        return $this->_reservation->instanceId;
    }

    public function getReferenceNumber()
    {
        return $this->_reservation->referenceNumber;
    }

    public function isOwnedBy(User $user)
    {
        return $this->_reservation->userId == $user->id;
    }

    public function isParticipating(User $user)
    {
        if (empty($user)) {
            $user = auth()->user;
        }
        $uid = $user->get('id');

        return $this->_reservation->isUserParticipating($uid) || $this->_reservation->isUserInvited($uid);
    }

    public function __toString()
    {
        return sprintf('Start: %s, End: %s, Span: %s', $this->begin(), $this->end(), $this->periodSpan());
    }

    /**
     * @return string
     */
    public function beginSlotId()
    {
        return $this->_beginSlotId;
    }

    /**
     * @return string
     */
    public function endSlotId()
    {
        return $this->_beginSlotId;
    }
}
