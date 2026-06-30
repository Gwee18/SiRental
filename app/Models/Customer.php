<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $table = 'customers';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'no_telp',
        'alamat',
        'foto_ktp',
        'google_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}