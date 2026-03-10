<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogPersetujuanPangkatgajitunjangan extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit jika tidak sesuai konvensi Laravel (logs...)
    protected $table = 'log_persetujuan_pangkatgajitunjangan';

    protected $primaryKey = 'id';
    // Tabel ini memiliki updated_at, jadi kita biarkan $timestamps default (true)
    public $timestamps = false;
    // const UPDATED_AT = 'updated_at'; // Kolom updated_at sudah sesuai
    // const CREATED_AT = 'create_at';

    // Menentukan kolom mana yang bisa diisi secara massal (mass assignable)
    protected $fillable = [
        'id_pengajuan',
        'nomor_urut_pegawai',
        'tahap_persetujuan',
        'status_persetujuan',
        'komentar',
        'updated_at',
        'nomor_urut_pegawai_penyetuju', // Kolom yang ada di skema gambar
    ];

    // Default primary key adalah 'id' dan sudah bigint(20) secara default di Laravel
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
