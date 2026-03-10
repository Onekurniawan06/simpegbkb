<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom id_divisi
            $table->unsignedBigInteger('id_divisi')->nullable()->after('nomor_urut_pegawai');

            // Tambahkan foreign key constraint
            $table->foreign('id_divisi')->references('id')->on('divisi')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_divisi');
        });
    }
};
