<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dateTime('tanggal_mulai')->nullable()->change();
            $table->dateTime('tanggal_selesai')->nullable()->change();
        });

        $transaksiAktif = DB::table('transaksi')
            ->where('status', 'aktif')
            ->whereNotNull('tanggal_mulai')
            ->whereNotNull('tanggal_selesai')
            ->get();

        foreach ($transaksiAktif as $transaksi) {
            $tanggalMulaiLama = Carbon::parse($transaksi->tanggal_mulai);

            if ($tanggalMulaiLama->format('H:i:s') !== '00:00:00') {
                continue;
            }

            $lamaSewa = DB::table('detail_transaksi')
                ->where('transaksi_id', $transaksi->id)
                ->value('lama_sewa');

            $lamaSewa = max((int) ($lamaSewa ?? 1), 1);

            $tanggalMulaiBaru = $transaksi->updated_at
                ? Carbon::parse($transaksi->updated_at)
                : $tanggalMulaiLama;

            $tanggalSelesaiBaru = $tanggalMulaiBaru
                ->copy()
                ->addHours($lamaSewa * 24);

            DB::table('transaksi')
                ->where('id', $transaksi->id)
                ->update([
                    'tanggal_mulai' => $tanggalMulaiBaru,
                    'tanggal_selesai' => $tanggalSelesaiBaru,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->change();
            $table->date('tanggal_selesai')->nullable()->change();
        });
    }
};
