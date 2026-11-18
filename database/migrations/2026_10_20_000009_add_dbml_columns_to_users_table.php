<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->unique();
            $table->string('password_hash')->nullable();
            $table->enum('role', ['admin_hr', 'karyawan', 'pemilik'])->default('karyawan');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
        });

        DB::statement('UPDATE users SET password_hash = password WHERE password_hash IS NULL');

        Schema::table('users', function (Blueprint $table) {
            $table->string('password_hash')->nullable(false)->change();
            $table->foreign('nik')->references('nik')->on('karyawan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['nik']);
            $table->dropUnique(['nik']);
            $table->dropColumn(['nik', 'password_hash', 'role', 'status']);
        });
    }
};
