<?php

// app/Models/Reward.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Pegawai; // Pastikan Anda mengimpor model Pegawai

class Punishment extends Model
{
    use HasFactory;

    protected $table = 'punishment';
    protected $primaryKey = 'id_punishment';
    public $incrementing = true;

    protected $fillable = [
        'nomor_urut_pegawai',
        'jenis_punishment',
        'deskripsi',
        'tanggal_diberikan',
        'diberikan_oleh',
    ];

    protected $casts = [
        'tanggal_diberikan' => 'date',
    ];

    /**
     * Dapatkan pegawai yang memiliki reward ini.
     */
    public function pegawai()
    {
        // Relasi belongsTo:
        // 'Pegawai' adalah model terkait
        // 'nomor_urut_pegawai' adalah foreign key di tabel 'reward'
        // 'nomor_urut_pegawai' adalah local key (primary/unique key) di tabel 'pegawai'
        return $this->belongsTo(Pegawai::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

}
