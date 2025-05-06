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
            $table->integer('id_donasi', true);
            $table->integer('id_barang')->index('fk_id_barang_donasi');
            $table->integer('id_request')->index('fk_id_request_donasi');
            $table->dateTime('tanggal_donasi');
            $table->string('penerima_donasi', 50);
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
