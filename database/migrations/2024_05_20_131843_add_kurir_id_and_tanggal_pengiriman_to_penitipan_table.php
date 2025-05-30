<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penitipan', function (Blueprint $table) {
            $table->unsignedBigInteger('kurir_id')->nullable()->after('id_penitip');
            $table->dateTime('tanggal_pengiriman')->nullable()->after('kurir_id');

            // Jika ingin enforce relasi foreign key
            $table->foreign('kurir_id')->references('id_pegawai')->on('pegawai')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('penitipan', function (Blueprint $table) {
            $table->dropForeign(['kurir_id']);
            $table->dropColumn(['kurir_id', 'tanggal_pengiriman']);
        });
    }
};
