<?php

namespace App\Jongman\Traits;

use Illuminate\Support\Carbon;

trait CustomDate
{
    public function registerCustomDateFunction()
    {
        Carbon::macro('getDate', function () {
            return $this->clone()->setTime(0, 0, 0);
        });
    }
}
