<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Carbon\Carbon; // Pastikan Carbon diimpor

class SubmissionProcessorService
{
    public function processSubmissions(Collection $submissions): Collection
    {
        return $submissions->map(function ($submission) {
            // --- 1. Tentukan Role Secara Dinamis (Tanpa level_akses) ---
            $user = auth()->user();
            $roleMapping = \DB::table('roles_mapping')
                ->where('jabatan_id', $user->jabatan_id)
                ->where('level_id', $user->level_id)
                ->first();

            // Cek apakah user adalah manager berdasarkan route_name di database
            $isManager = $roleMapping && str_contains($roleMapping->route_name, 'manager');
            $type = $submission['type'];

            // --- 2. Tentukan Alur (Flow) ---

            // DEFAULT FLOW (5 Tahap: Digunakan oleh Pegawai untuk Pensiun & PangkatGajiTunjangan)
            $flow = ['Pengajuan Awal', 'Kepala SKK & MR', 'Direktur Kepatuhan', 'Direktur Utama', 'HRO'];

            if ($isManager) {
                // LOGIKA KHUSUS USER MANAGER
                if ($type === 'PangkatGajiTunjangan') {
                    // Alur Manager Pangkat/Gaji (Tanpa Kepala SKK & MR)
                    $flow = ['Pengajuan Awal', 'Direktur Kepatuhan', 'Direktur Utama', 'HRO'];
                } elseif ($type === 'Pensiun') {
                    // Alur Pensiun Manager disamakan dengan pegawai (5 tahap)
                    $flow = ['Pengajuan Awal', 'Kepala SKK & MR', 'Direktur Kepatuhan', 'Direktur Utama', 'HRO'];
                } elseif ($type === 'Cuti') {
                    // Cuti Manager: Pengajuan -> Dir. Operasional -> HRO
                    $flow = ['Pengajuan Awal', 'Direktur Operasional', 'HRO'];
                } else {
                    // Alur Umum Manager (Termasuk Lembur Manager): Pengajuan -> Dir. Operasional -> Dir. Utama -> HRO
                    $flow = ['Pengajuan Awal', 'Direktur Operasional', 'Direktur Utama', 'HRO'];
                }
            } else {
                // LOGIKA KHUSUS USER PEGAWAI (Cuti & Lembur)
                if (in_array($type, ['Cuti', 'Lembur'])) {
                    $flow = ['Pengajuan Awal', 'Manager', 'Direktur Operasional', 'HRO'];

                    // Penyesuaian Label khusus Lembur Pegawai: Ganti Dir. Operasional menjadi Kepala SKK MR
                    if ($type === 'Lembur') {
                        $flow = array_map(fn($s) => ($s == 'Direktur Operasional') ? 'Kepala SKK MR' : $s, $flow);
                    }
                }
            }

            // --- 2. Ambil Logs ---
            $logs = collect($submission['logs'] ?? []);
            $logsByStage = $logs->keyBy('tahap_persetujuan');
            $processStopped = false;

            // --- 3. Generate stageData (Untuk Progress Bar) ---
            $stageData = [];
            foreach ($flow as $stageName) {
                $log = $logsByStage->get($stageName);
                $status = 'menunggu';
                $isCurrent = false;

                if ($log) {
                    // Normalisasi status ke lowercase untuk pengecekan
                    $rawStatus = strtolower($log['status_persetujuan'] ?? $log['status_pengajuan'] ?? 'diproses');
                    $status = $rawStatus;

                    // Gunakan logika status yang sinkron dengan database
                    if ($status == 'ditolak') {
                        $processStopped = true;
                    } elseif ($status == 'diproses') {
                        // isCurrent true jika ini log terakhir yang masuk
                        $isCurrent = true;
                    }
                }

                // Mapping Status Text menggunakan logika yang lebih rapi
                $statusText = match(true) {
                    $status == 'ditolak' => 'Ditolak',
                    $status == 'disetujui' => 'Disetujui',
                    $isCurrent => 'Diproses',
                    $processStopped => '-', // Jika sudah ada yang ditolak sebelumnya, tahap berikutnya strip
                    default => 'Menunggu'
                };

                $statusBadge = match($statusText) {
                    'Ditolak' => 'bg-red-100 text-red-800',
                    'Disetujui' => 'bg-teal-100 text-teal-800',
                    'Diproses' => 'bg-orange-100 text-orange-800',
                    default => 'bg-gray-100 text-gray-600'
                };

                $stageData[] = [
                    'stageName' => $stageName,
                    'statusString' => $status,
                    'statusText' => $statusText,
                    'statusBadge' => $statusBadge,
                    // Cek kolom 'update_at' atau 'updated_at' (sesuaikan dengan tabel log Anda)
                    'updatedAt' => isset($log['updated_at'])
                        ? Carbon::parse($log['updated_at'])->format('d/m/Y H:i')
                        : (isset($log['update_at']) ? Carbon::parse($log['update_at'])->format('d/m/Y H:i') : null),
                    'isCurrent' => $isCurrent
                ];
            }

            // --- 1. Logika Warna Garis (DIPERBAIKI) ---
            foreach ($stageData as $index => &$currentStage) {
                if (isset($stageData[$index + 1])) {
                    $nextStage = $stageData[$index + 1];

                    // Garis hijau jika tahap saat ini sudah disetujui
                    if ($currentStage['statusString'] == 'disetujui') {
                        $currentStage['lineColor'] = 'bg-teal-500';
                    }
                    // Garis merah jika tahap BERIKUTNYA ditolak (menunjukkan aliran berhenti di sana)
                    elseif ($nextStage['statusString'] == 'ditolak') {
                        $currentStage['lineColor'] = 'bg-red-500';
                    }
                    else {
                        $currentStage['lineColor'] = 'bg-gray-200';
                    }
                } else {
                    $currentStage['lineColor'] = 'bg-transparent';
                }
            }
            $submission['stageData'] = $stageData;

            // --- 2. DATA TRANSFORMER (SECTION 2 - DETAIL) ---
            $type = $submission['type'];
            $display = [];

            // Kolom 1: Jenis
            $display['jenis_label'] = "Jenis Pengajuan";
            $display['jenis_val'] = match($type) {
                'Cuti' => data_get($submission, 'pengajuancuti.jenisCuti.nama_cuti') ?? data_get($submission, 'pengajuancuti.jenis_cuti') ?? 'Cuti',
                'Lembur' => 'Lembur',
                'Pensiun' => 'Pensiun',
                'PangkatGajiTunjangan' => 'Kenaikan Pangkat/Gaji/Tunjangan',
                default => $type ?? 'N/A'
            };

            $isTMT = in_array($type, ['Pensiun', 'PangkatGajiTunjangan']);
            $currentType = strtolower($type);

            // A. Riwayat Pengajuan (Created At)
            $rawCreated = data_get($submission, 'created_at') ?? ($submission['created_at'] ?? null);
            $display['tgl_pengajuan_val'] = $rawCreated ? Carbon::parse($rawCreated)->locale('id')->translatedFormat('d F Y') : 'N/A';

            // B. Kolom Kiri: Label & Tanggal Mulai
            if ($currentType == 'cuti') {
                $display['tgl_mulai_label'] = 'Tanggal Mulai';
                $rawDetailDate = data_get($submission, 'tanggal_mulai') ?? data_get($submission, 'pengajuancuti.tanggal_mulai');
                $dateFormat = 'l, d F Y';
            } elseif ($currentType == 'lembur') {
                $display['tgl_mulai_label'] = 'Tanggal Lembur';
                $rawDetailDate = data_get($submission, 'tanggal_lembur') ?? data_get($submission, 'pengajuanlembur.tanggal_lembur');
                $dateFormat = 'l, d F Y';
            } else {
                $display['tgl_mulai_label'] = 'TMT Pegawai';
                $rawDetailDate = data_get($submission, 'tmt_pegawai') ?? ($submission['tmt_pegawai'] ?? null);
                $dateFormat = 'd F Y';
            }

            $display['tgl_mulai_val'] = ($rawDetailDate && $rawDetailDate != 'N/A')
                ? Carbon::parse($rawDetailDate)->locale('id')->translatedFormat($dateFormat)
                : 'N/A';

            // C. Kolom Tengah: Tanggal Selesai / Waktu
            $display['tgl_selesai_label'] = match($type) {
                'Lembur' => 'Waktu Lembur',
                'Pensiun' => 'TMT Pensiun',
                'PangkatGajiTunjangan' => 'Berkas Pendukung',
                default => 'Tanggal Selesai'
            };

            if ($type == 'Lembur') {
                $display['tgl_selesai_val'] = (data_get($submission, 'jam_mulai') ?? 'N/A') . ' - ' . (data_get($submission, 'jam_selesai') ?? 'N/A');
            } else {
                $rawTglSelesai = ($type == 'Pensiun')
                    ? data_get($submission, 'tmt_pensiun')
                    : (data_get($submission, 'tanggal_selesai') ?? data_get($submission, 'pengajuancuti.tanggal_selesai'));

                if ($rawTglSelesai && $rawTglSelesai != 'N/A') {
                    $formatSelesai = ($type == 'Cuti') ? 'l, d F Y' : 'd F Y';
                    $display['tgl_selesai_val'] = Carbon::parse($rawTglSelesai)->locale('id')->translatedFormat($formatSelesai);
                } else {
                    $display['tgl_selesai_val'] = ($type == 'PangkatGajiTunjangan') ? 'Dokumen Terunggah' : 'N/A';
                }
            }

            // D. Kolom Kanan: Total / Masa Kerja
            $display['total_label'] = match(true) {
                $isTMT => 'Masa Kerja',
                $type == 'Lembur' => 'Durasi Lembur',
                default => 'Total Hari'
            };

            $display['total_val'] = match($type) {
                'Lembur' => data_get($submission, 'total_jam_lembur', 'N/A'),
                'Cuti' => (data_get($submission, 'jumlah_cuti') ?? data_get($submission, 'pengajuancuti.jumlah_cuti', '0')) . ' Hari',
                'Pensiun', 'PangkatGajiTunjangan' => data_get($submission, 'masa_kerja', 'N/A'),
                default => 'N/A'
            };

            // Gabungkan ke submission agar bisa diakses di Blade
            $submission['display'] = $display;

            // --- 4. Kolom Tambahan (Alasan/Sisa Cuti) ---
            $display['alasan_label'] = "Alasan $type";
            $display['alasan_val'] = match($type) {
                'Lembur' => data_get($submission, 'uraian_tugas') ?? data_get($submission, 'pengajuanlembur.uraian_tugas'),
                'Cuti'   => data_get($submission, 'keterangan') ?? data_get($submission, 'pengajuancuti.keterangan'),
                default  => data_get($submission, 'keterangan') // Fallback untuk Pensiun/Pangkat jika ada kolom keterangan
            };

            $display['sisa_cuti'] = ($type === 'Cuti')
                ? (data_get($submission, 'sisa_cuti') ?? data_get($submission, 'pengajuancuti.sisa_cuti', '0')) . ' hari'
                : null;

            // Menghubungkan ke view Blade
            $submission['display_info'] = $display;

            // --- 5. Logika Route & Status Utama (DINAMIS - TANPA LEVEL_AKSES) ---
            $user = auth()->user();

            // Cek role via roles_mapping (Konsisten dengan fungsi sebelumnya)
            $roleMapping = \DB::table('roles_mapping')
                ->where('jabatan_id', $user->jabatan_id)
                ->where('level_id', $user->level_id)
                ->first();

            $isManager = $roleMapping && str_contains($roleMapping->route_name, 'manager');

            // Pemetaan rute lacak (Sesuai dengan nama rute di web.php Anda)
            $routeMap = [
                'Cuti'                 => 'datapengajuan.statuscuti',
                'Lembur'               => 'datapengajuan.statuslembur',
                'Pensiun'              => 'datapengajuan.statuspensiun',
                'PangkatGajiTunjangan' => 'datapengajuan.statuspangkatgajitunjangan',
            ];

            $submission['blade_route'] = $routeMap[$type] ?? null;

            // Ambil log terakhir berdasarkan urutan flow yang sudah ditentukan sebelumnya
            $latestLogForStatus = $logs->sortByDesc(fn($log) => array_search($log['tahap_persetujuan'], $flow))->first();

            $rawStatusUtama = strtolower($latestLogForStatus['status_persetujuan'] ?? $latestLogForStatus['status_pengajuan'] ?? 'diproses');

            $submission['blade_status_text'] = ucfirst($rawStatusUtama);
            $submission['blade_stage']       = $latestLogForStatus['tahap_persetujuan'] ?? 'Belum Diproses';

            $submission['blade_class'] = match($rawStatusUtama) {
                'disetujui' => 'bg-teal-100 text-teal-800',
                'ditolak'   => 'bg-red-100 text-red-800',
                default     => 'bg-yellow-100 text-yellow-800', // Untuk status 'diproses' atau 'menunggu'
            };

            return $submission;
        });

    }

}
