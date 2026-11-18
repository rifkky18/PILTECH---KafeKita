<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_jabatan', 50)->unique();
            $table->decimal('gaji_pokok', 12, 2);

        });
    }

    public function down()
    {
        Schema::dropIfExists('jabatan');
    }
};
