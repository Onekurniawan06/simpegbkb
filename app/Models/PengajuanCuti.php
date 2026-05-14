<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\LogPersetujuanCuti;

class PengajuanCuti extends Model
{
    protected $table = 'pengajuan_cuti';
    protected $primaryKey = 'id_cuti';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_cuti',
        'nomor_urut_pegawai',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_cuti',
        'jatah_periode_hari',
        'sisa_cuti',
        'keterangan',
        'jalur_dokumen_pendukung',
        'saldo_awal',        // WAJIB ADA
        'saldo_akhir',       // WAJIB ADA (Sesuai nama di DB)
        'status_pengajuan',  // WAJIB ADA
        'keterangan'
    ];

    public static function hitungSaldoAwal($nomor_pegawai, $nama_jenis_cuti)
    {
        $masterCuti = JenisCuti::where('nama_cuti', $nama_jenis_cuti)->first();
        $jatahMaster = $masterCuti ? ($masterCuti->durasi_hari ?? 0) : 12;

        if ($nama_jenis_cuti == 'Cuti Tahunan') {
            $terakhir = self::where('nomor_urut_pegawai', $nomor_pegawai)
                ->where('jenis_cuti', 'Cuti Tahunan')
                // Ambil yang statusnya sudah disetujui (atau masih diproses)
                ->whereIn('status_pengajuan', ['disetujui', 'diproses']) 
                ->orderBy('id_cuti', 'desc')
                ->first();

            // KUNCINYA DI SINI: Harus ambil 'saldo_akhir' (sesuai nama di database kamu)
            return $terakhir ? $terakhir->saldo_akhir : $jatahMaster;
        }

        return $jatahMaster;
    }



    // Tambahkan mutator untuk memastikan angka
    protected function jatahPeriodeHari(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT),
        );
    }

    // --- LOGIKA SALDO BERANTAI ---

    /**
     * Fungsi untuk mengambil saldo terakhir yang valid (Approved/Pending)
     */
    public static function getSisaCutiTerakhir($nomor_pegawai)
    {
        $last = self::where('nomor_urut_pegawai', $nomor_pegawai)
                    ->whereIn('status_pengajuan', ['disetujui', 'diproses', 'pending']) // Jangan ambil yang 'ditolak'
                    ->orderBy('id_cuti', 'desc')
                    ->first();

        return $last ? $last->saldo_akhir : 12; // Default 12 jika belum pernah cuti
    }

    // --- RELASI & BOOTED ---

    protected static function booted()
    {
        // static::creating(function ($model) {
        //     Log::debug('Mencoba menyimpan data PengajuanCuti:', $model->toArray());
        // });
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti', 'nama_cuti');
    }

    public function logs(): HasMany {
        return $this->hasMany(LogPersetujuanCuti::class, 'id_cuti', 'id_cuti')
                    ->orderBy('id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public static function isOverlapDivisi($idDivisi, $startDate, $endDate, $ignoreId = null)
{
    return self::where(function ($q) use ($startDate, $endDate) {
            $q->where('tanggal_mulai', '<=', $endDate)
              ->where('tanggal_selesai', '>=', $startDate);
        })
        // Pakai nama kolom yang benar sesuai tabelmu
        ->whereIn('status_pengajuan', ['diproses', 'disetujui'])
        // JANGAN tembak ke tabel pegawai, tapi tembak ke tabel pekerjaan lewat pegawai
        ->whereHas('pegawai', function ($q) use ($idDivisi) {
            $q->whereHas('pekerjaan', function ($sq) use ($idDivisi) {
                $sq->where('id_divisi', $idDivisi);
            });
        })
        ->when($ignoreId, function ($q) use ($ignoreId) {
            return $q->where('id_cuti', '!=', $ignoreId);
        })
        ->exists();
}


}
