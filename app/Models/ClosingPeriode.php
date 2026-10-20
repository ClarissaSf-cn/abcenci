<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClosingPeriode extends Model
{
    use HasFactory;

    protected $table = 'closing_periodes';
    protected $fillable = ['bulan', 'tahun', 'is_closed', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
