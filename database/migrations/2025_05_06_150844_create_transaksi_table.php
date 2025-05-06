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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->integer('id_transaksi', true);
            $table->integer('id_barang')->index('fk_id_barang_transaksi');
            $table->integer('id_pembeli')->index('fk_id_pembeli_pegawai');
            $table->string('nama_barang');
            $table->dateTime('tanggal_pemesanan');
            $table->dateTime('tanggal_pelunasan');
            $table->string('jenis_pengiriman', 50);
            $table->dateTime('tanggal_pengiriman')->nullable();
            $table->dateTime('tanggal_pengambilan')->nullable();
            $table->integer('ongkir');
            $table->string('status_transaksi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
