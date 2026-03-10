<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubJenisCutiPenting extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     * Secara default Laravel menggunakan nama jamak dari nama model (sub_jenis_cuti_pentings),
     * tetapi tabel Anda bernama simpegbkb_sub_jenis_cuti_penting (singular/prefix).
     * Jadi kita perlu menentukannya secara eksplisit.
     *
     * Jika nama tabel Anda benar-benar 'simpegbkb_sub_jenis_cuti_penting', gunakan kode di bawah ini:
     */
    protected $table = 'sub_jenis_cuti_penting';

    /**
     * Primary Key dari tabel
     */
    protected $primaryKey = 'id';

    /**
     * Tentukan kolom mana yang bisa diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'id_jenis_cuti',
        'nama_sub_jenis',
    ];

    /**
     * Nonaktifkan timestamps jika tabel Anda tidak menggunakan kolom created_at dan updated_at.
     * Berdasarkan gambar skema database yang Anda berikan, tabel ini memang tidak ada timestamps-nya.
     */
    public $timestamps = false;

    // Anda bisa menambahkan relasi di sini jika diperlukan
    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'id_jenis_cuti');
    }
}
