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
        Schema::create('reward_pembeli', function (Blueprint $table) {
            $table->integer('id_poin_reward', true);
            $table->integer('id_pembeli')->index('fk_id_pembeli_reward_pembeli');
            $table->integer('id_merch')->index('fk_id_merch_reward_pembeli');
            $table->integer('jumlah_poin_pembeli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_pembeli');
    }
};
