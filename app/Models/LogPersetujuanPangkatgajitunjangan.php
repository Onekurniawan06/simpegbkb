<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogPersetujuanPangkatgajitunjangan extends Model
{
    use HasFactory;

    protected $table = 'log_persetujuan_pangkatgajitunjangan';

    protected $primaryKey = 'id';

    public $timestamps = true;

    const CREATED_AT = null;

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id_kenaikan',
        'nomor_urut_pegawai',
        'tahap_persetujuan',
        'status_persetujuan',
        'komentar',
        'updated_at',
        'nomor_urut_pegawai_penyetuju',
    ];

    // protected $casts = [
    //     'status_persetujuan' => \App\Enums\StatusPersetujuan::class,
    // ];

    // 2. Tambahkan relasi balik ke model utama
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPangkatgajitunjangan::class, 'id_kenaikan', 'id_kenaikan');
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
