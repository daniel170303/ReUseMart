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
        Schema::table('donasi', function (Blueprint $table) {
            $table->foreign(['id_barang'], 'FK_id_barang_donasi')->references(['id_barang'])->on('barang_titipan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_request'], 'FK_id_request_donasi')->references(['id_request'])->on('request')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donasi', function (Blueprint $table) {
            $table->dropForeign('FK_id_barang_donasi');
            $table->dropForeign('FK_id_request_donasi');
        });
    }
};
