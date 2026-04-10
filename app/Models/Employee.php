<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Jika Anda ingin nama tabelnya 'pegawai'
    protected $table = 'pegawai';

    // Tentukan kolom mana yang bisa diisi (Fillable)
    protected $fillable = [
        'nip',
        'nama_lengkap',
        'email',
        'jabatan',
        'departemen',
        'tanggal_bergabung',
        'status',
    ];

    public function detail()
    {
        return $this->hasOne(DetailPribadi::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }
}
