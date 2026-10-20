<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationeryTransaction extends Model
{
    use HasFactory;

    protected $table = 'stationery_transactions';

    protected $fillable = [
        'stationery_id',
        'type',
        'jumlah',
        'tanggal',
        'no_fpb',
        'dept',
        'keterangan',
        'no_dokumen',
        'vendor'
    ];

    /**
     * Relasi Balik ke Master Stationery
     * Setiap transaksi pasti memiliki satu barang induk
     */
    public function stationery()
    {
        return $this->belongsTo(Stationery::class);
    }
}
