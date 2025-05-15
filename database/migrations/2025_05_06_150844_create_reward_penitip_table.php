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
        Schema::create('reward_penitip', function (Blueprint $table) {
            $table->unsignedBigInteger('id_penitip')->index('fk_id_penitip_reward_penitip');
            $table->integer('jumlah_poin_penitip');
            $table->float('komisi_penitip');

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
        Schema::dropIfExists('reward_penitip');
    }
};
