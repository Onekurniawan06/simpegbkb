<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); // Primary key (auto-incrementing ID)
            $table->string('nip')->unique(); // NIP (Nomor Induk Pegawai), harus unik
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('jabatan');
            $table->string('departemen');
            $table->date('tanggal_bergabung');
            $table->string('status')->default('Aktif'); // Contoh: Aktif, Cuti, Keluar
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
