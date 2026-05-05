<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanPensiun extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_pensiun';
    protected $primaryKey = 'id_pensiun';

    // 1. Aktifkan Timestamps secara standar
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'nomor_urut_pegawai',
        'nama_pegawai',
        'pangkat',
        'grade',
        'jabatan',
        'unit_kerja',
        'status_pegawai',
        'tmt_pegawai',
        'jenis_pengajuan',
        'masa_kerja',
        'tmt_pensiun',
        'status_pensiun', 
        'created_at',
    ];
    
    // Tambahkan mutator untuk tmt_pegawai
    public function setTmtPegawaiAttribute($value)
    {
        $this->attributes['tmt_pegawai'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
    }

    public function setTmtPensiunAttribute($value)
    {
        $this->attributes['tmt_pensiun'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
    }

    public function logPersetujuanPensiun(): HasMany
    {
        return $this->hasMany(LogPersetujuanPensiun::class, 'id_pensiun', 'id_pensiun')
                ->orderBy('id', 'asc'); // Agar stepper tracking berurutan
    }

    public function files(): HasMany
    {
        return $this->hasMany(FilePersyaratanPensiun::class, 'id_pensiun', 'id_pensiun');
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
        $query = self::whereHas('pegawai.pekerjaan', function ($q) use ($idDivisi) {
                $q->where('id_divisi', $idDivisi);
            })
            ->where('status_pensiun', 'diproses');

        if ($ignoreId) {
            $query->where('id_pensiun', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
