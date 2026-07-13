<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->timestamp('last_seen_transaksi_at')->nullable();
            $table->timestamp('last_seen_pelanggan_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn([
                'last_seen_transaksi_at',
                'last_seen_pelanggan_at',
            ]);
        });
    }
};