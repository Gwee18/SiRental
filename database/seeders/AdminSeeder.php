<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'nama' => 'Admin SiRental',
            'email' => 'admin@sirental.com',
            'password' => Hash::make('admin123'),
        ]);
    }
}
