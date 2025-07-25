<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organisasi', function (Blueprint $table) {
            $table->string('password_organisasi', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('organisasi', function (Blueprint $table) {
            $table->string('password_organisasi', 50)->change();
        });
    }
};
