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
        Schema::table('detail_penitipan', function (Blueprint $table) {
            $table->foreign(['id_barang'], 'FK_id_barang_detail')->references(['id_barang'])->on('barang_titipan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_penitipan'], 'FK_id_penitipan_detail')->references(['id_penitipan'])->on('penitipan')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_penitipan', function (Blueprint $table) {
            $table->dropForeign('FK_id_barang_detail');
            $table->dropForeign('FK_id_penitipan_detail');
        });
    }
};
