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

    // Nama tabel di database MySQL Anda
    protected $table = 'pengajuan_pensiun';

    // Primary key untuk tabel ini
    protected $primaryKey = 'id_pengajuan';

    // Nonaktifkan timestamps karena skema tabel di gambar tidak memiliki 'created_at' dan 'updated_at'
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null; // Ini mencegah Laravel mencari kolom updated_at

    // Kolom-kolom yang dapat diisi secara massal (mass assignable fields)
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
        'created_at', // Tambahkan ini agar bisa terbaca saat konversi array
    ];
        // Tambahkan mutator untuk tmt_pegawai
    public function setTmtPegawaiAttribute($value)
    {
        // Konversi dari DD-MM-YYYY menjadi YYYY-MM-DD (format DB)
        $this->attributes['tmt_pegawai'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
    }

    // Tambahkan mutator untuk tmt_pensiun jika formatnya sama dari input
    public function setTmtPensiunAttribute($value)
    {
        $this->attributes['tmt_pensiun'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
    }

    public function logPersetujuanPensiun(): HasMany // Bisa juga tanpa ': HasMany'
    {
        // Sesuaikan 'id_pengajuan' dengan nama foreign key di tabel log_persetujuan_pensiun
        return $this->hasMany(LogPersetujuanPensiun::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function files(): HasMany
    {
        // Spasi ekstra sudah dihapus dari sini:
        return $this->hasMany(FilePersyaratanPensiun::class, 'pengajuan_pensiun_id', 'id_pengajuan');
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
