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

        Carbon::macro('compare', function ($date) {
            $date2 = $date;
            if ($this->lt($date2)) {
                return -1;
            }else {
                if ($this->gt($date2)) {
                    return 1;
                }
            }

            return 0;
        });
    }
}
