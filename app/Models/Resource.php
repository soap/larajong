<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
