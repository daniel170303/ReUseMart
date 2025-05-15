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
        Schema::create('penitipan', function (Blueprint $table) {
            $table->id('id_penitipan'); // unsignedBigInteger primary key auto increment
            $table->unsignedBigInteger('id_penitip')->index('fk_id_penitip');
            $table->dateTime('tanggal_penitipan');
            $table->dateTime('tanggal_selesai_penitipan');
            $table->dateTime('tanggal_batas_pengambilan');
            $table->string('status_perpanjangan', 20);
            $table->dateTime('tanggal_terjual')->nullable();
            $table->string('status_barang')->nullable();
            $table->timestamps();

            $table->foreign('id_penitip')
                ->references('id_penitip')
                ->on('penitip')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penitipan');
    }
};
