<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pekerjaan extends Model
{
    // Override nama tabel default (Laravel secara default mencari 'pekerjaans')
    protected $table = 'pekerjaan';

    // Override primary key default (Laravel secara default mencari 'id')
    protected $primaryKey = 'nomor_urut_pegawai';

    // Beri tahu Eloquent bahwa primary key BUKAN integer auto-incrementing
    public $incrementing = false;

    // Tentukan tipe data primary key (sesuai varchar(15) di DB)
    protected $keyType = 'string';

    // Tentukan kolom mana saja yang BOLEH diisi menggunakan mass assignment (create/update)
    protected $fillable = [
        'nomor_urut_pegawai',
        'golongan_pajak',
        'no_rekening',
    ];

    /**
     * Jika Anda ingin mencegah mass assignment pada kolom tertentu, gunakan $guarded.
     * protected $guarded = []; // Mengizinkan semua kolom diisi (hati-hati)
     */

    public function pegawai(): BelongsTo
    {
        // Parameter pertama: Model tujuan relasi (Pegawai::class)
        // Parameter kedua: Kunci asing (foreign key) di tabel 'pekerjaan' ('nomor_urut_pegawai')
        // Parameter ketiga: Kunci lokal (local key) di tabel 'pegawai' (asumsi juga 'nomor_urut_pegawai')
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function jabatan()
    {
        // Sesuaikan 'jabatan_id' jika nama kolom di tabel pekerjaan berbeda
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'jabatan_id');
    }

    public function detailJabatan(): BelongsTo // Ubah nama fungsi dari 'jabatan' menjadi 'detailJabatan'
    {
        // Sesuaikan 'level_id' jika nama kolom foreign key Anda berbeda di tabel pekerjaan
        // dan sesuaikan 'level_id' jika primary key di tabel jabatan berbeda
        return $this->belongsTo(Jabatan::class, 'level_id', 'level_id');
    }
}
