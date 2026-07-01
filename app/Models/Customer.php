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
        'foto_profil',
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

    /**
     * URL foto profil yang siap ditampilkan di navbar.
     * Kalau customer punya foto_profil (dari Google), pakai itu.
     * Kalau tidak (daftar manual), generate avatar dari inisial nama.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->foto_profil) {
            return $this->foto_profil;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_lengkap)
            . '&background=0F766E&color=fff&bold=true';
    }
}