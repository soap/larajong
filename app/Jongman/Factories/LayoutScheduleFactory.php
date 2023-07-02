<?php

namespace App\Jongman\Factories;

use App\Jongman\Contracts\LayoutFactoryInterface;
use App\Jongman\Layouts\LayoutSchedule;

class LayoutScheduleFactory implements LayoutFactoryInterface
{
    public function __construct(private string $targetTimezone)
    {        
    }
    
    public function createLayout()
    {
        return new LayoutSchedule($this->targetTimezone);
    }
}
