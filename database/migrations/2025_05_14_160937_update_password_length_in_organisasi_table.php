<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah kolom password_organisasi menjadi panjang 255 karakter.
     */
    public function up(): void
    {
        Schema::table('organisasi', function (Blueprint $table) {
            $table->string('password_organisasi', 255)->change();
        });
    }

    /**
     * Kembalikan kolom password_organisasi menjadi panjang 50 karakter.
     */
    public function down(): void
    {
        Schema::table('organisasi', function (Blueprint $table) {
            $table->string('password_organisasi', 50)->change();
        });
    }
};
