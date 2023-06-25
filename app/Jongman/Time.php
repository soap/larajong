<?php

namespace App\Jongman;

use Illuminate\Support\Carbon;

class Time
{
	
	const FORMAT_HOUR_MINUTE = "H:i";

    public function __construct(private int $hour, private int $minute, private int $second = 0, private string $timezone = '')
    {
        if (empty($timezone)) {
            $this->timezone = config('app.timezone');
        }
    }

    private function getDate()
    {
        $parts = getdate(strtotime("$this->hour:$this->minute:$this->second"));
      
        return new Carbon("{$parts['year']}-{$parts['mon']}-{$parts['mday']} $this->hour:$this->minute:$this->second", $this->timezone);

    }

    public function parse($time, $timezone = null)
    {
        $date = new Carbon($time, $timezone);

        return new Time($date->hour, $date->minute, $date->second, $timezone);    
    }

	public function hour()
	{
		return $this->hour;
	}
	
	public function minute()
	{
		return $this->minute;
	}
	
	public function second()
	{
		return $this->second;
	}
	
	public function timezone()
	{
		return $this->timezone;
	}

	public function format($format)
	{
		return $this->getDate()->format($format);
	}
	
	public function toDatabase()
	{
		return $this->format('H:i:s');
	}

    public function toString()
	{
		return sprintf("%02d:%02d:%02d", $this->hour, $this->minute, $this->second);
	}
	
	public function __toString() 
	{
        return $this->toString();
  	}

	/**
	 * Compares this time to the one passed in
	 * Returns:
	 * -1 if this time is less than the passed in time
	 * 0 if the times are equal
	 * 1 if this time is greater than the passed in time
	 * @param Time $time
	 * @param Carbon $comparisonDate date to be used for time comparison
	 * @return int comparison result
	 */
	public function compare(Time $time, $comparisonDate = null)
	{
		if ($comparisonDate != null)
		{
			$myDate = Carbon::create($comparisonDate->year, $comparisonDate->month, $comparisonDate->day, $this->hour, $this->minute, $this->second, $this->timezone);
			$otherDate = Carbon::create($comparisonDate->year, $comparisonDate->month, $comparisonDate->day, $time->hour, $time->minute, $time->second, $time->timezone);
			
			return ($myDate->compare($otherDate));
		}
		
		return $this->getDate()->compare($time->getDate());
	}
}