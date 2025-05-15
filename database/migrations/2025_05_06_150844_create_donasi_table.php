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
        Schema::create('donasi', function (Blueprint $table) {
            $table->id('id_donasi'); // otomatis unsignedBigInteger auto increment primary key
            $table->unsignedBigInteger('id_barang')->index('fk_id_barang_donasi');
            $table->unsignedBigInteger('id_request')->index('fk_id_request_donasi');
            $table->dateTime('tanggal_donasi');
            $table->string('penerima_donasi', 50);

            $table->foreign('id_barang')->references('id_barang')->on('barang_titipan')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_request')
                ->references('id_request')
                ->on('request')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi');
    }
};
