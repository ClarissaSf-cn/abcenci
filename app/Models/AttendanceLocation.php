<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLocation extends Model
{
    use HasFactory;

    protected $table = 'attendance_locations';

    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius_meter',
        'polygon',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meter' => 'integer',
        'polygon' => 'array',
        'is_active' => 'boolean',
    ];
}

