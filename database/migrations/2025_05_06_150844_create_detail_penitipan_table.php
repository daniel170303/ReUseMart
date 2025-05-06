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
        Schema::create('detail_penitipan', function (Blueprint $table) {
            $table->integer('id_barang')->index('fk_id_barang_detail');
            $table->integer('id_penitipan')->index('fk_id_penitipan_detail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penitipan');
    }
};
