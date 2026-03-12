@extends('layouts.app-manager')

@section('content')

<div class="bg-indigo-500 rounded-tl-md shadow-xl pl-4 pr-4 pb-4 pt-2">
    <span class="text-sm font-semibold text-white">#Section {{ $pageTitle }}</span>
    {{-- <div class="px-2 py-2 bg-amber-500 shadow-lg text-center"></div> --}}
    <p class="text-xs text-white">Tinjau informasi pengajuan dengan teliti sebelum memberikan keputusan.</p>
</div>

@php
    $totalHistory = count($historiLog);
    $lastLog = $historiLog->last();
    $statusTerakhir = strtolower($lastLog->status_persetujuan ?? $lastLog->status_pengajuan);

    // Cek apakah sudah disetujui sampai tahap paling akhir (misal HRO atau Direktur)
    $isFinalApproved = ($statusTerakhir === 'disetujui' && ($lastLog->tahap_persetujuan === 'HRO' || $lastLog->tahap_persetujuan === 'Selesai'));
@endphp

<div class="bg-white border-b border-gray-100 max-w-full py-4 shadow-sm">
    <div class="max-w-4xl mx-auto px-6">
        <div class="relative flex items-start">
            @foreach($historiLog as $index => $log)
@php
    $logStatus = strtolower($log->status_persetujuan ?? $log->status_pengajuan);
    $isDone = ($logStatus === 'disetujui');
    $isCurrent = ($logStatus === 'diproses');
    $isRejected = ($logStatus === 'ditolak');

    $timeCol = (isset($sumber) && $sumber === 'pensiun') ? 'update_at' : 'updated_at';
    $waktuTampil = $log->tgl_persetujuan ?? $log->tgl_pengajuan ?? ($log->$timeCol ?? null);

    $nextLog = $historiLog[$index + 1] ?? null;
    $nextStatus = $nextLog ? strtolower($nextLog->status_persetujuan ?? $nextLog->status_pengajuan) : null;

    // LOGIKA GARIS (DIUBAH AGAR SETELAH MERAH JADI MERAH)
    if ($nextStatus === 'ditolak') {
        $lineColor = 'bg-red-500'; // Garis ke arah penolakan jadi MERAH
    } elseif ($nextStatus === 'diproses') {
        $lineColor = 'bg-orange-500'; // Garis ke arah yang sedang diproses jadi ORANGE
    } elseif ($nextStatus === 'disetujui') {
        $lineColor = 'bg-emerald-500'; // Garis ke arah yang sudah disetujui jadi HIJAU
    } elseif ($isRejected) {
        $lineColor = 'bg-red-500'; // Garis setelah titik merah tetap MERAH
    } else {
        $lineColor = 'bg-gray-200'; // Sisanya ABU-ABU
    }

    $circleClass = $isDone ? 'bg-emerald-500 border-emerald-100' : ($isCurrent ? 'bg-orange-500 border-orange-100 shadow-[0_0_15px_rgba(249,115,22,0.4)]' : ($isRejected ? 'bg-red-500 border-red-100 shadow-[0_0_15px_rgba(239,68,68,0.4)]' : 'bg-gray-200 border-gray-50'));
@endphp

<div class="flex flex-col items-center flex-1 relative">
    <!-- GARIS PENGHUBUNG -->
    @if(!$loop->last)
        <div class="absolute top-5 left-1/2 w-full h-[2.5px] z-0 {{ $lineColor }}"></div>
    @else
        <div class="absolute top-5 left-1/2 w-full h-[2.5px] z-0 {{ (isset($isFinalApproved) && $isFinalApproved) ? 'bg-emerald-500' : 'bg-gray-100' }}"></div>
    @endif

    <!-- Bulatan -->
    <div class="w-10 h-10 rounded-full {{ $circleClass }} border-[5px] flex items-center justify-center z-10 transition-all duration-300">
        @if($isDone)
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        @elseif($isRejected)
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
        @elseif($isCurrent)
            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @else
            <div class="w-2 h-2 bg-white rounded-full"></div>
        @endif
    </div>

    <!-- Label Info -->
    <div class="mt-4 text-center px-2">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tahap {{ $index + 1 }}</p>
        <p class="text-[11px] font-semibold text-gray-900 mt-1 leading-tight">
            {{ $log->tahap_persetujuan == 'Pengajuan Awal' ? 'Pengajuan' : $log->tahap_persetujuan }}
        </p>
        <p class="text-[9px] font-bold {{ $isDone ? 'text-emerald-600' : ($isCurrent ? 'text-orange-600' : ($isRejected ? 'text-red-600' : 'text-gray-400')) }} mt-1 italic uppercase">
            {{ $logStatus }}
        </p>

        @if($waktuTampil)
            <div class="mt-2">
                <p class="text-[9px] text-gray-500 font-medium whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($waktuTampil)->translatedFormat('d M Y') }}
                    <span class="text-gray-400 ml-1">{{ \Carbon\Carbon::parse($waktuTampil)->format('H:i') }} WIB</span>
                </p>
            </div>
        @endif
    </div>
</div>
@endforeach

            <!-- TITIK AKHIR (DINAMIS) -->
            <div class="flex flex-col items-center flex-1 relative">
                <div class="w-10 h-10 rounded-full {{ $isFinalApproved ? 'bg-emerald-500 border-emerald-100' : 'bg-gray-100' }} border-[5px] flex items-center justify-center z-10">
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                </div>
                <div class="mt-4 text-center {{ $isFinalApproved ? '' : 'opacity-40' }}">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Akhir</p>
                    <p class="text-[11px] font-semibold text-gray-400 mt-1">Selesai</p>
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
            <div class="grid grid-cols-2 gap-8 mb-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jabatan</label>
                    <p class="text-sm text-slate-700 font-medium">{{ $data->jabatan }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jenis Pengajuan</label>
                    <p class="text-sm text-indigo-600 font-medium">{{ $sumber == 'cuti' ? $data->Jenis_cuti : 'Lembur Kerja' }}</p>
                </div>
            </div>

            <!-- Content Card dengan Perbaikan Aksen Garis -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative">

                <!-- Perbaikan Aksen Garis: Menambahkan margin top/bottom dan rounded agar lebih 'soft' -->
                <div class="absolute left-0 top-4 bottom-4 w-1.5 bg-indigo-500 rounded-r-full shadow-[2px_0_8px_rgba(99,102,241,0.4)]"></div>

                @if($sumber == 'cuti')
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pl-4"> <!-- Tambah padding left agar tidak menempel garis -->

                        <!-- Blok Tanggal -->
                        <div class="flex items-center space-x-6">
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
                    <div class="ml-4 bg-slate-50/50 p-5 rounded-2xl border border-slate-100">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Alasan Pengajuan</label>
                        <p class="text-sm text-slate-600 leading-relaxed italic">
                            "{{ $data->keterangan ?? '-' }}"
                        </p>
                    </div>
                @else
                    <!-- Blok Deskripsi Lembur -->
                    <div class="ml-4 bg-blue-50/50 p-5 rounded-2xl border border-blue-100">
                        <label class="block text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-3">Deskripsi Pengajuan Lembur</label>
                        <p class="text-sm text-slate-600 leading-relaxed italic">
                            "{{ $data->uraian_tugas ?? 'Tidak ada deskripsi' }}"
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Action (Light Slate Theme) -->
        <div class="lg:col-span-1 flex flex-col bg-slate-50 p-4">
            <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-[0.2em] mb-3">
                Keputusan {{ str_replace('Verifikasi ', '', $tahapTeks) }}
            </h3>

            <form id="formApproval" action="{{ route('manager.updateStatus', [$sumber, $id_log]) }}" method="POST" class="flex flex-col h-full">
                @csrf
                @method('PUT')

                <textarea name="catatan" rows="4"
                    class="w-full p-4 text-sm bg-white border border-slate-300 rounded-lg text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition resize-none placeholder-slate-400 shadow-lg"
                    placeholder="Wajib memberikan alasan atau catatan..."></textarea>

                <input type="hidden" name="status" id="status_input">

                <div class="mt-3 space-y-3">
                    <button type="button" onclick="handleApproval('disetujui')" @disabled($data->status !== 'diproses')
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-200 disabled:text-slate-400 text-white text-sm font-bold rounded-lg transition-all shadow-md active:scale-[0.98]">
                        Disetujui
                    </button>

                    <button type="button" onclick="handleApproval('ditolak')" @disabled($data->status !== 'diproses')
                        class="w-full py-3 bg-white border border-rose-300 text-rose-600 hover:bg-rose-50 disabled:border-slate-200 disabled:text-slate-300 text-sm font-bold rounded-lg transition-all shadow-sm">
                        Ditolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
