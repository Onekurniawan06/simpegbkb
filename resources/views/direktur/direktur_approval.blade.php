@extends('layouts.app-direktur')

@section('content')

<div class="bg-indigo-500 rounded-tl-md shadow-xl pl-4 pr-4 pb-4 pt-2">
    <span class="text-sm font-semibold text-white">#Section {{ $pageTitle }}</span>
    {{-- <div class="px-2 py-2 bg-amber-500 shadow-lg text-center"></div> --}}
    <p class="text-xs text-white">Tinjau informasi pengajuan dengan teliti sebelum memberikan keputusan.</p>
</div>

@php
    // 1. Ambil data log paling baru
    $lastLog = $historiLog->last();

    // Sesuaikan pengambilan status karena nama kolom berbeda di tiap tabel log
    $statusTerakhir = strtolower($lastLog->status_persetujuan ?? $lastLog->status_pengajuan ?? '');

    // 2. Definisi Variable Cek untuk Direktur
    // Apakah pengajuan ini sudah selesai (final)?
    $isFinalStatus = ($statusTerakhir === 'disetujui' && $lastLog->tahap_persetujuan === 'Selesai');
    $isRejected = ($statusTerakhir === 'ditolak');

    // 3. Tentukan Nama Tahap Berikutnya (Next Step)
    $nextStepName = 'SELESAI';

    if ($statusTerakhir === 'diproses') {
        // Jika masih diproses dan yang sedang login adalah Direktur Operasional,
        // Apakah perlu lanjut ke Direktur Utama? (Sesuaikan dengan kebijakan kantor)
        if (str_contains(strtolower($tahapTeks), 'operasional')) {
            $nextStepName = 'Direktur Utama (Jika Diperlukan)';
        } else {
            $nextStepName = 'Selesai / Pengarsipan';
        }
    } elseif ($statusTerakhir === 'ditolak') {
        $nextStepName = 'PENGAJUAN DIBATALKAN';
    }
@endphp

<!-- Tracking Status Stepper -->
<div class="bg-white border-b border-gray-100 max-w-full py-4 shadow-sm">
    <div class="max-w-4xl mx-auto px-6">
        <div class="relative flex items-start">

            {{-- LOOP HISTORI (Otomatis dari Database) --}}
            @foreach($historiLog as $index => $log)
                @php
                    // 1. Ambil Status (Aman dari NULL)
                    $statusAsli = $log->status_persetujuan ?? ($log->status_pengajuan ?? 'diproses');
                    $logStatus = strtolower((string)$statusAsli);

                    $isDone = ($logStatus === 'disetujui');
                    $isCurr = ($logStatus === 'diproses');
                    $isFail = ($logStatus === 'ditolak');

                    // 2. Deteksi Kolom Waktu
                    $timeCol = isset($log->updated_at) ? 'updated_at' : (isset($log->created_at) ? 'created_at' : 'id');

                    // 3. Logika Warna Garis
                    $nextLog = $historiLog[$index + 1] ?? null;
                    $nextStatus = $nextLog ? strtolower((string)($nextLog->status_persetujuan ?? ($nextLog->status_pengajuan ?? ''))) : null;

                    if ($isDone) {
                        $lineColor = ($nextStatus) ? 'bg-emerald-500' : 'bg-gray-200';
                    } elseif ($isCurr) {
                        $lineColor = 'bg-orange-500';
                    } elseif ($isFail) {
                        $lineColor = 'bg-red-500';
                    } else {
                        $lineColor = 'bg-gray-200';
                    }
                @endphp

                <div class="flex flex-col items-center flex-1 relative">
                    {{-- Garis Penghubung --}}
                    @if(!$loop->last)
                        <div class="absolute top-5 left-1/2 w-full h-[2px] z-0 {{ $lineColor }}"></div>
                    @endif

                    {{-- Bulatan Status --}}
                    <div class="relative flex items-center justify-center z-10 bg-white rounded-full">
                        @if($isCurr)
                            <span class="absolute inline-flex h-9 w-9 rounded-full bg-orange-400 opacity-20 animate-ping"></span>
                        @endif

                        <div class="w-10 h-10 rounded-full {{ $isDone ? 'bg-emerald-500 border-emerald-100' : ($isFail ? 'bg-red-500 border-red-100' : 'bg-orange-500 border-orange-100') }} border-[5px] flex items-center justify-center shadow-sm transition-all duration-300">
                            @if($isDone)
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @elseif($isFail)
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            @else
                                <div class="w-2 h-2 bg-white rounded-full text-white"></div>
                            @endif
                        </div>
                    </div>

                    {{-- Label Informasi --}}
                    <div class="mt-4 text-center px-2">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Tahap {{ $index + 1 }}</p>
                        <p class="text-[11px] font-bold text-slate-800 mt-1 leading-tight whitespace-normal">
                            {{ $log->tahap_persetujuan == 'Pengajuan Awal' ? 'Pengajuan' : $log->tahap_persetujuan }}
                        </p>
                        <p class="text-[9px] font-black {{ $isDone ? 'text-emerald-600' : ($isFail ? 'text-red-600' : 'text-orange-600') }} mt-1 italic uppercase tracking-tighter">
                            {{ $logStatus }}
                        </p>

                        @if(isset($log->$timeCol) && $log->$timeCol)
                            <p class="text-[8px] text-gray-400 mt-1 font-medium">{{ \Carbon\Carbon::parse($log->$timeCol)->format('d/m H:i') }} WIB</p>
                        @endif
                    </div>
                </div>
            @endforeach

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
                    {{-- p.nomor_urut_pegawai dipastikan terbaca dari join pegawai --}}
                    <p class="text-xs text-slate-500 font-mono tracking-wider">{{ $data->nomor_urut_pegawai }} | {{ $data->nama_divisi }}</p>
                </div>

                {{-- Badge Warna Dinamis: Purple (Cuti), Cyan (Lembur), Orange (Pensiun/Pangkat) --}}
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter
                    @if($sumber == 'cuti') bg-purple-100 text-purple-600 border border-purple-200
                    @elseif($sumber == 'lembur') bg-cyan-100 text-cyan-600 border border-cyan-200
                    @else bg-orange-100 text-orange-600 border border-orange-200 @endif">
                    {{ strtoupper($sumber) }}
                </span>
            </div>

            <!-- Grid Detail -->
            <div class="grid grid-cols-2 gap-8 mb-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jabatan</label>
                    <p class="text-sm text-slate-700 font-medium">{{ $data->jabatan }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jenis Pengajuan</label>
                    <p class="text-sm text-indigo-600 font-medium italic">
                        @if($sumber == 'cuti')
                            {{ $data->jenis_cuti_nama ?? $data->jenis_cuti }}
                        @elseif($sumber == 'lembur')
                            Lembur Kerja
                        @elseif($sumber == 'pensiun')
                            Pensiun ({{ $data->jenis_pengajuan ?? 'Normal' }})
                        @elseif($sumber == 'pangkat')
                            Kenaikan Pangkat/Gaji/Tunjangan
                        @else
                            {{ ucfirst($sumber) }}
                        @endif
                    </p>
                </div>
            </div>


            <!-- Content Card dengan Perbaikan Aksen Garis -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative">

                <!-- Perbaikan Aksen Garis: Menambahkan margin top/bottom dan rounded agar lebih 'soft' -->
                <div class="absolute left-0 top-4 bottom-4 w-1.5 bg-indigo-500 rounded-r-full shadow-[2px_0_8px_rgba(99,102,241,0.4)]"></div>

                @if($sumber == 'cuti')
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pl-4"> <!-- Tambah padding left agar tidak menempel garis -->

                        <!-- Blok Tanggal -->
                        <div class="flex items-center space-x-4">
                            <!-- Mulai -->
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <svg xmlns="http://www.w3.org" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mulai Cuti</span>
                                    <span class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($data->tanggal_mulai)->locale('id')->translatedFormat('l, d M Y') }}</span>
                                </div>
                            </div>

                            <div class="text-slate-300">
                                <svg xmlns="http://www.w3.org" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </div>

                            <!-- Kembali -->
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                                    <svg xmlns="http://www.w3.org" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Selesai Cuti</span>
                                    <span class="text-sm font-bold text-emerald-600">{{ \Carbon\Carbon::parse($data->tanggal_selesai)->locale('id')->translatedFormat('l, d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Durasi (Badge) -->
                        <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100 shadow-sm">
                            <span class="text-xs font-bold text-indigo-700">
                                {{ \Carbon\Carbon::parse($data->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($data->tanggal_selesai)) + 1 }} Hari Cuti
                            </span>
                        </div>
                    </div>

                    <hr class="my-3 border-slate-100 ml-3">

                    <!-- Blok Alasan -->
                    <div class="ml-4 bg-slate-50/50 p-3 rounded-2xl border border-slate-100">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Alasan Pengajuan</label>
                        <p class="text-sm text-slate-600 leading-relaxed italic">
                            "{{ $data->keterangan ?? '-' }}"
                        </p>
                    </div>

                @elseif($sumber == 'lembur')
                    <!-- Blok Deskripsi & Detail Waktu Lembur -->
                    <div class="ml-2 bg-blue-50/50 p-3 rounded-2xl border border-blue-100 space-y-5">
                        <div>
                            <label class="block text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-2">Deskripsi Pengajuan Lembur</label>
                            <p class="text-sm text-slate-700 leading-relaxed italic">"{{ $data->uraian_tugas ?? 'Tidak ada deskripsi' }}"</p>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-blue-100/50">
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
                    </div>

                @elseif($sumber == 'pensiun')
                    <!-- Blok Detail Pensiun (Khusus Direktur Kepatuhan) -->
                    <div class="ml-4 bg-orange-50/50 p-6 rounded-2xl border border-orange-100 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <span class="block text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-1">Masa Kerja</span>
                                <span class="text-sm font-bold text-slate-800">{{ $data->masa_kerja ?? '-' }} Tahun</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-1">TMT Pegawai</span>
                                <span class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($data->tmt_pegawai)->translatedFormat('d F Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-1">TMT Pensiun</span>
                                <span class="text-sm font-bold text-orange-600">{{ \Carbon\Carbon::parse($data->tmt_pensiun)->translatedFormat('d F Y') }}</span>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- Blok Detail Pangkat/Gaji (Khusus Direktur Kepatuhan) -->
                    <div class="ml-4 bg-emerald-50/50 p-6 rounded-2xl border border-emerald-100">
                        <label class="block text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-2">Uraian Perubahan Pangkat/Gaji/Tunjangan</label>
                        <p class="text-sm text-slate-700 leading-relaxed italic">"{{ $data->uraian_perubahan ?? 'Detail perubahan sedang diproses' }}"</p>
                    </div>
                @endif

            </div>
        </div>

        <!-- KOLOM KANAN: Action (Scroll Mandiri) -->
        <div class="lg:col-span-1 flex flex-col bg-slate-50 overflow-hidden border-l border-slate-100 h-full">

            <!-- 1. TITLE KEPUTUSAN (Wajib Ada di Sini agar Sticky/Tetap Muncul) -->
            <div class="p-5 bg-white border-b border-slate-200 flex-none shadow-sm">
                <h3 class="text-[12px] font-bold text-indigo-600 uppercase tracking-[0.2em]">
                    Keputusan {{ str_replace('Verifikasi ', '', $tahapTeks) }}
                </h3>
            </div>

            <!-- 2. AREA FORM (Scroll Mandiri) -->
            <!-- pb-10 saja biar margin bawah nggak terlalu jauh -->
            <div class="flex-1 overflow-y-auto custom-scroll-container p-3 pb-5">
                {{-- BENAR (langsung isinya saja) --}}
                <form id="formApproval" action="{{ route('direktur.approval.update', [$id_log, $sumber]) }}" method="POST" class="flex flex-col">
                    @csrf
                    @method('PUT')

                    {{-- LOGIKA KHUSUS LEMBUR (Direktur Operasional) --}}
                    @if($sumber === 'lembur')
                        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest">Informasi Waktu Lembur</label>
                                {{-- Tombol Ubah Dihapus --}}
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

                    {{-- LOGIKA KHUSUS CUTI/PENSIUN/PANGKAT --}}
                    @else
                        <textarea name="catatan" rows="5"
                            class="w-full p-5 text-sm bg-white border border-slate-200 rounded-3xl text-slate-700 focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition resize-none placeholder-slate-400 shadow-sm"
                            placeholder="Berikan alasan atau instruksi tambahan jika diperlukan..."
                            {{-- Jika status sudah bukan 'diproses', kotak catatan hanya bisa dibaca (readonly) --}}
                            @if($data->status !== 'diproses') readonly @endif>{{ $data->komentar ?? '' }}
                        </textarea>
                    @endif

                    {{-- Input Hidden untuk menangkap nilai 'disetujui' atau 'ditolak' dari handleApproval() --}}
                    <input type="hidden" name="aksi" id="status_input">

                    <!-- 3. ACTION BUTTONS -->
                    <div class="grid grid-cols-1 gap-3 pt-4">
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
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Fungsi sederhana untuk menangani keputusan Direktur
     * @param {string} status - 'disetujui' atau 'ditolak'
     */
    function handleApproval(status) {
        // Isi nilai input hidden 'aksi' sebelum submit
        const statusInput = document.getElementById('status_input');
        if (statusInput) {
            statusInput.value = status;
        }

        // Tampilkan konfirmasi bahasa Indonesia yang sopan
        const pesan = status === 'disetujui' ? 'menyetujui' : 'menolak';

        if(confirm('Apakah Anda yakin ingin ' + pesan + ' pengajuan ini?')) {
            document.getElementById('formApproval').submit();
        }
    }
</script>

@endsection
