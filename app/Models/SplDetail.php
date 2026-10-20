<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SplDetail extends Model
{
    use HasFactory;

    protected $table = 'spl_details';
    protected $fillable = ['spl_id','nik','nama','jabatan','jam_in','jam_out','lembur_awal','lembur_akhir','istirahat_awal','istirahat_akhir','total_jam','alasan','keterangan'];

    public function spl()
    {
        return $this->belongsTo(Spl::class, 'spl_id');
    }
}
