<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lembur';

    protected $fillable = [
        'employee_id',
        'tanggal',
        'is_libur',
        'jam_mulai',
        'jam_selesai',
        'istirahat',
        'jumlah_jam',
        'jam_1_5',
        'jam_2',
        'jam_3',
        'jam_4',
        'total_akumulasi_lembur',
        'keterangan'
    ];

    /**
     * Relasi ke Karyawan (Untuk mengambil nama berdasarkan employee_id)
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Menghitung upah per jam otomatis
     */
    public function getUpahPerJamAttribute()
    {
        return 3496195 / 173;
    }

    /**
     * Menghitung total uang lembur
     */
    public function getTotalUangLemburAttribute()
    {
        return $this->total_akumulasi_lembur * $this->upah_per_jam;
    }
}
