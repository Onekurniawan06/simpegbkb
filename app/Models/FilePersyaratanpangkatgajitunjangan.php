<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FilePersyaratanpangkatgajitunjangan extends Model
{
    protected $table = 'file_persyaratanpangkatgajitunjangan';

    // Menonaktifkan fitur timestamps bawaan Laravel
    public $timestamps = false;

    protected $fillable = [
        'pengajuan_pangkatgajitunjangan_id',
        'nomor_urut_pegawai',
        'nama_file_asli',
        'path_file_server',
        'tipe_dokumen',
        'created_at', // Tambahkan created_at ke fillable
    ];

    // Menggunakan event boot untuk mengisi created_at secara otomatis
    protected static function boot()
    {
        parent::boot();

        // Saat event 'creating' terjadi (sebelum insert ke DB)
        static::creating(function ($model) {
            $model->created_at = Carbon::now();
        });
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPangkatgajitunjangan::class, 'pengajuan_pangkatgajitunjangan_id', 'id_pengajuan');
    }
}
