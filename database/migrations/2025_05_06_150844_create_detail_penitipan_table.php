<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penitipan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_penitipan');

            $table->foreign('id_barang')->references('id_barang')->on('barang_titipan')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_penitipan')->references('id_penitipan')->on('penitipan')->onDelete('cascade')->onUpdate('cascade');

            $table->primary(['id_barang', 'id_penitipan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penitipan');
    }
};
