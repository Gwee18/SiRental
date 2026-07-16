<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    public const PEMBAYARAN_BELUM_BAYAR =
        'belum_bayar';

    public const PEMBAYARAN_SEWA_LUNAS =
        'sewa_lunas';

    public const PEMBAYARAN_LUNAS =
        'lunas';

    protected $table = 'transaksi';

    protected $fillable = [
        'customer_id',
        'request_token',
        'nama_peminjam',
        'email_peminjam',
        'no_telp_peminjam',
        'alamat_peminjam',
        'kode_transaksi',
        'status',
        'status_pembayaran',
        'total_harga',
        'total_denda',
        'total_dibayar',
        'dibayar_pada',
        'denda_dibayar_pada',
        'tanggal_pesan',
        'tanggal_mulai',
        'tanggal_selesai',
        'catatan',
        'foto_ktp',
    ];

    protected $attributes = [
        'status_pembayaran' => self::PEMBAYARAN_BELUM_BAYAR,
        'total_dibayar' => 0,
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pesan' => 'date',
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'dibayar_pada' => 'datetime',
            'denda_dibayar_pada' => 'datetime',
            'total_harga' => 'integer',
            'total_denda' => 'integer',
            'total_dibayar' => 'integer',
        ];
    }

    public function getStatusPembayaranLabelAttribute(): string
    {
        return match ($this->status_pembayaran) {
            self::PEMBAYARAN_BELUM_BAYAR => 'Belum Dibayar',

            self::PEMBAYARAN_SEWA_LUNAS => 'Sewa Lunas',

            self::PEMBAYARAN_LUNAS => 'Lunas',

            default => ucfirst(
                str_replace(
                    '_',
                    ' ',
                    (string) $this->status_pembayaran
                )
            ),
        };
    }

    public function getTotalTagihanAttribute(): int
    {
        return max(
            0,
            (int) $this->total_harga +
            (int) $this->total_denda
        );
    }

    public function getSisaTagihanAttribute(): int
    {
        return max(
            0,
            $this->total_tagihan -
            (int) $this->total_dibayar
        );
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(
            DetailTransaksi::class
        );
    }

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }
}
