<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    protected $table = 'alat';

    protected $fillable = [
        'nama_alat',
        'kategori',
        'stok_total',
        'stok_tersedia',
        'harga_per_hari',
        'kondisi',
        'deskripsi',
        'foto_alat',
    ];

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}