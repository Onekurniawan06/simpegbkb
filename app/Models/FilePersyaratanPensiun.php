<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilePersyaratanPensiun extends Model
{
    use HasFactory;

    protected $table = 'file_persyaratanpensiun';
    protected $primaryKey = 'id';
    
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_pensiun', // 🔄 Diubah dari pengajuan_pensiun_id
        'nomor_urut_pegawai',
        'nama_file_asli',
        'path_file_server',
        'tipe_dokumen',
    ];

    // ➕ Tambahkan relasi ke tabel utama pengajuan pensiun
    public function pengajuanPensiun(): BelongsTo
    {
        return $this->belongsTo(PengajuanPensiun::class, 'id_pensiun', 'id_pensiun');
    }
}
