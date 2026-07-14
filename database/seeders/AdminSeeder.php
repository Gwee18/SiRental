<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = trim(
            (string) config(
                'sirental.admin.name',
                'Admin SiRental'
            )
        );

        $email = Str::lower(
            trim(
                (string) config(
                    'sirental.admin.email'
                )
            )
        );

        $password = (string) config(
            'sirental.admin.password'
        );

        if (
            $email === '' ||
            ! filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new InvalidArgumentException(
                'ADMIN_EMAIL harus berisi alamat email yang valid.'
            );
        }

        if ($password === '') {
            throw new RuntimeException(
                'ADMIN_PASSWORD belum diatur pada file .env.'
            );
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException(
                'ADMIN_PASSWORD minimal harus terdiri dari 8 karakter.'
            );
        }

        if (
            app()->environment('production') &&
            $password === 'admin123'
        ) {
            throw new RuntimeException(
                'Password admin bawaan tidak boleh digunakan pada production.'
            );
        }

        Admin::updateOrCreate(
            [
                'email' => $email,
            ],
            [
                'nama' => $name !== ''
                        ? $name
                        : 'Admin SiRental',

                'password' => Hash::make($password),
            ]
        );
    }
}
