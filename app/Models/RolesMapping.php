<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolesMapping extends Model
{
    // Pastikan nama tabel sesuai di database
    protected $table = 'roles_mapping';

    // Kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'level_id',
        'id_divisi',
        'jabatan_id',
        'role_name',
        'route_name',
        'priority'
    ];

    /**
     * Relasi ke tabel level_jabatan
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(LevelJabatan::class, 'level_id', 'level_id');
    }

    /**
     * Relasi ke tabel divisi
     */
    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    /**
     * Relasi ke tabel jabatan
     */
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'jabatan_id');
    }
}
