<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Import library Carbon
use App\Models\Pegawai; // Sesuaikan dengan namespace model Anda
use App\Models\DetailPribadi; // Sesuaikan dengan namespace model Anda

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi Awal
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
            'is_verified' => 'required|accepted',
        ], [
            'is_verified.accepted' => 'Harap geser slider verifikasi untuk melanjutkan.',
            'username.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('password'));
        }

        $credentials = $validator->validated();

        // 2. Proses Autentikasi
        if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 3. Cari Dashboard Berdasarkan Roles Mapping (Dinamis)
            // Mencari mapping yang paling cocok berdasarkan level, jabatan, dan divisi
            $mapping = DB::table('roles_mapping')
                ->where('level_id', $user->level_id)
                ->where(function($q) use ($user) {
                    $q->where('jabatan_id', $user->jabatan_id)
                    ->orWhereNull('jabatan_id');
                })
                ->where(function($q) use ($user) {
                    $q->where('id_divisi', $user->id_divisi)
                    ->orWhereNull('id_divisi');
                })
                ->orderBy('priority', 'asc') // Penting: Mengambil yang paling prioritas
                ->first();

            // 4. Logika Redirect Berdasarkan Hasil Mapping
            if ($mapping) {
                // A. Khusus Pegawai: Cek Kelengkapan Profile
                if ($mapping->role_name === 'pegawai') {
                    $pegawaiExists = DB::table('pegawai')
                        ->where('nomor_urut_pegawai', $user->nomor_urut_pegawai)
                        ->exists();

                    if (!$pegawaiExists) {
                        return redirect()->route($mapping->route_name)
                            ->with('warning', 'Silahkan mengisi profile data diri sebagai pegawai bank kota bogor.');
                    }
                }

                // B. Khusus Manager: Redirect dengan parameter Slug Divisi
                if ($mapping->route_name === 'manager.dashboardmanager') {
                    $slugDivisi = \Illuminate\Support\Str::slug($user->divisi->nama_divisi ?? 'dashboard');
                    return redirect()->intended(route($mapping->route_name, ['divisi' => $slugDivisi]));
                }

                // C. Khusus Direktur (TAMBAHAN BARU)
                // Jika route direktur tidak membutuhkan parameter slug/id di URL
                if ($mapping->route_name === 'direktur.dashboarddirektur') {
                    return redirect()->intended(route($mapping->route_name));
                }

                // Redirect standar untuk route lainnya
                return redirect()->intended(route($mapping->route_name));
            }

            // Default redirect jika tidak ditemukan di mapping
            return redirect('/');
        }

        return back()->withErrors([
            'verification' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function showRegistrationForm()
    {
        return view('register');
    }

    // Fungsi untuk memproses data registrasi
    public function register(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nomor_urut_pegawai' => 'nullable|string',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $nomorUrutPegawai = trim($request->nomor_urut_pegawai);

        // Variabel untuk menampung ID yang akan disimpan ke tabel users
        $idJabatanTerpilih = null;
        $idDivisiTerpilih = null;
        $idLevelTerpilih = 1; // Default ke 1 (Pegawai)

        // 2. Logika Pengecekan Kategori
        if (empty($nomorUrutPegawai)) {
            // --- LOGIKA PEGAWAI BARU ---
            $currentYear = Carbon::now()->format('Y');
            $tahunLahirInput = Carbon::parse($request->tanggal_lahir);
            $tahunLahir2Digit = $tahunLahirInput->format('y');

            $lastPegawai = Pegawai::orderBy(DB::raw('CAST(nomor_urut_pegawai AS SIGNED)'), 'desc')->first();
            $lastSequence = $lastPegawai ? (int)substr($lastPegawai->nomor_urut_pegawai, -3) : 0;
            $nextSequence = $lastSequence + 1;
            $nomorUrutPegawai = $currentYear . $tahunLahir2Digit . sprintf('%03d', $nextSequence);

            $idLevelTerpilih = 1;
        } else {
            // --- LOGIKA PEGAWAI LAMA / DIREKTUR ---
            $dataPegawai = DB::table('pegawai')
                ->where('nomor_urut_pegawai', $nomorUrutPegawai)
                ->first();

            // Ambil level_id dari tabel pegawai (Direktur=3, Manager=2, Pegawai=1)
            $idLevelTerpilih = $dataPegawai ? $dataPegawai->level_id : 1;

            $dataPekerjaan = DB::table('pekerjaan')
                ->where('nomor_urut_pegawai', $nomorUrutPegawai)
                ->first();

            // if ($dataPekerjaan) {
            //     $idDivisiTerpilih = $dataPekerjaan->id_divisi;

            //     // Ambil kata pertama jabatan (Contoh: "Direktur Utama" -> "Direktur")
            //     $jabatanArray = explode(' ', trim($dataPekerjaan->jabatan));
            //     $kataPertama = $jabatanArray[0];

            //     $jabatanRecord = DB::table('jabatan')
            //         ->where('nama_jabatan', 'LIKE', $kataPertama . '%')
            //         ->first();

            //     $idJabatanTerpilih = $jabatanRecord ? $jabatanRecord->jabatan_id : null;

            //     // FAIL-SAFE: Jika jabatannya mengandung kata "Direktur", paksa level_id jadi 3
            //     if (stripos($dataPekerjaan->jabatan, 'Direktur') !== false) {
            //         $idLevelTerpilih = 3;
            //     }
            // }
            if ($dataPekerjaan) {
                $idDivisiTerpilih = $dataPekerjaan->id_divisi;

                // 1. Cari Jabatan yang SAMA PERSIS (Penting agar ID 10, 11, 12 tidak tertukar)
                $namaJabatanInput = trim($dataPekerjaan->jabatan);
                $jabatanRecord = DB::table('jabatan')
                    ->where('nama_jabatan', $namaJabatanInput)
                    ->first();

                // 2. Jika tidak ketemu yang persis, baru pakai LIKE (Fallback)
                if (!$jabatanRecord) {
                    $kataPertama = explode(' ', $namaJabatanInput)[0];
                    $jabatanRecord = DB::table('jabatan')
                        ->where('nama_jabatan', 'LIKE', $kataPertama . '%')
                        ->first();
                }

                $idJabatanTerpilih = $jabatanRecord ? $jabatanRecord->jabatan_id : null;

                // 3. FAIL-SAFE LEVEL (Hanya jika jabatannya mengandung kata 'Direktur')
                if (stripos($namaJabatanInput, 'Direktur') !== false) {
                    $idLevelTerpilih = 3;
                }
            }
        }

        // --- 5. DEBUGGING (Hapus jika sudah berhasil) ---
        // Jika Anda masih ragu, aktifkan baris di bawah ini untuk melihat nilainya sebelum disimpan:
        // dd("NUP: $nomorUrutPegawai", "Level: $idLevelTerpilih");

        DB::beginTransaction();
        try {
            // 1. Gunakan Query Builder (Bypass Eloquent) untuk menghindari "intervensi" Model
            DB::table('users')->insert([
                'name'               => $request->name,
                'email'              => $request->email,
                'password'           => Hash::make($request->password),
                'nomor_urut_pegawai' => $nomorUrutPegawai,
                'jabatan_id'         => $idJabatanTerpilih,
                'id_divisi'          => $idDivisiTerpilih,
                'level_id'           => $idLevelTerpilih,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // Ambil instance user untuk digunakan di proses selanjutnya (jika dibutuhkan)
            $user = User::where('nomor_urut_pegawai', $nomorUrutPegawai)->first();

            // 2. Logika Pegawai Baru
            if (empty($request->nomor_urut_pegawai)) {
                Pegawai::create([
                    'nomor_urut_pegawai' => $nomorUrutPegawai,
                    'nama' => $request->name,
                ]);

                DB::table('pekerjaan')->insert([
                    'nomor_urut_pegawai' => $nomorUrutPegawai,
                    'jabatan' => 'Pegawai Baru',
                    'id_divisi' => 1
                ]);
            }

            // 3. Update Detail Pribadi
            DetailPribadi::updateOrCreate(
                ['nomor_urut_pegawai' => $nomorUrutPegawai],
                [
                    'email' => $request->email,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir
                ]
            );

            DB::commit();
            return redirect('/')->with('status', 'Registrasi berhasil.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal Simpan: ' . $e->getMessage()])->withInput();
        }

    }

    public function logout(Request $request)
    {
        Auth::logout(); // Menghapus data otentikasi dari session

        $request->session()->invalidate(); // Membatalkan seluruh session saat ini
        $request->session()->regenerateToken(); // Meregenerasi token CSRF baru

        // Mengarahkan pengguna ke halaman utama ('/')
        return redirect('/');
    }
}

