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
        Schema::create('pembeli', function (Blueprint $table) {
            $table->id('id_pembeli');
            $table->string('nama_pembeli', 50);
            $table->string('alamat_pembeli', 50);
            $table->string('nomor_telepon_pembeli', 50);
            $table->string('email_pembeli', 50);
            $table->string('password_pembeli', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_pembeli');
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('pembeli'); // baru ini bisa dijalankan
    }
};
