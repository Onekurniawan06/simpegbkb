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
                // Khusus Pegawai: Cek Kelengkapan Profile (Sesuai permintaan Anda sebelumnya)
                if ($mapping->role_name === 'pegawai') {
                    $pegawaiExists = DB::table('pegawai')
                        ->where('nomor_urut_pegawai', $user->nomor_urut_pegawai)
                        ->exists();

                    if (!$pegawaiExists) {
                        return redirect()->route($mapping->route_name)
                            ->with('warning', 'Silahkan mengisi profile data diri sebagai pegawai bank kota bogor.');
                    }
                }

                if ($mapping->route_name === 'manager.dashboardmanager') {
                    $slugDivisi = \Illuminate\Support\Str::slug($user->divisi->nama_divisi ?? 'dashboard');

                    return redirect()->intended(route($mapping->route_name, ['divisi' => $slugDivisi]));
                }

                // Redirect biasa untuk rute yang tidak butuh parameter (seperti pegawai)
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

        $nomorUrutPegawai = $request->nomor_urut_pegawai;

        // Variabel untuk menampung ID yang akan disimpan ke tabel users
        $idJabatanTerpilih = null;
        $idDivisiTerpilih = null;

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

            // Untuk pegawai baru, ID Jabatan/Divisi mungkin default atau null sementara
        } else {
            $dataPekerjaan = DB::table('pekerjaan')
                ->where('nomor_urut_pegawai', $nomorUrutPegawai)
                ->first();

            if ($dataPekerjaan) {
                $idDivisiTerpilih = $dataPekerjaan->id_divisi;

                // 1. Ambil teks dan bersihkan spasi di ujung-ujung
                $kataPertama = explode(' ', trim($dataPekerjaan->jabatan))[0];
                $jabatanRecord = DB::table('jabatan')
                    ->where('nama_jabatan', 'LIKE', $kataPertama . '%')
                    ->first();

                // 3. Ambil ID-nya
                $idJabatanTerpilih = $jabatanRecord ? $jabatanRecord->jabatan_id : null;
            }
        }

        DB::beginTransaction();
        try {
            // dd($idJabatanTerpilih, $kataPertama);
            // 3. Simpan ke tabel USERS (Termasuk ID Jabatan & ID Divisi)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nomor_urut_pegawai' => $nomorUrutPegawai,
                'jabatan_id' => $idJabatanTerpilih, // Simpan ID Jabatan
                'id_divisi' => $idDivisiTerpilih,   // Simpan ID Divisi
            ]);

            if (empty($request->nomor_urut_pegawai)) {
                Pegawai::create([
                    'nomor_urut_pegawai' => $nomorUrutPegawai,
                    'nama' => $request->name,
                ]);

                // Tambahkan juga record di tabel pekerjaan untuk pegawai baru jika diperlukan
                DB::table('pekerjaan')->insert([
                    'nomor_urut_pegawai' => $nomorUrutPegawai,
                    'jabatan' => 'Pegawai Baru', // Contoh default
                    'id_divisi' => 1 // Contoh ID divisi default
                ]);
            }

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

