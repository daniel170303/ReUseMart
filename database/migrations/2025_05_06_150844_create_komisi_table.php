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
        Schema::create('komisi', function (Blueprint $table) {
            $table->integer('id_komisi', true);
            $table->integer('id_role')->nullable()->index('fk_id_role_komisi');
            $table->integer('id_penitip')->nullable()->index('fk_id_penitip_komisi');
            $table->integer('persen_komisi');
            $table->integer('jumlah_komisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komisi');
    }
};
