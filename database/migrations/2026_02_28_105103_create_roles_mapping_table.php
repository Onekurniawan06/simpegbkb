<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('roles_mapping', function (Blueprint $table) {
            $table->id();
            $table->string('nup')->nullable(); // Untuk aturan spesifik orang (Nomor Urut Pegawai)
            $table->integer('level_id')->nullable();
            $table->integer('id_divisi')->nullable();
            $table->integer('jabatan_id')->nullable();
            $table->string('role_name'); // Output: 'manager', 'kepala skkmr', dll
            $table->string('route_name'); // Nama rute dashboard: 'skkmr.dashboard'
            $table->integer('priority'); // 1: NUP, 2: Level, 3: Jabatan (Urutan cek)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_mapping');
    }
};
