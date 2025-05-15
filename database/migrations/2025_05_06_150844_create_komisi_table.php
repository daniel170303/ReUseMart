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
            $table->id('id_komisi');
            $table->integer('id_role')->nullable()->index('fk_id_role_komisi');
            $table->unsignedBigInteger('id_penitip')->nullable();
            $table->integer('persen_komisi');
            $table->integer('jumlah_komisi');

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
        Schema::dropIfExists('komisi');
    }
};
