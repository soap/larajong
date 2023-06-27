<?php

namespace App\Jongman\Reservations;

use App\Models\User;
use App\Jongman\Contracts\ReservationSlotInterface;
use App\Jongman\SchedulePeriod;
use Illuminate\Support\Carbon;

class ReservationSlotEmpty implements ReservationSlotInterface
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
	protected $_date;

	/**
	 * @var $_isReservable
	 */
	protected $_isReservable;

	/**
	 * @var int
	 */
	protected $_periodSpan;

	protected $_beginDisplayTime;
	protected $_endDisplayTime;

	protected $_beginSlotId;
	protected $_endSlotId;

	public function __construct(SchedulePeriod $begin, SchedulePeriod $end, Carbon $displayDate, $isReservable)
	{
		$this->_begin = $begin->beginDate();
		$this->_end = $end->endDate();
		$this->_date = $displayDate;
		$this->_isReservable = $isReservable;

		$this->_beginDisplayTime = $this->_begin->getTime();
		if (!$this->_begin->dateEquals($displayDate))
		{
			$this->_beginDisplayTime = $displayDate->getDate()->getTime();
		}

		$this->_endDisplayTime = $this->_end->getTime();
		if (!$this->_end->dateEquals($displayDate))
		{
			$this->_endDisplayTime = $displayDate->getDate()->getTime();
		}

		$this->_beginSlotId = $begin->Id();
		$this->_endSlotId = $end->Id();
	}

	/**
	 * @return Time
	 */
	public function begin()
	{
		return $this->_beginDisplayTime;
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
		return $this->_endDisplayTime;
	}

	/**
	 * @return Date
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
		return $this->_date;
	}

	/**
	 * @return int
	 */
	public function periodSpan()
	{
		return 1;
	}

	public function label()
	{
		return '';
	}

	public function isReservable()
	{
		return $this->_isReservable;
	}

	public function isReserved()
	{
		return false;
	}

	public function isPending()
	{
		return false;
	}

	public function isPastDate(Carbon $date)
	{
		$constraint = '';

		if (empty($constraint))
		{
			$constraint = 'default';
		}

		if ($constraint == 'none')
		{
			return false;
		}

		if ($constraint == 'current')
		{
			return $this->_date->setTime($this->end(), true)->lessThan($date);
		}

		return $this->_date->setTimeFromTimeString($this->begin())->lessThan($date);
	}

	public function toTimezone($timezone)
	{
		return new ReservationSlotEmpty($this->beginDate()->toTimezone($timezone), $this->end()->toTimezone($timezone), $this->date(), $this->_isReservable);
	}

	public function isOwnedBy(User $user)
	{
		return false;
	}

	public function isParticipating(User $user)
	{
		return false;
	}

	public function beginSlotId()
	{
		return $this->_beginSlotId;
	}

	public function endSlotId()
	{
		return $this->_endSlotId;
	}
}