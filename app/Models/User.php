<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\PengajuanCuti;
use App\Models\PengajuanLembur;
use App\Models\PengajuanPensiun;
use App\Models\PengajuanPangkatgajitunjangan;
use App\Models\DetailPribadi;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $primaryKey = 'nomor_urut_pegawai';
    public $incrementing = false;
    protected $fillable = [
        'name',
        'email',
        'password',
        'nomor_urut_pegawai',
        'id_divisi',
        'jabatan_id',
        'level_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'role',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRoleAttribute()
    {
        $role = 'pegawai';
        if ((int) $this->jabatan_id === 12) {
            $role = 'direkturUtama';
        } elseif ((int) $this->jabatan_id === 11) {
            $role = 'direkturOperasional';
        } elseif ((int) $this->jabatan_id === 10) {
            $role = 'direkturKepatuhan';
        } elseif ((int) $this->jabatan_id === 16) {
            $role = 'hro';
        } elseif ((int) $this->jabatan_id === 19) {
            $role = 'kepalaSKAudit';
        } elseif ((int) $this->jabatan_id === 18) {
            $role = 'kepalaSKKMR';
        } elseif ((int) $this->level_id === 2) {
            $role = 'manajer';
        }

        return $role;
    }

    public function pegawai()
    {
        // Parameter ketiga 'nomor_urut_pegawai' tidak diperlukan jika sama dengan kolom lokal
        return $this->hasOne(Pegawai::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function detailPribadi()
    {
        return $this->hasOne(DetailPribadi::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi'); // Sesuaikan foreign key jika perlu
    }

    public function jabatan()
    {
        // Parameter: (Model, Foreign Key di Users, Primary Key di Jabatan)
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'jabatan_id');
    }

    public function level()
    {
        // Parameter: (Model, Foreign Key di Users, Primary Key di Jabatan)
        return $this->belongsTo(LevelJabatan::class, 'level_id', 'level_id');
    }

    // datapengajuan
    public function cutis()
    {
        return $this->hasMany(PengajuanCuti::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function lemburs()
    {
        return $this->hasMany(PengajuanLembur::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function pensiuns()
    {
        return $this->hasMany(PengajuanPensiun::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }
    public function pangkatgajitunjangans()
    {
        return $this->hasMany(PengajuanPangkatgajitunjangan::class, 'nomor_urut_pegawai', 'nomor_urut_pegawai');
    }

    public function getMapping()
    {
        return DB::table('roles_mapping')
            ->where(function($q) {
                $q->where('nup', $this->nomor_urut_pegawa)
                ->orWhere('level_id', $this->level_id)
                ->orWhere('jabatan_id', $this->jabatan_id);
            })
            ->orderBy('priority', 'asc') // Cek dari yang paling spesifik
            ->first();
    }

    // Accessor agar $user->level_akses tetap bisa dipakai di kode Approval lama
    public function getLevelAksesAttribute()
    {
        $map = $this->getMapping();
        return $map ? $map->role_name : 'pegawai';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // 1. Ambil data teks jabatan dari tabel pekerjaan
            $pekerjaan = \DB::table('pekerjaan')
                ->where('nomor_urut_pegawai', $user->nomor_urut_pegawai)
                ->first();

            if ($pekerjaan) {
                // 2. Cari ID Jabatan di tabel 'jabatan' berdasarkan teks (Misal: "Manajer Umum")
                $jabatan = \DB::table('jabatan')
                    ->where('nama_jabatan', $pekerjaan->jabatan)
                    ->first();

                if ($jabatan) {
                    $user->jabatan_id = $jabatan->jabatan_id;
                    $user->id_divisi = $pekerjaan->id_divisi;

                    // 3. Logika Sementara: Jika jabatan mengandung kata "Kepala" atau "Manajer/Manager"
                    $namaJabatan = strtolower($pekerjaan->jabatan);
                    if (str_contains($namaJabatan, 'kepala') || str_contains($namaJabatan, 'manajer') || str_contains($namaJabatan, 'manager')) {
                        // Ambil level_id dari roles_mapping yang kategorinya manager (Level 2)
                        $mapping = \DB::table('roles_mapping')
                            ->where('level_id', 2)
                            ->first();

                        if ($mapping) {
                            $user->level_id = $mapping->level_id;
                        }
                    } else {
                        $user->level_id = 1;
                    }
                }
            }
        });
    }

    public function getDashboardLinkAttribute()
    {
        // 1. Ambil rute dari mapping tabel roles_mapping
        $mapping = \DB::table('roles_mapping')
        ->where('level_id', $this->level_id)
        ->where(function($query) {
            $query->where('jabatan_id', $this->jabatan_id)
                ->orWhereNull('jabatan_id');
        })
        ->orderBy('priority', 'asc')
        ->first();

        $routeName = $mapping->route_name ?? 'pegawai.dashboard';
        $routeNameLower = strtolower($routeName);

        if (str_contains($routeNameLower, 'manager') && !str_contains($routeNameLower, 'dashboardskkmr')) {
            $slug = Str::slug($this->divisi->nama_divisi ?? 'dashboard');
            return route($routeName, ['divisi' => $slug]);
        }
        return route($routeName);
    }

    public function getLayoutFileAttribute()
    {
        // Ambil mapping dengan urutan jabatan_id yang terisi dulu (lebih spesifik)
        $mapping = \DB::table('roles_mapping')
            ->where('level_id', $this->level_id)
            ->where(function($q) {
                $q->where('jabatan_id', $this->jabatan_id)
                ->orWhereNull('jabatan_id');
            })
            ->orderByRaw('jabatan_id IS NULL ASC') // Prioritaskan yang jabatan_id-nya ADA isinya
            ->first();

        if (!$mapping) return 'layouts.app-pegawai';

        $routeName = strtolower($mapping->route_name ?? '');
        $roleName = strtolower($mapping->role_name ?? '');

        // Logika pengecekan (Tetap seperti sebelumnya)
        if (str_contains($routeName, 'hro') || str_contains($roleName, 'hro') || str_contains($routeName, 'human')) {
            return 'layouts.app-hro';
        }

        if (str_contains($routeName, 'skkmr') || str_contains($roleName, 'skkmr')) {
            return 'layouts.app-skkmr';
        }

        if ($this->level_id == 3 || str_contains($routeName, 'direktur')) {
            return 'layouts.app-direktur';
        }

        if ($this->level_id == 2) {
            return 'layouts.app-manager';
        }

        return 'layouts.app-pegawai';
    }

}
