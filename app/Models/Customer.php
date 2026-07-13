<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Customer extends Authenticatable
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

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Str::lower(
                trim((string) $value)
            ),
        );
    }

    protected function googleId(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $googleId = trim((string) ($value ?? ''));

                return $googleId !== ''
                    ? $googleId
                    : null;
            },
        );
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->foto_profil) {
            if (
                Str::startsWith(
                    $this->foto_profil,
                    ['http://', 'https://']
                )
            ) {
                return $this->foto_profil;
            }

            return Storage::url($this->foto_profil);
        }

        return 'https://ui-avatars.com/api/?name='
            . urlencode($this->nama_lengkap)
            . '&background=0F766E&color=fff&bold=true';
    }
}