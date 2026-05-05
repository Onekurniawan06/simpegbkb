<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanLembur extends Model
{
    use HasFactory;

    // Menentukan primary key secara eksplisit
    protected $primaryKey = 'id_lembur';

    // Menentukan nama tabel secara eksplisit
    protected $table = 'pengajuan_lembur';

    // Menentukan kolom yang dapat diisi (fillable)
    protected $fillable = [
        'nomor_urut_pegawai',
        'tanggal_lembur',
        'jam_mulai',
        'jam_selesai',
        'total_jam_lembur',
        'status_lembur',
        'uraian_tugas',
    ];

    // Relasi ke Log Persetujuan
    public function user()
    {
        return $this->belongsTo(User::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function logPersetujuanLembur(): HasMany
    {
        return $this->hasMany(LogPersetujuanLembur::class, 'id_lembur')
        ->orderByDesc('updated_at');
    }

    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public static function isOverlapDivisi($idDivisi, $tanggalLembur, $jamMulai, $jamSelesai, $ignoreId = null)
    {
        $query = self::whereHas('pegawai.pekerjaan', function ($q) use ($idDivisi) {
                $q->where('id_divisi', $idDivisi);
            })
            ->where('tanggal_lembur', $tanggalLembur)
            ->where('status_lembur', '!=', 'ditolak')
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                ->where('jam_selesai', '>', $jamMulai);
            });

        if ($ignoreId) {
            $query->where('id_lembur', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
