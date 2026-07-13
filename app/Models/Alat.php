<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stok_total' => 'integer',
            'stok_tersedia' => 'integer',
            'harga_per_hari' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeTersedia(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('stok_tersedia', '>', 0);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
