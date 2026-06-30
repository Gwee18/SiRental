<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('alat', function (Blueprint $table) {
        $table->id();
        $table->string('nama_alat');
        $table->string('kategori');
        $table->integer('stok_total');
        $table->integer('stok_tersedia');
        $table->decimal('harga_per_hari', 10, 2);
        $table->string('kondisi')->default('baik');
        $table->text('deskripsi')->nullable();
        $table->string('foto_alat')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
