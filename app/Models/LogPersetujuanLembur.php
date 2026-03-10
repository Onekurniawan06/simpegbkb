<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogPersetujuanLembur extends Model
{
    use HasFactory;

    protected $table = 'log_persetujuan_lembur';

    // Tetap aktifkan timestamps
    public $timestamps = true;

    // Beritahu Laravel bahwa kolom created_at tidak digunakan/tidak ada di DB
    const CREATED_AT = null;

    // Menentukan kolom yang dapat diisi (fillable)
    protected $fillable = [
        'lembur_id',
        'nomor_urut_pegawai',
        'tahap_persetujuan',
        'nomor_urut_pegawai_penyetuju',
        'status_persetujuan',
        'komentar',
        'updated_at',
    ];

    // Menentukan tipe data untuk enum status persetujuan (Laravel 11+ feature)
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

    public function user(): BelongsTo
    {
        // Fungsi ini memberi tahu Laravel bahwa setiap log dimiliki oleh satu User.
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
