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
            $table
                ->string('nama_peminjam')
                ->nullable()
                ->after('customer_id');

            $table
                ->string('email_peminjam')
                ->nullable()
                ->after('nama_peminjam');

            $table
                ->string('no_telp_peminjam', 30)
                ->nullable()
                ->after('email_peminjam');

            $table
                ->text('alamat_peminjam')
                ->nullable()
                ->after('no_telp_peminjam');
        });

        /*
         * Isi snapshot transaksi lama menggunakan data customer saat
         * migration dijalankan. Kolom tetap nullable agar aman untuk
         * data lama yang relasi customernya sudah tidak tersedia.
         */
        DB::table('transaksi')
            ->select(['id', 'customer_id'])
            ->orderBy('id')
            ->chunkById(100, function ($transaksi) {
                $customerIds = $transaksi
                    ->pluck('customer_id')
                    ->filter()
                    ->unique()
                    ->values();

                $customers = DB::table('customers')
                    ->whereIn('id', $customerIds)
                    ->get([
                        'id',
                        'nama_lengkap',
                        'email',
                        'no_telp',
                        'alamat',
                    ])
                    ->keyBy('id');

                foreach ($transaksi as $item) {
                    $customer = $customers->get(
                        $item->customer_id
                    );

                    if (!$customer) {
                        continue;
                    }

                    DB::table('transaksi')
                        ->where('id', $item->id)
                        ->update([
                            'nama_peminjam' =>
                                $customer->nama_lengkap,
                            'email_peminjam' =>
                                $customer->email,
                            'no_telp_peminjam' =>
                                $customer->no_telp,
                            'alamat_peminjam' =>
                                $customer->alamat,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn([
                'nama_peminjam',
                'email_peminjam',
                'no_telp_peminjam',
                'alamat_peminjam',
            ]);
        });
    }
};