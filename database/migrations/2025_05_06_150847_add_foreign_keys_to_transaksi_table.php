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
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreign(['id_barang'], 'FK_id_barang_transaksi')->references(['id_barang'])->on('barang_titipan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_pembeli'], 'FK_id_pembeli_transaksi')->references(['id_pembeli'])->on('pembeli')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign('FK_id_barang_transaksi');
            $table->dropForeign('FK_id_pembeli_transaksi');
        });
    }
};
