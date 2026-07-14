<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizeCustomerData();
        $this->normalizeOtpEmails();
        $this->ensureNoDuplicateGoogleIds();

        Schema::table('customers', function (
            Blueprint $table
        ) {
            $table->unique(
                'google_id',
                'customers_google_id_unique'
            );
        });

        Schema::table('email_otps', function (
            Blueprint $table
        ) {
            $table->index(
                ['email', 'used_at', 'id'],
                'email_otps_lookup_index'
            );

            $table->index(
                'expires_at',
                'email_otps_expires_at_index'
            );
        });

        Schema::table('detail_transaksi', function (
            Blueprint $table
        ) {
            $table->dropForeign(['alat_id']);

            $table->foreign('alat_id')
                ->references('id')
                ->on('alat')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('detail_transaksi', function (
            Blueprint $table
        ) {
            $table->dropForeign(['alat_id']);

            $table->foreign('alat_id')
                ->references('id')
                ->on('alat')
                ->onDelete('cascade');
        });

        Schema::table('email_otps', function (
            Blueprint $table
        ) {
            $table->dropIndex(
                'email_otps_lookup_index'
            );

            $table->dropIndex(
                'email_otps_expires_at_index'
            );
        });

        Schema::table('customers', function (
            Blueprint $table
        ) {
            $table->dropUnique(
                'customers_google_id_unique'
            );
        });
    }

    private function normalizeCustomerData(): void
    {
        $duplicateEmail = DB::table('customers')
            ->selectRaw(
                'LOWER(TRIM(email)) AS normalized_email'
            )
            ->selectRaw('COUNT(*) AS total')
            ->groupByRaw('LOWER(TRIM(email))')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicateEmail) {
            throw new RuntimeException(
                'Migration dibatalkan karena terdapat email customer duplikat setelah dinormalisasi: '
                .$duplicateEmail->normalized_email
            );
        }

        DB::table('customers')
            ->select('id', 'email', 'google_id')
            ->orderBy('id')
            ->chunkById(100, function ($customers) {
                foreach ($customers as $customer) {
                    $email = strtolower(
                        trim((string) $customer->email)
                    );

                    $googleId = trim(
                        (string) ($customer->google_id ?? '')
                    );

                    DB::table('customers')
                        ->where('id', $customer->id)
                        ->update([
                            'email' => $email,
                            'google_id' => $googleId !== ''
                                    ? $googleId
                                    : null,
                        ]);
                }
            });
    }

    private function normalizeOtpEmails(): void
    {
        DB::table('email_otps')
            ->select('id', 'email')
            ->orderBy('id')
            ->chunkById(200, function ($otps) {
                foreach ($otps as $otp) {
                    DB::table('email_otps')
                        ->where('id', $otp->id)
                        ->update([
                            'email' => strtolower(
                                trim((string) $otp->email)
                            ),
                        ]);
                }
            });
    }

    private function ensureNoDuplicateGoogleIds(): void
    {
        $duplicateGoogleId = DB::table('customers')
            ->select('google_id')
            ->selectRaw('COUNT(*) AS total')
            ->whereNotNull('google_id')
            ->groupBy('google_id')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicateGoogleId) {
            throw new RuntimeException(
                'Migration dibatalkan karena google_id digunakan oleh lebih dari satu customer: '
                .$duplicateGoogleId->google_id
            );
        }
    }
};
