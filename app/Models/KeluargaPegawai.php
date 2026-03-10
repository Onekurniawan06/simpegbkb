<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeluargaPegawai extends Model
{
    // Jika nama tabel Anda di database bukan 'keluarga_pegawais', definisikan di sini:
    protected $table = 'keluarga_pegawai'; 

    // Masukkan kolom-kolom yang ada di tabel Anda agar bisa disimpan
    protected $fillable = [
        'nomor_urut_pegawai',
        'nama',
        'hubungan',
        'created_at',
    ];

    // Jika tabel Anda tidak memiliki kolom created_at dan updated_at, aktifkan baris bawah ini:
    // public $timestamps = false;
}
