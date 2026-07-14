<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (
            Blueprint $table
        ) {
            $table->dropForeign(['customer_id']);

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (
            Blueprint $table
        ) {
            $table->dropForeign(['customer_id']);

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }
};
