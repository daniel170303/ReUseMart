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
        Schema::table('reward_penitip', function (Blueprint $table) {
            $table->foreign(['id_penitip'], 'FK_id_penitip_reward_penitip')->references(['id_penitip'])->on('penitip')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reward_penitip', function (Blueprint $table) {
            $table->dropForeign('FK_id_penitip_reward_penitip');
        });
    }
};
