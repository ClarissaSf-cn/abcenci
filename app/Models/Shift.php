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

    public function getWstTextAttribute($value)
    {
        if (is_numeric($value) && (float)$value > 0 && (float)$value < 1) {
            if ($this->break1_start && $this->break1_end) {
                $pfx = substr($this->wst_code, 0, 2);
                $s = \Carbon\Carbon::parse($this->break1_start)->format('H.i');
                $e = \Carbon\Carbon::parse($this->break1_end)->format('H.i');
                return "{$pfx} {$s}-{$e}";
            }
            $totalMinutes = (int) round((float)$value * 1440);
            $hours = intdiv($totalMinutes, 60) % 24;
            $mins = $totalMinutes % 60;
            return sprintf('%02d.%02d', $hours, $mins);
        }
        return $value;
    }
}
