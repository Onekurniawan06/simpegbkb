<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilePersyaratanPensiun extends Model
{
    use HasFactory;

    protected $table = 'file_persyaratanpensiun';
    protected $primaryKey = 'id';
    // Tabel ini memiliki created_at, tapi tidak updated_at.
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null; // Matikan updated_at

    protected $fillable = [
        'pengajuan_pensiun_id', // <-- Tambahkan kolom baru di sini
        'nomor_urut_pegawai',
        'nama_file_asli',
        'path_file_server',
        'tipe_dokumen',
    ];
}
