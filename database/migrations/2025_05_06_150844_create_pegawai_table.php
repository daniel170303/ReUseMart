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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->integer('id_pegawai', true);
            $table->integer('id_role')->index('fk_id_role');
            $table->string('nama_pegawai', 50);
            $table->string('nomor_telepon_pegawai', 50);
            $table->string('email_pegawai', 50);
            $table->string('password_pegawai', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
