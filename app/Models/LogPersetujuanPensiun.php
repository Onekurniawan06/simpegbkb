<?php

namespace App\Models;

use GuzzleHttp\Promise\Create;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogPersetujuanPensiun extends Model
{
    use HasFactory;

    protected $table = 'log_persetujuan_pensiun';
    protected $primaryKey = 'id';

    // Tetap true, tapi kita beritahu Laravel nama kolom aslinya di DB
    public $timestamps = true;

    // Beritahu Laravel bahwa kolom created_at tidak ada
    const CREATED_AT = null;

    // BERITAHU LARAVEL: Gunakan 'update_at', bukan 'updated_at'
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'id_pengajuan',
        'nomor_urut_pegawai',
        'tahap_persetujuan',
        'nomor_urut_pegawai_penyetuju',
        'status_persetujuan',
        'komentar',
        // 'update_at', // Hapus dari fillable karena sudah dikelola otomatis oleh timestamps
    ];

    // Kolom updated_at akan otomatis terisi oleh Laravel
    protected $casts = [
        'status_persetujuan' => \App\Enums\StatusPersetujuan::class, // Membutuhkan Enum kustom
    ];

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    // Relasi Many-to-One ke Pegawai (Penyetuju)
    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai_penyetuju', 'nomor_urut_pegawai');
    }
}
