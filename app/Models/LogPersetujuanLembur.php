<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogPersetujuanLembur extends Model
{
    use HasFactory;

    protected $table = 'log_persetujuan_lembur';

    public $timestamps = true;

    // Sesuai gambar Anda, hanya ada updated_at
    const CREATED_AT = null;

    protected $fillable = [
        'id_lembur', // 🔄 Diubah dari lembur_id
        'nomor_urut_pegawai',
        'tahap_persetujuan',
        'nomor_urut_pegawai_penyetuju',
        'status_persetujuan',
        'komentar',
        // 🗑️ updated_at dihapus dari sini karena diisi otomatis
    ];

    // protected $casts = [
    //     'status_persetujuan' => \App\Enums\StatusPersetujuan::class,
    // ];

    // ➕ Tambahkan relasi ke tabel pengajuan lembur
    public function pengajuanLembur(): BelongsTo
    {
        return $this->belongsTo(PengajuanLembur::class, 'id_lembur', 'id_lembur');
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai_penyetuju', 'nomor_urut_pegawai');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
