<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function scheduleLayout()
    {
        return $this->belongsTo(ScheduleLayout::class);
    }
}
