<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'customer_id',
        'kode_transaksi',
        'status',
        'total_harga',
        'total_denda',
        'tanggal_pesan',
        'tanggal_mulai',
        'tanggal_selesai',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pesan'    => 'date',
            'tanggal_mulai'    => 'date',
            'tanggal_selesai'  => 'date',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }
}