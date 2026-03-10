<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPribadi extends Model
{
    protected $table = 'detail_pribadi'; // Pastikan nama tabel benar
    /**
     * Beritahu Laravel bahwa Primary Key tabel ini bukan 'id'
     */
    protected $primaryKey = 'nomor_urut_pegawai';
    public $timestamps = false;
    // Tambahkan semua kolom data pribadi di sini
        // Beritahu Laravel bahwa ID ini tidak auto-increment
    public $incrementing = false;
    protected $fillable = [
        'nomor_urut_pegawai', // Ini kunci penghubung ke tabel pekerjaan/pegawai
        // 'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'alamat',
        'email',
        'no_telpon',
        'nama_ibu',
        'nama_ayah',
        'pendidikan_terakhir',
        'jurusan',
        'status_perkawinan',
        'jenis_kelamin',
        'dokumen_ktp',
        'dokumen_kk',
        'dokumen_npwp',
        'dokumen_buku_nikah',
        'dokumen_akta_cerai',
        'photo_selfie',
    ];

protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    // Opsional: Definisikan relasi kembali ke Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai');
    }
}
