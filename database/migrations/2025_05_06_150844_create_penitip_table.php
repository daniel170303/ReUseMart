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
        Schema::create('penitip', function (Blueprint $table) {
            $table->integer('id_penitip', true);
            $table->string('nama_penitip', 50);
            $table->string('nik_penitip', 16);
            $table->string('nomor_telepon_penitip', 50);
            $table->string('email_penitip', 50);
            $table->string('password_penitip', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penitip');
    }
};
