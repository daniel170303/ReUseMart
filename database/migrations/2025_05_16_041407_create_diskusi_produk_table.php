<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiskusiProdukTable extends Migration
{
    public function up(): void
    {
        Schema::create('diskusi_produk', function (Blueprint $table) {
            $table->id('id_diskusi');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('nama_pengirim', 100);
            $table->text('isi_pesan');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_barang')->references('id_barang')->on('barang_titipan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diskusi_produk');
    }
}
