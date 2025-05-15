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
            $table->id('id_poin_reward');  // primary key bigint unsigned
            $table->unsignedBigInteger('id_pembeli')->index();
            $table->unsignedBigInteger('id_merch')->index();
            $table->integer('jumlah_poin_pembeli');

            // foreign key ke tabel pembeli (pastikan tabel pembeli sudah dibuat)
            $table->foreign('id_pembeli')
                ->references('id_pembeli')
                ->on('pembeli')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // foreign key ke tabel merchandise
            $table->foreign('id_merch')
                ->references('id_merch')
                ->on('merchandise')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
