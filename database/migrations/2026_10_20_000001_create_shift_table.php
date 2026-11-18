<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shift', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_shift', 30);
            $table->time('jam_masuk');
            $table->time('jam_pulang');

        });
    }

    public function down()
    {
        Schema::dropIfExists('shift');
    }
};
