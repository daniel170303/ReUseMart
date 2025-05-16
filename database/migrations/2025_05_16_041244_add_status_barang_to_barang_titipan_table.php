<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusBarangToBarangTitipanTable extends Migration
{
    public function up()
    {
        Schema::table('barang_titipan', function (Blueprint $table) {
            $table->enum('status_barang', ['dijual', 'barang untuk donasi'])->default('dijual');
        });
    }

    public function down()
    {
        Schema::table('barang_titipan', function (Blueprint $table) {
            $table->dropColumn('status_barang');
        });
    }
}
