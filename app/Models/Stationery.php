<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stationery extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model ini.
     */
    protected $table = 'stationeries';

    /**
     * Kolom yang boleh diisi secara massal (Mass Assignable).
     * SUDAH DISESUAIKAN DENGAN FORMAT EXCEL GUDANG & RUMUS MINIMUM STOK BARU.
     */
    protected $fillable = [
        'kode',
        'nama_barang',
        'kategori',
        'satuan',
        'stok_awal',
        'stok_masuk',
        'stok_keluar',
        'harga_unit',
        'avg_penggunaan',
        'lead_time',
        'safety_stock',
        'min_stok',
        'is_active'
    ];

    /**
     * Mendaftarkan kolom Virtual (kolom yang dihitung otomatis oleh sistem)
     * agar bisa terbaca di Tampilan (View) dan saat Download Excel.
     */
    protected $appends = ['total_stok', 'status_cek', 'total_harga'];

    public function getTotalStokAttribute()
    {
        return $this->stok_awal + $this->stok_masuk - $this->stok_keluar;
    }

    public function getStatusCekAttribute()
    {
        return $this->total_stok <= $this->min_stok ? 'Order' : 'Tidak Order';
    }

    public function getTotalHargaAttribute()
    {
        return $this->total_stok * $this->harga_unit;
    }
}
