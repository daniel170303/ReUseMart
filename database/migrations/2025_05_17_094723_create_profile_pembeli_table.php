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
        Schema::create('pembelis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_');
            $table->string('foto'); 
            $table->integer('poin')->default(0);
            $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('profile_pembeli');
    }
};
