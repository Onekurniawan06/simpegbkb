<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
// Import kelas Attribute
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\LogPersetujuanCuti;

class PengajuanCuti extends Model
{
    protected $table = 'pengajuan_cuti';
    protected $primaryKey = 'id_cuti'; // Ganti dari id ke id_cuti
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_cuti', // Masukkan id_cuti ke fillable jika ingin diisi manual, tapi biasanya otomatis
        'nomor_urut_pegawai',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_cuti',
        'jatah_periode_hari',
        'sisa_cuti',
        'keterangan'
    ];
    // Tambahkan mutator di sini
    protected function jatahPeriodeHari(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT),
        );
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            Log::debug('Mencoba menyimpan data PengajuanCuti:', $model->toArray());
        });
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti', 'nama_cuti');
    }

    public function logs(): HasMany {
        return $this->hasMany(LogPersetujuanCuti::class, 'id_cuti', 'id_cuti')
                    ->orderByDesc('updated_at');
    }

    public function user()
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
        // Gunakan relasi yang benar-benar menunjuk ke tabel 'pekerjaan'
        $query = self::whereHas('user.pegawai.pekerjaan', function ($q) use ($idDivisi) {
            // PENTING: Tambahkan nama tabel agar SQL tidak bingung
            // Ganti 'pekerjaans' dengan nama asli tabel pekerjaan Anda (misal: 'pekerjaan')
            $q->where('pekerjaan.id_divisi', $idDivisi);
        })
        ->where(function ($q) use ($startDate, $endDate) {
            $q->where('tanggal_mulai', '<=', $endDate)
            ->where('tanggal_selesai', '>=', $startDate);
        })
        // Pastikan status yang dicek adalah yang sudah disetujui atau sedang diproses
        // (Opsional: sesuaikan dengan logika bisnis Anda)
        ->whereHas('logs', function($q) {
            $q->whereIn('status_pengajuan', ['disetujui', 'diproses']);
        });

        if ($ignoreId) {
            // Gunakan primary key tabel pengajuan_cuti (contoh: id_pengajuan)
            $query->where('id_pengajuan', '!=', $ignoreId);
        }

        return $query->exists();
    }


}

