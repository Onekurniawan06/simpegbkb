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
        Schema::create('keluarga_pegawai', function (Blueprint $table) {
            $table->id();
            // Gunakan string jika nomor_urut_pegawai mengandung karakter, atau bigInteger jika angka murni
            $table->string('nomor_urut_pegawai'); 
            $table->string('nama');
            $table->enum('hubungan', ['istri', 'anak']);
            $table->timestamps();

            // Opsional: Tambahkan foreign key jika ingin terhubung ketat dengan tabel pegawai
            // $table->foreign('nomor_urut_pegawai')->references('nomor_urut_pegawai')->on('pegawai')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluarga_pegawais');
    }
};
