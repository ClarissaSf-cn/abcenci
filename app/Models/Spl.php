<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spl extends Model
{
    use HasFactory;

    protected $table = 'spls';
    protected $fillable = [
        'tanggal',
        'vendor',
        'created_by',
        'dibuat_oleh',
        'ditugaskan_oleh',
        'disetujui_oleh',
        'diketahui_oleh',
    ];

    public function details()
    {
        return $this->hasMany(SplDetail::class, 'spl_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
