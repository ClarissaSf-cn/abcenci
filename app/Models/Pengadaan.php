<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengadaan extends Model
{
    use HasFactory;

    protected $table = 'pengadaans';

    protected $fillable = [
        'stationery_id',
        'no_pr',
        'tgl_pr',
        'estimasi_tiba',
        'tgl_released_pr',
        'no_po',
        'qty_item',
        'qty_po',
        'status',
    ];

    public function stationery()
    {

        return $this->belongsTo(Stationery::class, 'stationery_id');
    }
}
