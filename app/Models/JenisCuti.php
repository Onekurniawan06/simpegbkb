<?php

// app/Models/JenisCuti.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisCuti extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'jenis_cuti';

    // Menentukan primary key jika bukan 'id' standar (di gambar adalah 'id')
    protected $primaryKey = 'id';

    // Nonaktifkan timestamps jika tabel Anda tidak memiliki created_at dan updated_at
    public $timestamps = false;

    // Kolom yang dapat diisi (fillable)
    protected $fillable = [
        'nama_cuti',
        'durasi_hari',
        'durasi_bulan',
        'deskripsi_periode',
        'is_cuti_penting',
    ];

    public function subJenisCuti()
    {
        return $this->hasMany(SubJenisCutiPenting::class, 'id_jenis_cuti', 'id');
    }
}
