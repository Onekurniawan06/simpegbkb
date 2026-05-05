<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanPangkatgajitunjangan extends Model
{
    // 1. Ganti primary key menjadi id_kenaikan
    protected $primaryKey = 'id_kenaikan';

    protected $table = 'pengajuan_pangkatgajitunjangan';

    public $timestamps = true;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $guarded = []; // Izinkan mass assignment untuk semua field

    public function files(): HasMany
    {
        // 2. Ganti foreign key dan local key menjadi id_kenaikan
        return $this->hasMany(FilePersyaratanpangkatgajitunjangan::class, 'id_kenaikan', 'id_kenaikan');
    }

    public function logPersetujuanPangkatgajitunjangan(): HasMany
    {
        return $this->hasMany(LogPersetujuanPangkatgajitunjangan::class, 'id_kenaikan', 'id_kenaikan')
                    ->orderBy('id', 'asc');
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
        // Mencari record pengajuan yang terkait dengan id_divisi yang diberikan
        $query = self::whereHas('pegawai.pekerjaan', function ($q) use ($idDivisi) {
                $q->where('id_divisi', $idDivisi);
            });

        // 4. Ganti 'id_pengajuan' menjadi 'id_kenaikan' untuk ignore ID saat update
        if ($ignoreId) {
            $query->where('id_kenaikan', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
