@extends('layouts.app-skkmr')

@section('content')

<div class="bg-indigo-500 rounded-tl-md shadow-xl pl-4 pr-4 pb-4 pt-2">
    <span class="text-sm font-semibold text-white">#Section {{ $pageTitle }}</span>
    {{-- <div class="px-2 py-2 bg-amber-500 shadow-lg text-center"></div> --}}
    <p class="text-xs text-white">Tinjau informasi pengajuan dengan teliti sebelum memberikan keputusan.</p>
</div>

@php
    // 1. AMBIL DATA TERAKHIR
    $lastLog = $historiLog->last();

    // Ambil status terakhir secara dinamis
    $statusTerakhir = strtolower($lastLog->status_persetujuan ?? $lastLog->status_pengajuan ?? '');

    // 2. DEFINISIKAN VARIABLE CEK
    $isHRODone = ($statusTerakhir === 'disetujui' && $lastLog->tahap_persetujuan === 'HRO');
    $isSelesai = ($lastLog->tahap_persetujuan === 'Selesai');
    $isFailFinal = ($statusTerakhir === 'ditolak');

    // 3. Tentukan Nama Tahap Berikutnya (Next Step)
    $nextStepName = 'SELESAI';

    if ($statusTerakhir === 'diproses') {
        $tahapSekarang = $lastLog->tahap_persetujuan ?? '';

        if ($tahapSekarang === 'Pengajuan Awal') {
            $nextStepName = 'Kepala SKK & SKKMR';
        }
        elseif (str_contains(strtolower($tahapSekarang), 'skk')) {
            $nextStepName = 'Direktur Kepatuhan';
        } else {
            $nextStepName = 'Tahap Selanjutnya';
        }

    } elseif ($statusTerakhir === 'ditolak') {
        $nextStepName = 'PENGAJUAN DIBATALKAN';
    }
@endphp

<!-- Tracking Status Stepper -->
<div class="bg-white border-b border-gray-100 max-w-full py-4 shadow-sm">
    <div class="max-w-4xl mx-auto px-6">
        <div class="relative flex items-start">
            {{-- 1. LOOP HISTORI (Data dari Database) --}}
            @foreach($historiLog as $index => $log)
                @php
                    // --- 1. DEFINISI STATUS (TETAP UTUH, TIDAK ADA YANG DIHAPUS) ---
                    $logStatus = strtolower($log->status_persetujuan ?? $log->status_pengajuan);
                    $isDone = ($logStatus === 'disetujui');
                    $isCurr = ($logStatus === 'diproses');
                    $isFail = ($logStatus === 'ditolak'); // Ini tetap ada untuk warna MERAH
                    $timeCol = 'updated_at';
                    $nextLog = $historiLog[$index + 1] ?? null;
                    $nextStatus = $nextLog ? strtolower($nextLog->status_persetujuan ?? $nextLog->status_pengajuan) : null;

                    if ($isDone) {
                        $lineColor = ($nextStatus === 'disetujui' || $nextStatus === 'diproses' || $nextStatus === 'ditolak')
                                    ? 'bg-emerald-500' : 'bg-gray-200';
                    } elseif ($isCurr) {
                        $lineColor = 'bg-orange-500';
                    } elseif ($isFail) {
                        $lineColor = 'bg-red-500';
                    } else {
                        $lineColor = 'bg-gray-200';
                    }
                @endphp

                <div class="flex flex-col items-center flex-1 relative">
                    @if(!$loop->last)
                        <div class="absolute top-5 left-1/2 w-full h-[2.5px] z-0 {{ $lineColor }}"></div>
                    @else
                        <div class="absolute top-5 left-1/2 w-full h-[2.5px] z-0 {{ $isHRODone ? 'bg-emerald-500' : ($isFailFinal ? 'bg-red-500' : ($isCurr ? 'bg-orange-500' : 'bg-gray-100')) }}"></div>
                    @endif

                    {{-- Bulatan Utama --}}
                    <div class="relative flex items-center justify-center z-10">
                        @if($isCurr)
                            <span class="absolute inline-flex h-9 w-9 rounded-full bg-orange-400 opacity-20 animate-ping"></span>
                        @endif

                        <div class="w-10 h-10 rounded-full {{ $isDone ? 'bg-emerald-500 border-emerald-100' : ($isFail ? 'bg-red-500 border-red-100' : 'bg-orange-500 border-orange-100') }} border-[5px] flex items-center justify-center shadow-sm transition-all duration-300">
                            @if($isDone)
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @elseif($isFail)
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            @else
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            @endif
                        </div>
                    </div>

                    {{-- Label Info --}}
                    <div class="mt-4 text-center px-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tahap {{ $index + 1 }}</p>
                        <p class="text-[11px] font-semibold text-gray-900 mt-1 leading-tight">{{ $log->tahap_persetujuan == 'Pengajuan Awal' ? 'Pengajuan' : $log->tahap_persetujuan }}</p>
                        <p class="text-[9px] font-bold {{ $isDone ? 'text-emerald-600' : ($isFail ? 'text-red-600' : 'text-orange-600') }} mt-1 italic uppercase">{{ $logStatus }}</p>
                        @if($log->$timeCol)
                            <p class="text-[8px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($log->$timeCol)->format('d/m H:i') }} WIB</p>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- 2. TITIK ATASAN OTOMATIS (Next Step) - Muncul jika baru Pengajuan Awal --}}
            @if(count($historiLog) == 1 && $statusTerakhir !== 'ditolak')
                <div class="flex flex-col items-center flex-1 relative">
                    <div class="absolute top-5 left-1/2 w-full h-[2.5px] z-0 bg-gray-100"></div>
                    {{-- Bulatan Manager (WAJIB ORANGE & EFEK PING) --}}
                    <div class="relative flex items-center justify-center z-10">
                        {{-- Efek Radar Orange --}}
                        <span class="absolute inline-flex h-9 w-9 rounded-full bg-orange-400 opacity-20 animate-ping"></span>
                        {{-- Bulatan Utama Orange --}}
                        <div class="w-10 h-10 rounded-full bg-orange-500 border-[5px] border-orange-100 flex items-center justify-center shadow-sm transition-all duration-300">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                        </div>
                    </div>
                    <div class="mt-4 text-center px-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tahap 2</p>
                        <p class="text-[11px] font-semibold text-gray-900 mt-1 leading-tight">{{ $tahapTeks }}</p>
                        {{-- STATUS TETAP DIPROSES ORANGE --}}
                        <p class="text-[9px] font-bold text-orange-600 mt-1 italic uppercase">DIPROSES</p>
                    </div>
                </div>
            @endif

            {{-- 3. TITIK AKHIR (Selesai) --}}
            <div class="flex flex-col items-center flex-1 relative">
                {{-- Bulatan Akhir: Hijau jika HRO Done, Merah jika ada yang Ditolak, Abu-abu jika masih proses --}}
                <div class="w-10 h-10 rounded-full
                    {{ $isHRODone ? 'bg-emerald-500 border-emerald-100' : ($isFailFinal ? 'bg-red-500 border-red-100' : 'bg-gray-100') }} border-[5px] flex items-center justify-center z-10 shadow-sm transition-all duration-300">
                    @if($isHRODone)
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    @elseif($isFailFinal)
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    @else
                        <div class="w-2 h-2 bg-white rounded-full"></div>
                    @endif
                </div>
                <div class="mt-4 text-center {{ ($isHRODone || $isFailFinal) ? '' : 'opacity-40' }}">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Akhir</p>
                    <p class="text-[11px] font-semibold {{ $isFailFinal ? 'text-red-600' : ($isHRODone ? 'text-emerald-600' : 'text-gray-400') }} mt-1 leading-tight">
                        {{ $isFailFinal ? 'BERHENTI' : 'Selesai' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Container: Kita tambah tingginya ke h-[580px] agar pas untuk textarea 5 baris + 2 tombol di kanan -->
<div class="max-w-full flex flex-col h-[450px] overflow-hidden bg-white border border-slate-200 shadow-xl">
    <div class="grid grid-cols-1 lg:grid-cols-3 h-full divide-x divide-slate-100">

        <!-- Kolom Kiri: Informasi (Clean White/Gray Theme) -->
        <div class="lg:col-span-2 flex flex-col min-h-0 overflow-y-auto p-4 bg-slate-50/30">
            <!-- Header -->
            <div class="mb-4 flex justify-between items-start">
                <div>
                    <span class="text-xl font-bold text-slate-800">{{ $data->nama }}</span>
                    <p class="text-xs text-slate-500 font-mono tracking-wider">{{ $data->nomor_urut_pegawai }} | {{ $data->nama_divisi }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter {{ $sumber == 'cuti' ? 'bg-purple-100 text-purple-600 border border-purple-200' : 'bg-cyan-100 text-cyan-600 border border-cyan-200' }}">
                    {{ $sumber }}
                </span>
            </div>

            <!-- Grid Detail -->
            <div class="flex flex-wrap items-start gap-x-10 gap-y-4 mb-6">
                {{-- 1. JABATAN --}}
                <div class="min-w-[120px]">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jabatan</label>
                    <p class="text-sm text-slate-700 font-semibold">{{ $data->jabatan }}</p>
                </div>

                {{-- 🔒 DATA KHUSUS PENSIUN & PANGKAT --}}
                @if(in_array($sumber, ['pensiun', 'pangkatgajitunjangan', 'pangkat']))
                    <div class="min-w-[100px]">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Masa Kerja</label>
                        <p class="text-sm text-slate-700 font-semibold">{{ $data->masa_kerja ? preg_replace('/(?<=\d)(?=[a-z])|(?<=[a-z])(?=\d)/i', ' ', $data->masa_kerja) : '-' }}</p>
                    </div>
                    <div class="min-w-[120px]">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">TMT Pegawai</label>
                        <p class="text-sm text-slate-700 font-semibold">
                            {{ $data->tmt_pegawai ? \Carbon\Carbon::parse($data->tmt_pegawai)->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                @endif

                {{-- 2. JENIS PENGAJUAN --}}
                <div class="min-w-[120px]">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jenis Pengajuan</label>
                    <p class="text-sm text-indigo-600 font-bold italic">
                        @if($sumber == 'lembur')
                            Lembur Kerja
                        @else
                            {{ $data->jenis_pengajuan ?? ucfirst($sumber) }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative">
                <div class="absolute left-0 top-4 bottom-4 w-1.5 bg-indigo-500 rounded-r-full shadow-[2px_0_8px_rgba(99,102,241,0.4)]"></div>

                @if($sumber == 'pensiun')
                    <!-- Blok Detail Pensiun -->
                    <div class="bg-orange-50/50 rounded-md border border-orange-100 space-y-5">
                        <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-x-auto">
                            <table class="w-full min-w-[500px] text-sm text-left text-slate-700">
                                <thead class="text-[10px] uppercase text-slate-400 bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 font-bold tracking-widest">Dokumen Persyaratan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($files ?? [] as $file)
                                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                                            <td class="px-6 py-4 font-medium text-slate-700 whitespace-nowrap">
                                                {{ str_replace('_', ' ', $file->tipe_dokumen) }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button type="button"
                                                    onclick="openPdfModal('{{ route('skkmr.lihatDokumen', $file->id) }}', '{{ str_replace('_', ' ', $file->tipe_dokumen) }}')"
                                                    class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg transition shadow-sm inline-block">
                                                    Lihat Dokumen
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-6 py-5 text-center text-slate-400 italic">
                                                Tidak ada file dokumen yang diunggah.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                @elseif($sumber == 'lembur')
                    <!-- Blok Deskripsi & Detail Waktu Lembur -->
                    <div class="bg-blue-50/50 rounded-2xl border border-blue-100">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-4 py-3">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Lembur</span>
                                <span class="text-sm font-semibold text-slate-700 mt-1">{{ \Carbon\Carbon::parse($data->tanggal_lembur)->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Jam Mulai</span>
                                <span class="text-sm font-semibold text-emerald-600">{{ $data->jam_mulai ? date('H:i', strtotime($data->jam_mulai)) : '-' }} WIB</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Jam Selesai</span>
                                <span class="text-sm font-semibold text-rose-600 mt-1">{{ $data->jam_selesai ? date('H:i', strtotime($data->jam_selesai)) : '-' }} WIB</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-blue-500 uppercase tracking-wider">Total Lembur</span>
                                <span class="text-sm font-bold text-blue-700 mt-1">{{ $data->total_jam_lembur ?? '0' }}</span>
                            </div>
                        </div>
                        <div class="border-t border-blue-100/50 px-4 py-3">
                            <label class="block text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-2">Deskripsi Pengajuan Lembur</label>
                            <p class="text-sm text-slate-700 leading-relaxed italic">"{{ $data->uraian_tugas ?? 'Tidak ada deskripsi' }}"</p>
                        </div>
                    </div>

                @elseif($sumber == 'pangkatgajitunjangan' || $sumber == 'pangkat')
                    <!-- Blok Detail Pangkat/Gaji -->
                    <div class="ml-4 space-y-4">

                        <!-- 🟢 BLOK DOKUMEN PANGKAT/GAJI/TUNJANGAN -->
                        <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-x-auto">
                            <table class="w-full min-width-[500px] text-sm text-left text-slate-700">
                                <thead class="text-[10px] uppercase text-slate-400 bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 font-bold tracking-widest">Dokumen Persyaratan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($files ?? [] as $file)
                                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                                            <td class="px-6 py-4 font-medium text-slate-700 whitespace-nowrap">
                                                {{ str_replace('_', ' ', $file->tipe_dokumen) }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <button type="button"
                                                        onclick="openPdfModal('{{ route('skkmr.lihatDokumen', $file->id) }}', '{{ str_replace('_', ' ', $file->tipe_dokumen) }}')"
                                                        class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg transition shadow-sm inline-block">
                                                    Lihat Dokumen
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-6 py-5 text-center text-slate-400 italic">
                                                Tidak ada file dokumen yang diunggah.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- KOLOM KANAN: Action (Scroll Mandiri) -->
        <div class="lg:col-span-1 flex flex-col bg-slate-50 overflow-hidden border-l border-slate-100 h-full">

            <!-- 1. TITLE KEPUTUSAN -->
            <div class="p-5 bg-white border-b border-slate-200 flex-none shadow-sm">
                <h3 class="text-[12px] font-bold text-indigo-600 uppercase tracking-[0.2em]">
                    Keputusan {{ str_replace('Verifikasi ', '', $tahapTeks) }}
                </h3>
            </div>

            <!-- 2. AREA FORM (Scroll Mandiri) -->
            <div class="flex-1 overflow-y-auto custom-scroll-container p-3 pb-5">
                <form id="formApproval" action="{{ route('skkmr.updateStatus', ['sumber' => $sumber, 'id_log' => $id_log]) }}" method="POST" class="flex flex-col">
                    @csrf
                    @method('PUT')

                    {{-- LOGIKA KHUSUS LEMBUR (Hanya Tampilan Info) --}}
                    @if($sumber === 'lembur')
                        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest">Informasi Waktu Lembur</label>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <span class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">Jam Mulai</span>
                                    <input type="time" value="{{ isset($data->jam_mulai) ? date('H:i', strtotime($data->jam_mulai)) : '00:00' }}"
                                        readonly
                                        class="w-full p-3 text-sm border border-slate-100 rounded-xl bg-slate-50 font-medium text-slate-500 outline-none cursor-default">
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">Jam Selesai</span>
                                    <input type="time" value="{{ isset($data->jam_selesai) ? date('H:i', strtotime($data->jam_selesai)) : '00:00' }}"
                                        readonly
                                        class="w-full p-3 text-sm border border-slate-100 rounded-xl bg-slate-50 font-medium text-slate-500 outline-none cursor-default">
                                </div>
                            </div>

                            <div class="pt-2">
                                <span class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">Total Durasi</span>
                                <input type="text" value="{{ $data->total_jam_lembur ?? '0 jam 0 menit' }}"
                                    readonly
                                    class="w-full mt-2 p-4 text-sm bg-slate-50 border border-slate-100 rounded-xl font-bold text-slate-400 outline-none">
                            </div>
                        </div>
                    @endif

                    {{-- KOTAK CATATAN (Muncul untuk semua jenis pengajuan) --}}
                    <div class="flex flex-col mt-3">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Catatan Persetujuan</label>
                        <textarea name="catatan" rows="4"
                            class="w-full p-5 text-sm bg-white border border-slate-200 rounded-3xl text-slate-700 focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition resize-none placeholder-slate-400 shadow-sm"
                            placeholder="Wajib Berikan alasan atau instruksi tambahan..."
                            @if($data->status !== 'diproses') readonly @endif>{{ $data->komentar ?? '' }}</textarea>
                    </div>
                    <input type="hidden" name="status" id="status_input">

                    <!-- 3. ACTION BUTTONS -->
                    <div class="grid grid-cols-1 gap-3 pt-2">
                        <button type="button" onclick="handleApproval('disetujui')"
                            @disabled($data->status !== 'diproses' && $data->status !== null)
                            class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-200 disabled:text-slate-400 text-white text-xs font-semibold rounded-xl transition-all shadow-lg shadow-indigo-200 uppercase tracking-widest">
                            Setujui Pengajuan
                        </button>

                        <button type="button" onclick="handleApproval('ditolak')"
                            @disabled($data->status !== 'diproses' && $data->status !== null)
                            class="w-full py-4 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 disabled:border-slate-100 disabled:text-slate-300 text-xs font-semibold rounded-xl transition-all shadow-sm uppercase tracking-widest">
                            Tolak Pengajuan
                        </button>
                    </div>
                </form>
                <!-- Modal Tampilan Dokumen -->
                <div id="pdfModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col">
                        <!-- Header Modal -->
                        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200">
                            <h3 id="modalTitle" class="text-sm font-bold text-slate-800 uppercase tracking-wider">Lihat Dokumen</h3>
                            <button onclick="closePdfModal()" class="text-slate-400 hover:text-slate-600 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <!-- Body Modal -->
                        <div class="flex-1 p-4 bg-slate-100">
                            <iframe id="pdfIframe" src="" class="w-full h-full rounded-lg border border-slate-200" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    const jamMulai = document.getElementById('input_jam_mulai');
    const jamSelesai = document.getElementById('input_jam_selesai');
    const totalJam = document.getElementById('input_total_jam');
    const btnEdit = document.getElementById('btnEditTime');

    // 1. Logika Toggle Edit
    function toggleEditTime() {
        const isLocked = jamMulai.disabled;

        if (isLocked) {
            jamMulai.disabled = false;
            jamSelesai.disabled = false;
            [jamMulai, jamSelesai].forEach(el => {
                el.classList.remove('bg-slate-50', 'text-slate-500');
                el.classList.add('bg-white', 'text-slate-700');
            });
            totalJam.classList.add('text-indigo-600', 'bg-indigo-50/50', 'border-indigo-100');
            btnEdit.innerText = 'Batal';
            btnEdit.classList.replace('bg-indigo-50', 'bg-rose-50');
            btnEdit.classList.replace('text-indigo-600', 'text-rose-600');
        } else {
            jamMulai.disabled = true;
            jamSelesai.disabled = true;
            [jamMulai, jamSelesai].forEach(el => {
                el.classList.add('bg-slate-50', 'text-slate-500');
                el.classList.remove('bg-white', 'text-slate-700');
            });
            totalJam.classList.remove('text-indigo-600', 'bg-indigo-50/50', 'border-indigo-100');
            btnEdit.innerText = 'Ubah';
            btnEdit.classList.replace('bg-rose-50', 'bg-indigo-50');
            btnEdit.classList.replace('text-rose-600', 'text-indigo-600');
        }
    }

    // 2. Logika Hitung
    function calculateLembur() {
        if (!jamMulai.value || !jamSelesai.value) return;
        const [hStart, mStart] = jamMulai.value.split(':').map(Number);
        const [hEnd, mEnd] = jamSelesai.value.split(':').map(Number);
        let totalMinutesStart = (hStart * 60) + mStart;
        let totalMinutesEnd = (hEnd * 60) + mEnd;
        if (totalMinutesEnd < totalMinutesStart) totalMinutesEnd += 24 * 60;
        const diffMinutes = totalMinutesEnd - totalMinutesStart;
        const hours = Math.floor(diffMinutes / 60);
        const minutes = diffMinutes % 60;
        totalJam.value = hours + " jam " + minutes + " menit";
    }

    if(jamMulai && jamSelesai) {
        jamMulai.addEventListener('input', calculateLembur);
        jamSelesai.addEventListener('input', calculateLembur);
        calculateLembur();
    }
</script>

<!-- Script Modal -->
<script>
    function openPdfModal(url, docName) {
        const modal = document.getElementById('pdfModal');
        const iframe = document.getElementById('pdfIframe');
        const title = document.getElementById('modalTitle');

        if (modal && iframe) {
            // Set Judul & URL
            title.innerText = docName ? docName.toUpperCase() : "LIHAT DOKUMEN";
            iframe.src = url;

            // Tampilkan Modal
            modal.classList.remove('hidden');
            modal.classList.add('flex'); // Pakai flex supaya items-center & justify-center jalan
        }
    }

    function closePdfModal() {
        const modal = document.getElementById('pdfModal');
        const iframe = document.getElementById('pdfIframe');

        if (modal && iframe) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            iframe.src = ''; // Penting: Kosongkan iframe agar tidak berat & tidak bocor suara/data
        }
    }
</script>


@endsection
