<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailOtp extends Model
{
    use MassPrunable;

    protected $fillable = [
        'email',
        'code_hash',
        'attempts',
        'expires_at',
        'used_at',
    ];

    protected $hidden = [
        'code_hash',
    ];

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
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

    public function prunable(): Builder
    {
        $batasPenyimpanan = now()->subDay();

        return static::query()
            ->where(function (Builder $query) use (
                $batasPenyimpanan
            ) {
                $query
                    ->where(
                        'expires_at',
                        '<=',
                        $batasPenyimpanan
                    )
                    ->orWhere(function (
                        Builder $usedQuery
                    ) use ($batasPenyimpanan) {
                        $usedQuery
                            ->whereNotNull('used_at')
                            ->where(
                                'used_at',
                                '<=',
                                $batasPenyimpanan
                            );
                    });
            });
    }
}
