<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'label', 'label_end', 'availability_code',
        'start_time', 'end_time', 'day_of_week',
    ];

    public function scheduleLayout()
    {
        return $this->belongsTo(ScheduleLayout::class);
    }
}
