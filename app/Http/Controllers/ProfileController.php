<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\KeluargaPegawai;
use App\Models\Pekerjaan;
use App\Models\Reward;
use App\Models\Punishment;
// ... models lain jika diperlukan ...

class ProfileController extends Controller
{
    public function showProfile(Request $request)
    {
        $user = Auth::user();
        $nomorUrutPegawai = $user->nomor_urut_pegawai;
        $formType = $request->query('form_type', 'edit');

        // 1. Ambil data pendukung
        $detailPribadi = DB::table('detail_pribadi')->where('nomor_urut_pegawai', $nomorUrutPegawai)->first();
        $pekerjaanData = Pekerjaan::where('nomor_urut_pegawai', $nomorUrutPegawai)->first();

        if ($pekerjaanData) {
            $tmtPegawai = Carbon::parse($pekerjaanData->tmt_pegawai);
            $interval = $tmtPegawai->diff(Carbon::now());
            $pekerjaanData->masa_kerja = sprintf('%d Tahun %d Bulan', $interval->y, $interval->m);
        }

        $istris = KeluargaPegawai::where('nomor_urut_pegawai', $nomorUrutPegawai)->where('hubungan', 'istri')->get();
        $anaks = KeluargaPegawai::where('nomor_urut_pegawai', $nomorUrutPegawai)->where('hubungan', 'anak')->get();
        $rewards = Reward::where('nomor_urut_pegawai', $nomorUrutPegawai)->get();
        $punishment = Punishment::where('nomor_urut_pegawai', $nomorUrutPegawai)->get();

        // 2. Gunakan Accessor dari Model untuk Layout (Sangat Bersih)
        $layoutFile = $user->layout_file; // Otomatis menangani SKKMR, Direktur, Manager, dll.

        // 3. Logika Judul & Dashboard Link (Gunakan Accessor Dashboard Link yang ada di model)
        $pageTitle = 'Profil Data ' . $user->name; // Lebih personal

        $breadcrumbs = [
            'Beranda' => $user->dashboard_link, // Memanggil getDashboardLinkAttribute() di model
            $pageTitle => null
        ];

        return view('profile', compact(
            'user', 'detailPribadi', 'formType', 'pageTitle',
            'breadcrumbs', 'istris', 'anaks', 'pekerjaanData',
            'rewards', 'punishment',
            'layoutFile'
        ));
    }

    // Function UpdateProfile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Sesi Anda habis.');
        }

        $nomorUrut = trim($user->nomor_urut_pegawai);

        // 1. VALIDASI SEMUA DI AWAL (Jangan di dalam loop atau try-catch)
        $request->validate([
            'nomor_urut_pegawai' => 'required|in:' . $nomorUrut,
            'tempat_lahir'       => 'required|string|max:100',
            'tanggal_lahir'      => 'required',
            'agama'              => 'required',
            'dokumen_ktp'        => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'dokumen_kk'         => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'dokumen_npwp'       => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'dokumen_buku_nikah' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'dokumen_akta_cerai' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        $tanggalFormatted = \Carbon\Carbon::parse($request->tanggal_lahir)->format('Y-m-d');

        // Daftar konfigurasi folder
        $documentConfig = [
            'dokumen_ktp'         => 'ktp',
            'dokumen_kk'          => 'kk',
            'dokumen_npwp'        => 'npwp',
            'dokumen_buku_nikah'  => 'buku_nikah',
            'dokumen_akta_cerai'  => 'akta_cerai',
        ];

        $uploadedFiles = [];

        DB::beginTransaction();
        try {
            // A. Update data Pegawai
            DB::table('pegawai')->updateOrInsert(
                ['nomor_urut_pegawai' => $nomorUrut],
                ['nama' => $request->nama]
            );

            foreach ($documentConfig as $inputName => $folder) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);

                    // Penamaan file
                    $prefix = strtoupper(str_replace('dokumen_', '', $inputName));
                    $fileName = $prefix . '_' . $nomorUrut . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/' . $folder, $fileName, 'public');

                    if ($path) {
                        $uploadedFiles[$inputName] = $fileName;
                    }
                }
            }

            // C. Siapkan Data Detail
            $dataToUpdate = [
                'tempat_lahir'          => $request->tempat_lahir,
                'tanggal_lahir'         => $tanggalFormatted,
                'jenis_kelamin'         => $request->jenis_kelamin,
                'agama'                 => $request->agama,
                'alamat'                => $request->alamat,
                'email'                 => $request->email,
                'no_telpon'             => $request->no_telpon,
                'nama_ibu'              => $request->nama_ibu,
                'nama_ayah'             => $request->nama_ayah,
                'pendidikan_terakhir'   => $request->pendidikan_terakhir,
                'jurusan'               => $request->jurusan,
                'status_perkawinan'     => $request->status_perkawinan,
            ];

            $dataToUpdate = array_merge($dataToUpdate, $uploadedFiles);

            DB::table('detail_pribadi')->updateOrInsert(
                ['nomor_urut_pegawai' => $nomorUrut],
                $dataToUpdate
            );

            DB::commit();
            return redirect()->back()->with('success', 'Data dan seluruh dokumen berhasil disimpan!');

        } catch (Exception $e) {
            DB::rollBack();
            foreach ($uploadedFiles as $inputName => $fileName) {
                $folder = $documentConfig[$inputName];
                \Storage::disk('public')->delete($folder . '/' . $fileName);
            }
            \Log::error("Error Update Profile: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function updatePhoto(Request $request)
    {
        // 1. Validasi input file
        $request->validate([
            'photo_selfie' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Ambil data detail pribadi milik user yang sedang login
        $user = Auth::user();
        $detailPribadi = $user->detailPribadi;

        if (!$detailPribadi) {
            return back()->with('error', 'Data detail pribadi tidak ditemukan.');
        }

        if ($request->hasFile('photo_selfie')) {
            // 3. Hapus foto lama jika ada di dalam folder storage
            if ($detailPribadi->photo_selfie && Storage::disk('public')->exists($detailPribadi->photo_selfie)) {
                Storage::disk('public')->delete($detailPribadi->photo_selfie);
            }

            // 4. Simpan foto baru ke folder 'uploads/selfie'
            $path = $request->file('photo_selfie')->store('uploads/selfie', 'public');

            // 5. Update kolom photo_selfie di tabel detail_pribadi
            $detailPribadi->update([
                'photo_selfie' => $path
            ]);
        }

        return back()->with('success', 'Foto selfie berhasil diperbarui!');
    }

    public function updateKeluarga(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Sesi Anda habis.');
        }
        $nomorUrut = trim($user->nomor_urut_pegawai);

        // Validasi KHUSUS untuk keluarga
        $request->validate([
            'nomor_urut_pegawai' => 'required|in:' . $nomorUrut,
            'nama_istri'         => 'nullable|array',
            'nama_anak'          => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            KeluargaPegawai::where('nomor_urut_pegawai', $nomorUrut)->delete();

            // Simpan Istri
            if($request->has('nama_istri') && is_array($request->nama_istri)) {
                foreach($request->nama_istri as $nama) {
                    if(!empty(trim($nama))) {
                        KeluargaPegawai::updateOrInsert([
                            'nomor_urut_pegawai' => $nomorUrut, 'nama' => trim($nama), 'hubungan' => 'istri', 'created_at' => Carbon::now()
                        ]);
                    }
                }
            }

            // Simpan Anak
            if($request->has('nama_anak') && is_array($request->nama_anak)) {
                foreach($request->nama_anak as $nama) {
                    if(!empty(trim($nama))) {
                        KeluargaPegawai::updateOrInsert([
                            'nomor_urut_pegawai' => $nomorUrut, 'nama' => trim($nama), 'hubungan' => 'anak', 'created_at' => Carbon::now()
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data keluarga berhasil diperbarui!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error Update Keluarga: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data keluarga: ' . $e->getMessage());
        }
    }

    public function updatePekerjaan(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Sesi Anda habis.');
        }
        $nomorUrut = trim($user->nomor_urut_pegawai);

        // 1. Validasi input hanya untuk kolom yang akan disimpan/diubah
        $request->validate([
            'golongan_pajak' => 'required|string|max:10',
            'no_rekening' => 'required|string|max:30',
        ]);

        // Opsional: Tetap pakai transaksi di sini untuk keamanan
        DB::beginTransaction();
        try {
            // 2. Gunakan updateOrInsert:
            Pekerjaan::updateOrInsert(
                ['nomor_urut_pegawai' => $nomorUrut],
                [
                    'golongan_pajak' => $request->golongan_pajak,
                    'no_rekening' => $request->no_rekening,
                ]
            );

            DB::commit();
            return redirect()->back()->with('success', 'Data pekerjaan berhasil diperbarui!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error Update Pekerjaan: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data pekerjaan: ' . $e->getMessage());
        }
    }

    public function updateReward(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with(key: 'error', value: 'Sesi Anda habis.');
        }

        // 1. Validasi input
        $request->validate(rules: [
            'jenis_reward'          => 'required|array',
            'jenis_reward.*'        => 'required|string|max:255',
            'tanggal_diberikan'     => 'required|array',
            'tanggal_diberikan.*'   => 'required|date',
            'diberikan_oleh'        => 'required|array',
            'diberikan_oleh.*'      => 'required|string|max:255',
            'deskripsi_reward'      => 'nullable|array',
            'deskripsi_reward.*'    => 'nullable|string',
        ]);

        // dd($request->all());

        // Opsional: Tetap pakai transaksi di sini untuk keamanan
        DB::beginTransaction();

        try {
            $nomorUrut = trim(string: $user->nomor_urut_pegawai);

            // Hapus semua data reward lama untuk user ini
            Reward::where('nomor_urut_pegawai', $nomorUrut)->delete();

            // Siapkan data baru untuk dimasukkan
            $dataToInsert = [];
            foreach ($request->input('jenis_reward') as $index => $jenis) {
                $dataToInsert[] = [
                    'nomor_urut_pegawai'    => $nomorUrut,
                    'jenis_reward'          => $jenis,
                    'tanggal_diberikan'     => $request->input('tanggal_diberikan')[$index] ?? null,
                    'diberikan_oleh'        => $request->input('diberikan_oleh')[$index] ?? null,
                    'deskripsi_reward'      => $request->input('deskripsi_reward')[$index] ?? null,
                ];
            }

            // Masukkan semua data baru sekaligus (mass insert)
            if (!empty($dataToInsert)) {
                Reward::insert($dataToInsert);
            }

            DB::commit();
            return redirect()->back()->with(key: 'success', value: 'Data reward berhasil diperbarui!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error(message: "Error Update Reward: " . $e->getMessage());

            return redirect()->back()->withInput()->with(key: 'error', value: 'Gagal menyimpan data reward: ' . $e->getMessage());
        }
    }

    public function updatePunishment(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with(key: 'error', value: 'Sesi Anda habis.');
        }

        // 1. Validasi input
        $request->validate(rules: [
            'jenis_punishment'      => 'required|array',
            'jenis_punishment.*'    => 'required|string|max:255',
            'tanggal_diberikan'     => 'required|array',
            'tanggal_diberikan.*'   => 'required|date',
            'diberikan_oleh'        => 'required|array',
            'diberikan_oleh.*'      => 'required|string|max:255',
            'deskripsi'             => 'nullable|array',
            'deskripsi.*'           => 'nullable|string',
        ]);

        // dd($request->all());

        // Opsional: Tetap pakai transaksi di sini untuk keamanan
        DB::beginTransaction();

        try {
            $nomorUrut = trim(string: $user->nomor_urut_pegawai);

            // Hapus semua data punishment lama untuk user ini
            Punishment::where('nomor_urut_pegawai', $nomorUrut)->delete();

            // Siapkan data baru untuk dimasukkan
            $dataToInsert = [];
            foreach ($request->input('jenis_punishment') as $index => $jenis) {
                $dataToInsert[] = [
                    'nomor_urut_pegawai'    => $nomorUrut,
                    'jenis_punishment'      => $jenis,
                    'tanggal_diberikan'     => $request->input('tanggal_diberikan')[$index] ?? null,
                    'diberikan_oleh'        => $request->input('diberikan_oleh')[$index] ?? null,
                    'deskripsi'             => $request->input('deskripsi')[$index] ?? null,
                ];
            }

            // Masukkan semua data baru sekaligus (mass insert)
            if (!empty($dataToInsert)) {
                Punishment::insert($dataToInsert);
            }

            DB::commit();

            return redirect()->back()->with(key: 'success', value: 'Data Punishment berhasil diperbarui!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error(message: "Error Update Punishment: " . $e->getMessage());

            return redirect()->back()->withInput()->with(key: 'error', value: 'Gagal menyimpan data reward: ' . $e->getMessage());
        }
    }

}
