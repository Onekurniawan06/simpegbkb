<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    // Sesuaikan dengan nama tabel di database Anda: simpegbkb jabatan
    protected $table = 'jabatan';

    // Primary key default adalah 'id', jika Anda menggunakan 'jabatan_id' sebagai PK
    // Anda bisa tambahkan ini:
    // protected $primaryKey = 'jabatan_id';
    // public $incrementing = false; // Jika PK bukan integer auto-incrementing

    protected $fillable = [
        'jabatan_id',
        'nama_jabatan',
    ];

    // Menonaktifkan manajemen otomatis created_at dan updated_at jika tabel tidak memilikinya
    public $timestamps = false;
}
