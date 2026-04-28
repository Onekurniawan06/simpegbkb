<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Divisi;
use App\Models\DetailPribadi;

class Pegawai extends Model
{
    protected $table = 'pegawai'; // <-- Wajib ditambahkan
    public $timestamps = false;

    protected $primaryKey = 'nomor_urut_pegawai';
    public $incrementing = false; // Tambahkan ini kalau nomor urut bukan auto-incrementing integer standar
    protected $keyType = 'string'; // Tambahkan ini kalau nomor urut isinya ada huruf/karakter (opsional)
    protected $fillable = [
        // Data Pekerjaan (Contoh)
        'nomor_urut_pegawai',
        'nama',
        'nik',
        'npwp',
    ];

    public function detailPribadi(): HasOne
    {
        // Hubungkan model ini dengan DetailPribadi menggunakan foreign key 'pegawai_id'
        return $this->hasOne(DetailPribadi::class, 'nomor_urut_pegawai');
    }

    public function pekerjaan(): HasOne
    {
        return $this->hasOne(Pekerjaan::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    // Relasi One-to-Many ke Pengajuan Lembur
    public function pengajuanLembur(): HasMany
    {
        return $this->hasMany(PengajuanLembur::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function divisi(): BelongsTo
    {
        // Diasumsikan tabel 'pekerjaan' (atau tabel tempat Anda menyimpan id_divisi)
        // memiliki kolom foreign key bernama 'id_divisi'.
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }

    public function jabatan()
    {
        // Menghubungkan id_divisi (FK) ke id (PK) di tabel jabatan atau menggunakan field jabatan:varchar(100)
        // Asumsi ada kolom 'jabatan_id' di tabel pekerjaan yang merujuk ke tabel jabatan
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'jabatan_id');
    }
}
