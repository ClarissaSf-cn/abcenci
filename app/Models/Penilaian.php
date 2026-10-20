<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaians';

    protected $fillable = [
        'bulan',
        'tahun',
        'nik',
        'employee_name',
        'user',
        'vendor_os',
        'position_text',
        'pers_subarea_text',
        'skor_disiplin',
        'skor_kualitas',
        'skor_produktivitas',
        'skor_kerjasama',
        'skor_tanggung_jawab',
        'aspek_scores',
        'total_skor',
        'dipertahankan',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'aspek_scores' => 'array',
        'total_skor' => 'integer',
        'skor_disiplin' => 'integer',
        'skor_kualitas' => 'integer',
        'skor_produktivitas' => 'integer',
        'skor_kerjasama' => 'integer',
        'skor_tanggung_jawab' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'nik', 'nik');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
