<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jam_masuk',
        'jam_assorting',
        'jam_masuk_lembur',
        'status',
        'catatan',
        'nama_karyawan_assorting'
    ];

    protected $casts = [
        'jam_masuk' => 'datetime',
        'jam_assorting' => 'datetime',
        'jam_masuk_lembur' => 'datetime',
    ];
}
