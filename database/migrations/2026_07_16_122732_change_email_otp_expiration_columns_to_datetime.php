<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Pada MariaDB versi lama, kolom TIMESTAMP pertama dapat
         * memperoleh DEFAULT CURRENT_TIMESTAMP dan
         * ON UPDATE CURRENT_TIMESTAMP secara otomatis.
         *
         * Akibatnya, ketika attempts bertambah setelah kode salah,
         * expires_at ikut berubah menjadi waktu saat ini dan kode
         * langsung dianggap kedaluwarsa pada percobaan berikutnya.
         */
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE email_otps
                 MODIFY expires_at DATETIME NOT NULL,
                 MODIFY used_at DATETIME NULL'
            );

            return;
        }

        Schema::table('email_otps', function (Blueprint $table) {
            $table->dateTime('expires_at')->change();
            $table->dateTime('used_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            /*
             * DEFAULT ditulis eksplisit tanpa ON UPDATE agar rollback
             * tidak menghidupkan kembali masalah yang sama.
             */
            DB::statement(
                'ALTER TABLE email_otps
                 MODIFY expires_at TIMESTAMP NOT NULL
                    DEFAULT CURRENT_TIMESTAMP,
                 MODIFY used_at TIMESTAMP NULL DEFAULT NULL'
            );

            return;
        }

        Schema::table('email_otps', function (Blueprint $table) {
            $table
                ->timestamp('expires_at')
                ->useCurrent()
                ->change();

            $table
                ->timestamp('used_at')
                ->nullable()
                ->change();
        });
    }
};
