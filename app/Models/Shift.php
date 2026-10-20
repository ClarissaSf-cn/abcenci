<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts';

    protected $fillable = [
        'wst_code',
        'wst_text',
        'time_start',
        'time_end',
        'is_overnight',

        'break1_start',
        'break1_end',
        'break1_duration',
        'break2_start',
        'break2_end',
        'break2_duration',
        'group_ws_type',
    ];

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'shift_id');
    }
}
