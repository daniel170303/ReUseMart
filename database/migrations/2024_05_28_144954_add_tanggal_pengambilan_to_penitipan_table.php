<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('penitipan', function (Blueprint $table) {
            $table->date('tanggal_pengambilan')->nullable()->after('tanggal_selesai_penitipan');
        });
    }

    public function down()
    {
        Schema::table('penitipan', function (Blueprint $table) {
            $table->dropColumn('tanggal_pengambilan');
        });
    }

};
