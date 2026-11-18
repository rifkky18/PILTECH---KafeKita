<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('karyawan_id');
            $table->foreign('karyawan_id')->references('id')->on('karyawan');
            $table->unsignedInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shift');
            $table->date('tanggal');

            $table->unique(['karyawan_id', 'tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal');
    }
};
