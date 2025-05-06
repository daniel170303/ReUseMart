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
        Schema::create('organisasi', function (Blueprint $table) {
            $table->integer('id_organisasi', true);
            $table->string('nama_organisasi', 50);
            $table->string('alamat_organisasi', 50);
            $table->string('nomor_telepon_organisasi', 50);
            $table->string('email_organisasi', 50);
            $table->string('password_organisasi', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisasi');
    }
};
