<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleLayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'is_default', 'timezone', 'note',
    ];

    public function timeBlocks()
    {
        return $this->hasMany(TimeBlock::class);
    }
}
