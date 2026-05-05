<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PengajuanCuti; // <-- Ini yang benar
use App\Models\Pegawai;

class LogPersetujuanCuti extends Model
{
    protected $table = 'log_persetujuan_cuti';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'id_cuti',
        'nomor_urut_pegawai',
        'tahap_persetujuan',
        'nomor_urut_pegawai_penyetuju',
        'status_pengajuan',
        'komentar',
        'updated_at'
    ];

    // Relasi untuk mengetahui siapa pemilik pengajuan cuti
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanCuti::class, 'id_cuti', 'id_cuti');
    }

    protected static function booted()
    {
        // Setiap kali baris Log baru dibuat (admin klik setuju/tolak)
        static::created(function ($log) {
            // Ambil data pengajuan yang terkait
            $pengajuan = $log->pengajuan;

            if ($pengajuan) {
                // OTOMATIS update status di tabel pengajuan_cuti
                $pengajuan->status_pengajuan = $log->status_pengajuan;
                $pengajuan->save();
            }
        });
    }

    /**
     * Relasi Kunci: Untuk mengetahui detail (nama, level) dari INDIVIDU PENYETUJU.
     * Kita menghubungkan 'nomor_urut_pegawai_penyetuju' ke PK di tabel 'pegawai'.
     */
    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai_penyetuju', 'nomor_urut_pegawai');
    }

    public function logs()
    {
        // Kita hubungkan PK 'nomor_urut_pegawai' di tabel pengajuan
        // ke FK 'nomor_urut_pegawai' di tabel log_persetujuan_cuti
        return $this->hasMany(LogPersetujuanCuti::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function user(): BelongsTo
    {
        // Fungsi ini memberi tahu Laravel bahwa setiap log dimiliki oleh satu User.
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
