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
        Schema::create('barang_titipan', function (Blueprint $table) {
            $table->integer('id_barang', true);
            $table->string('nama_barang_titipan');
            $table->float('harga_barang');
            $table->string('deskripsi_barang');
            $table->string('jenis_barang');
            $table->string('garansi_barang', 50);
            $table->integer('berat_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_titipan');
    }
};
