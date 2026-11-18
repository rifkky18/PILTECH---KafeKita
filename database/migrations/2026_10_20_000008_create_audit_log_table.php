<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('nama_aksi', 100);
            $table->string('nama_tabel', 50);
            $table->integer('record_id')->nullable();
            $table->dateTime('waktu_perubahan');
            $table->text('keterangan')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_log');
    }
};
