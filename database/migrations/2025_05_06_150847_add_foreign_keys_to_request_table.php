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
        Schema::table('request', function (Blueprint $table) {
            $table->foreign(['id_organisasi'], 'FK_id_organisasi')->references(['id_organisasi'])->on('organisasi')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request', function (Blueprint $table) {
            $table->dropForeign('FK_id_organisasi');
        });
    }
};
