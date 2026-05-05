<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogPersetujuanPensiun extends Model
{
    use HasFactory;

    protected $table = 'log_persetujuan_pensiun';
    protected $primaryKey = 'id';

    // 1. Standarisasi Timestamps
    public $timestamps = true;
    const CREATED_AT = null; 
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id_pensiun', 
        'nomor_urut_pegawai',
        'tahap_persetujuan',
        'nomor_urut_pegawai_penyetuju',
        'status_persetujuan',
        'komentar',
    ];

    // protected $casts = [
    //     'status_persetujuan' => \App\Enums\StatusPersetujuan::class,
    // ];

    // --- RELASI ---

    public function pengajuanPensiun(): BelongsTo
    {
        return $this->belongsTo(PengajuanPensiun::class, 'id_pensiun', 'id_pensiun');
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai_penyetuju', 'nomor_urut_pegawai');
    }
}
