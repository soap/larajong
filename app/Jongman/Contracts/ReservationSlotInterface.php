<?php

namespace App\Jongman\Contracts;

use App\Models\User;
use Illuminate\Support\Carbon;

interface ReservationSlotInterface
{
    /**
     * @return Time
     */
    public function begin();

    /**
     * @return Time
     */
    public function end();

    /**
     * @return Carbon
     */
    public function beginDate();

    /**
     * @return Carbon
     */
    public function endDate();

    /**
     * @return Carbon
     */
    public function date();

    /**
     * @return int
     */
    public function periodSpan();

    /**
     * @return string
     */
    public function label();

    /**
     * @return bool
     */
    public function isReservable();

    /**
     * @return bool
     */
    public function isReserved();

    /**
     * @return bool
     */
    public function isPending();

    /**
     * @param $date Carbon
     * @return bool
     */
    public function isPastDate(Carbon $date);

    /**
     * @param  string  $timezone
     * @return ReservationSlotInterface
     */
    public function toTimezone($timezone);

    /**
     * @return bool
     */
    public function isOwnedBy(User $user);

    /**
     * @return bool
     */
    public function isParticipating(User $user);

    /**
     * @return string
     */
    public function beginSlotId();

    /**
     * @return string
     */
    public function endSlotId();
}
