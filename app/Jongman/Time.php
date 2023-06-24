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
}