<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanPangkatgajitunjangan extends Model
{
    protected $primaryKey = 'id_pengajuan';
    protected $table = 'pengajuan_pangkatgajitunjangan';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    protected $guarded = []; // Izinkan mass assignment untuk semua field

    public function files(): HasMany
    {
        return $this->hasMany(FilePersyaratanpangkatgajitunjangan::class, 'pengajuan_pangkatgajitunjangan_id', 'id_pengajuan');
    }

    public function logPersetujuanPangkatgajitunjangan(): HasMany // Bisa juga tanpa ': HasMany'
    {
        // Sesuaikan 'id_pengajuan' dengan nama foreign key di tabel log_persetujuan_pangkatgajitunjangan
        return $this->hasMany(LogPersetujuanPangkatgajitunjangan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function setTmtPegawaiAttribute($value)
    {
        // Konversi dari DD-MM-YYYY menjadi YYYY-MM-DD (format DB)
        $this->attributes['tmt_pegawai'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
    }

    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public static function isPengajuanExistInDivisi($idDivisi, $ignoreId = null)
    {
        // Mencari record pengajuan pensiun yang terkait dengan id_divisi yang diberikan
        $query = self::whereHas('pegawai.pekerjaan', function ($q) use ($idDivisi) {
                $q->where('id_divisi', $idDivisi);
            });
            // Semua logika tanggal/jam lembur telah dihapus di sini.

        // Abaikan ID pengajuan saat ini jika sedang melakukan update
        if ($ignoreId) {
            // Menggunakan 'id_pengajuan' sesuai kolom di gambar Anda
            $query->where('id_pengajuan', '!=', $ignoreId);
        }

        // Mengembalikan TRUE jika ditemukan setidaknya satu record dalam divisi tersebut
        return $query->exists();
    }
}
