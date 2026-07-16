<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('status_pembayaran', 20)
                ->default('belum_bayar')
                ->after('status');

            $table->unsignedBigInteger('total_dibayar')
                ->default(0)
                ->after('total_denda');

            $table->dateTime('dibayar_pada')
                ->nullable()
                ->after('total_dibayar');

            $table->dateTime('denda_dibayar_pada')
                ->nullable()
                ->after('dibayar_pada');
        });

        DB::table('transaksi')
            ->whereIn('status', ['menunggu', 'ditolak'])
            ->update([
                'status_pembayaran' => 'belum_bayar',
                'total_dibayar' => 0,
                'dibayar_pada' => null,
                'denda_dibayar_pada' => null,
            ]);

        DB::table('transaksi')
            ->whereIn('status', ['disetujui', 'aktif'])
            ->update([
                'status_pembayaran' => 'sewa_lunas',
                'total_dibayar' => DB::raw('COALESCE(total_harga, 0)'),
                'dibayar_pada' => DB::raw('COALESCE(tanggal_mulai, updated_at, created_at)'),
                'denda_dibayar_pada' => null,
            ]);

        DB::table('transaksi')
            ->where('status', 'selesai')
            ->update([
                'status_pembayaran' => 'lunas',
                'total_dibayar' => DB::raw('COALESCE(total_harga, 0) + COALESCE(total_denda, 0)'),
                'dibayar_pada' => DB::raw('COALESCE(tanggal_mulai, updated_at, created_at)'),
                'denda_dibayar_pada' => DB::raw(
                    'CASE WHEN COALESCE(total_denda, 0) > 0 '
                    .'THEN COALESCE(updated_at, created_at) ELSE NULL END'
                ),
            ]);
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn([
                'status_pembayaran',
                'total_dibayar',
                'dibayar_pada',
                'denda_dibayar_pada',
            ]);
        });
    }
};
