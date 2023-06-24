<?php

namespace App\Jongman\Layouts; 

class LayoutParser 
{
    private $layout;

    public function __construct(private string $timezone)
    {
        $this->layout = new LayoutSchedule($timezone);
    }
}