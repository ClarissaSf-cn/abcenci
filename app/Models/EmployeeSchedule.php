<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;

    protected $table = 'employee_schedules';

    protected $fillable = [
        'nik',

        'shift_id',

        'shift_1_id',
        'shift_2_id',
        'shift_3_id',

        'schedule_date',
        'hari_kerja',
        'schedule_type',
        'special_note',
    ];

    /**
     * KUNCI RAHASIANYA DI SINI:
     * Otomatis memuat relasi 'employee' setiap kali data jadwal dipanggil,
     * sehingga Anda TIDAK PERLU mengubah file dashboard.blade.php
     */
    protected $with = ['employee'];

    /**
     * Relasi ke tabel shifts (3 kolom)
     */
    public function shift1()
    {
        return $this->belongsTo(Shift::class, 'shift_1_id');
    }

    public function shift2()
    {
        return $this->belongsTo(Shift::class, 'shift_2_id');
    }

    public function shift3()
    {
        return $this->belongsTo(Shift::class, 'shift_3_id');
    }

    /**
     * Relasi legacy (untuk kompatibilitas lama)
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    /**
     * Relasi ke tabel karyawan (Employee)
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'nik', 'nik');
    }
}
