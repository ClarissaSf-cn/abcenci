<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $guarded = [];

    protected $casts = [
        'tmk' => 'date',
        'birthdate' => 'date',
        'tanggal_akhir_kerja' => 'date',
        'vaksin1_tanggal' => 'date',
        'vaksin2_tanggal' => 'date',
        'vaksin3_tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = true;

    public function lemburs()
    {
        return $this->hasMany(Lembur::class, 'employee_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'nik', 'nik');
    }
}
