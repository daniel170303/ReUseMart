<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisasiTable extends Migration
{
    public function up()
    {
        Schema::create('organisasi', function (Blueprint $table) {
            $table->id('id_organisasi');
            $table->string('nama_organisasi', 50);
            $table->string('alamat_organisasi', 50);
            $table->string('nomor_telepon_organisasi', 50);
            $table->string('email_organisasi', 50)->unique();
            $table->string('password_organisasi', 255); // Untuk menyimpan hash password
        });
    }

    public function down()
    {
        Schema::dropIfExists('organisasi');
    }
}
