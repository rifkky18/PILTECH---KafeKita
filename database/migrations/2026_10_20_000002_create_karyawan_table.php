<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nik', 16)->unique();
            $table->string('nama', 100);
            $table->string('no_hp', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_masuk');
            $table->unsignedInteger('jabatan_id');
            $table->foreign('jabatan_id')->references('id')->on('jabatan');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');

        });
    }

    public function down()
    {
        Schema::dropIfExists('karyawan');
    }
};
