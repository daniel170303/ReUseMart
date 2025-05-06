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
        Schema::table('reward_pembeli', function (Blueprint $table) {
            $table->foreign(['id_merch'], 'FK_id_merch_reward_pembeli')->references(['id_merch'])->on('merchandise')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_pembeli'], 'FK_id_pembeli_reward_pembeli')->references(['id_pembeli'])->on('pembeli')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reward_pembeli', function (Blueprint $table) {
            $table->dropForeign('FK_id_merch_reward_pembeli');
            $table->dropForeign('FK_id_pembeli_reward_pembeli');
        });
    }
};
