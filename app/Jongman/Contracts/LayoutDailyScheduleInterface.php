<?php

namespace App\Jongman\Contracts;

interface LayoutDailyScheduleInterface
{
    /**
     * Use daily layouts or single layout for all days
     *
     * @return bool
     */
    public function useDailayLayouts();
}
