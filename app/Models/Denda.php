<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    protected $table = 'denda';

    protected $fillable = [
        'transaksi_id',
        'hari_terlambat',
        'denda_per_hari',
        'total_denda',
        'keterangan',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
