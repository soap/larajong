<?php

namespace App\Enums;

enum PeriodTypeEnum: int
{
    case RESERVABLE = 1;
    
    case UNRESERVABLE = 2;
}