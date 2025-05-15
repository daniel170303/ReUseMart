<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gambar_barang_titipan', function (Blueprint $table) {
            $table->id('id_gambar');
            $table->unsignedBigInteger('id_barang')->index();
            $table->string('nama_file_gambar');
            $table->timestamps();

            $table->foreign('id_barang')
                ->references('id_barang')
                ->on('barang_titipan')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gambar_barang_titipan');
    }
};
