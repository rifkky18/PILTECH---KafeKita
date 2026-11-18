<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('penggajian', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('karyawan_id');
            $table->foreign('karyawan_id')->references('id')->on('karyawan');
            $table->char('periode', 7);
            $table->decimal('gaji_pokok', 12, 2);
            $table->decimal('lembur', 12, 2)->default(0);
            $table->decimal('potongan', 12, 2)->default(0);
            $table->decimal('gaji_bersih', 12, 2);
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->dateTime('finalized_at')->nullable();

            $table->unique(['karyawan_id', 'periode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('penggajian');
    }
};
