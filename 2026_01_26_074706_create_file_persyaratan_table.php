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
        Schema::create('file_persyaratanpangkatgajitunjangan', function (Blueprint $table) {
            $table->id();
            // Foreign key relationship
            $table->foreignId('pengajuan_pangkatgajitunjangan_id')->constrained('pengajuan_pangkatgajitunjangan', 'id_pengajuan')->onDelete('cascade');
            $table->string('nomor_urut_pegawai', 15);
            $table->string('nama_file_asli', 255);
            $table->string('path_file_server', 255); // Path tempat file disimpan
            $table->string('pangkat', 50);
            $table->string('tipe_dokumen', 100); // Nama dokumen (misal: "SK CPNS")
            $table->string('grade', 10);
            $table->string('jabatan', 100);
            $table->string('unit_kerja', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_persyaratanpangkatgajitunjangan');
    }
};
