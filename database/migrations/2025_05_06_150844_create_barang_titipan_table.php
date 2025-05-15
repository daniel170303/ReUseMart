<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_titipan', function (Blueprint $table) {
            $table->id('id_barang'); 
            $table->string('nama_barang_titipan');
            $table->float('harga_barang');
            $table->string('deskripsi_barang');
            $table->string('jenis_barang');
            $table->string('garansi_barang', 50);
            $table->integer('berat_barang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_titipan');
    }
};
