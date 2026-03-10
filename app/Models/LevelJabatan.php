<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LevelJabatan extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'level_jabatan';

    // Menentukan primary key (karena bukan 'id')
    protected $primaryKey = 'level_id';

    /**
     * Nonaktifkan timestamps jika tabel Anda tidak memiliki
     * kolom created_at dan updated_at.
     */
    public $timestamps = false;

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'nama_level',
        'deskripsi',
    ];

    /**
     * Relasi ke model Jabatan.
     * Satu level jabatan (misal: Manajer) bisa dimiliki oleh banyak Jabatan.
     */
    public function jabatan(): HasMany
    {
        return $this->hasMany(Jabatan::class, 'level_id', 'level_id');
    }

    /**
     * Relasi ke model Pekerjaan.
     * Menghubungkan level_id langsung ke data penempatan pegawai.
     */
    public function pekerjaan(): HasMany
    {
        return $this->hasMany(Pekerjaan::class, 'level_id', 'level_id');
    }
}
