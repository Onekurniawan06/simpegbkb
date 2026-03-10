<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    // Nama tabel secara default adalah 'divisis' (jamak dari Divisi).
    // Karena kita menggunakan nama tabel 'divisi', kita perlu override properti $table.
    protected $table = 'divisi';
    protected $primaryKey = 'id_divisi';

    // Kolom-kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'kode_divisi',
        'nama_divisi',
        'deskripsi',
        // Jika Anda ingin mengizinkan pengisian lokasi lantai lagi
        // 'lokasi_lantai',
    ];
    
}
