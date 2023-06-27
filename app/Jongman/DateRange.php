<?php

namespace App\Jongman;

use Illuminate\Support\Carbon;

class DateRange
{
	/**
	 * @var Carbon
	 */
	private $_begin;
	
	/**
	 * @var Carbon
	 */
	private $_end;

	/**
	 * @var string
	 */
	private $_timezone;

	/**
	 * @param RFDate $begin
	 * @param RFDate $end
	 * @param string $timezone
	 */
	public function __construct(Carbon $begin, Carbon $end, string $timezone = '')
	{
		$this->_begin = $begin;
		$this->_end = $end;

		if (empty($timezone))
		{
			$this->_timezone = $begin->timezone;
		}
		else
		{
			$this->_timezone = $timezone;
		}
	}

	/**
	 * @param string $beginString
	 * @param string $endString
	 * @param string $timezoneString
	 * @return DateRange
	 */
	public static function create($beginString, $endString, $timezoneString)
	{
		return new DateRange(Carbon::parse($beginString, $timezoneString), Carbon::parse($endString, $timezoneString), $timezoneString);
	}

	/**
	 * Whether or not the $date is within the range.  Range boundaries are inclusive
	 * @param RFDate $date
	 * @return bool
	 */
	public function contains(Carbon $date)
	{
		return $this->_begin->lte($date) && $this->_end->gte($date);
	}

	/**
	 * @param DateRange $dateRange
	 * @return bool
	 */
	public function containsRange(DateRange $dateRange)
	{
		return $this->_begin->lte($dateRange->_begin) && $this->_end->gte($dateRange->_end);
	}

	/**
	 * Whether or not the date ranges overlap.  Dates that start or end on boundaries are excluded
	 * @param DateRange $dateRange
	 * @return bool
	 */
	public function overlaps(DateRange $dateRange)
	{
		return (	$this->contains($dateRange->getBegin()) 
					|| $this->contains($dateRange->getEnd()) 
					|| $dateRange->contains($this->getBegin()) 
					|| $dateRange->contains($this->getEnd())) 
					&&
					(!$this->getBegin()->equals($dateRange->getEnd()) 
					&& !$this->getEnd()->equals($dateRange->getBegin())
				);

	}

	/**
	 * Whether or not any date within this range occurs on the provided date
	 * @param RFDate $date
	 * @return bool
	 */
	public function occursOn(Carbon $date)
	{
		$timezone = $date->timezone();
		$compare = $this;

		if ($timezone != $this->_timezone)
		{
			$compare = $this->toTimezone($timezone);
		}

		$beginMidnight = $compare->getBegin();

		if ($this->getEnd()->isMidnight())
		{
			$endMidnight = $compare->getEnd();
		}
		else
		{
			$endMidnight = $compare->getEnd()->addDays(1);
		}

		return ($beginMidnight->dateCompare($date) <= 0 &&
				$endMidnight->dateCompare($date) > 0);
	}

	/**
	 * @return Carbon
	 */
	public function getBegin()
	{
		return $this->_begin;	
	}

	/**
	 * @return Carbon
	 */
	public function getEnd()
	{
		return $this->_end;
	}
	
	/**
	 * @return array[int] Carbon
	 */
	public function dates()
	{
		$current = $this->_begin->getDate();
		$end = $this->_end->getDate();
		
		$dates = array($current);
		
		for($day = 0; $current->lt($end); $day++)
		{
			$current = $current->addDays(1);
			$dates[] = $current;
		}
		
		return $dates;
	}
	
	/**
	 * @param DateRange $otherRange
	 * @return bool
	 */
	public function equals(DateRange $otherRange)
	{
		return $this->_begin->equals($otherRange->getBegin()) && $this->_end->equals($otherRange->getEnd());
	}
	
	/**
	 * @param string $timezone
	 * @return DateRange
	 */
	public function toTimezone($timezone)
	{
		return new DateRange($this->_begin->setTimezone($timezone), $this->_end->setTimezone($timezone));
	}
	
	/**
	 * @return DateRange
	 */
	public function toUtc()
	{
		return new DateRange($this->_begin->toUtc(), $this->_end->toUtc());
	}
	
	/**
	 * @param int $days
	 * @return DateRange
	 */
	public function addDays($days)
	{
		return new DateRange($this->_begin->addDays($days), $this->_end->addDays($days));
	}
	
	/**
	 * @return string
	 */
	public function toString()
	{
		return "\nBegin: " . $this->_begin->toString() . " End: " . $this->_end->toString() . "\n";
	}

    /**
     * @return string
     */
    public function __toString()
	{
		return $this->toString();
	}
}