<?php

namespace App\Jongman\Contracts;

interface LayoutTimezoneInterface
{
    /**
     * Get configured timezone
     *
     * @return string
     */
    public function timezone();
}
