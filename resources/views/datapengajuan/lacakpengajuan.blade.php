{{-- @extends($layout) --}}

@extends($layout)

@section('content')

<div class="flex-1 overflow-y-auto h-[calc(100vh-120px)] space-y-2 custom-scroll-container">
    <div class="bg-white rounded-l-md shadow-lg max-w-full">
        {{-- Header Profile Section (menggunakan p-8 untuk padding internal) --}}
        <div style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.1)), url('{{ asset('images/vecteezylight.jpg') }}')" class="bg-cover bg-bottom p-2 rounded-t-lg relative">
            <img src="{{ asset('images/trackingcheck.png') }}" alt="Overtime" class="absolute right-0 top-0 h-40">
            {{-- Opsional: Tambahkan overlay gelap agar teks lebih mudah dibaca --}}
            {{-- <div class="absolute rounded-t-lg"></div> --}}
            {{-- Image and Name section --}}
            <div class="flex items-center mt-2 ml-2 mb-2">
                {{-- Container untuk Foto Profil atau Placeholder --}}
                <div class="h-28 w-28 rounded-full overflow-hidden flex items-center justify-center">
                    @if(Auth::user()->detailPribadi && Auth::user()->detailPribadi->photo_selfie)
                        {{-- Menampilkan foto yang sudah ada dengan cache busting --}}
                        <img src="{{ asset('storage/' . Auth::user()->detailPribadi->photo_selfie) }}?v={{ time() }}"
                            class="h-32 w-32 rounded-full object-cover border border-gray-200 group-hover:border-green-500 transition-all duration-300"
                            alt="Foto Selfie Pegawai">
                    @else
                        {{-- Placeholder jika foto belum diunggah (menggunakan ikon dari query awal Anda) --}}
                        <div class="h-28 w-28 rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-200 group-hover:border-yellow-500 transition-all duration-300">
                            <x-heroicon-x-person-profile class="h-20 w-20 text-gray-400 group-hover:text-yellow-500" />
                        </div>
                    @endif
                </div>

                <div class="ml-5">
                    <h1 class="text-gray-800 text-1xl font-bold">
                        {{ Auth::user()->name ?? 'User' }} -
                        <a class="text-gray-600 font-semibold text-sm">
                            {{ Auth::user()->pegawai->nomor_urut_pegawai ?? 'Nomor Urut Pegawai tidak ditemukan' }}
                        </a>
                    </h1>
                    <p class="text-gray-600 font-semibold text-sm">{{ $pekerjaanData->jabatan ?? 'Jabatan Tidak Ditemukan' }}</p>
                    <p class="text-gray-600 font-semibold text-sm">
                        {{ ($pekerjaanData->pangkat ?? 'Pangkat Tidak Ditemukan') . ' - ' . ($pekerjaanData->grade ?? 'Grade Tidak Ditemukan') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="mb-2 p-4 shadow-sm">
            <span class="text-sm font-semibold text-blue-700"># Section 1: Status Lacak Pengajuan {{ $submissionType ?? 'Pengajuan' }}</span>
            {{-- CARD 1: Tracking Persetujuan Horizontal (Badges Modern) --}}
            <div class="flex justify-between items-center mt-6">
                {{-- <div class="w-full flex justify-between items-start overflow-hidden"> --}}
                <div class="w-full flex justify-between items-start overflow-visible">
                    {{-- Loop stageData yang sudah diproses di DataPengajuanController --}}
                    @foreach ($submission['stageData'] as $stage)
                        @php
                            // Pemetaan status untuk style agar sinkron dengan atasan
                            $isDone = ($stage['statusString'] == 'disetujui');
                            $isFail = ($stage['statusString'] == 'ditolak');
                            $isCurr = $stage['isCurrent'];

                            $circleClass = $isDone ? 'bg-emerald-500 border-emerald-100 text-white' :
                                        ($isFail ? 'bg-red-500 border-red-100 text-white' :
                                        ($isCurr ? 'bg-orange-500 border-orange-100 text-white' : 'bg-gray-200 border-gray-50 text-gray-400'));
                        @endphp

                        <div class="flex flex-col items-center flex-1 relative">
                            {{-- Garis Penghubung (Mengikuti Logic Atasan) --}}
                            {{-- Garis Penghubung (Logika: Merah jika ditolak, Hijau jika disetujui, Abu jika putus) --}}
                            @if (!$loop->last)
                                @php
                                    // 1. Ambil warna yang sudah ditentukan di Controller (stageData)
                                    $lineColor = $stage['lineColor'] ?? 'bg-gray-100';

                                    // 2. Tambahkan logika pengaman: Jika ada penolakan sebelumnya, garis tetap abu-abu (putus)
                                    $hasPreviousReject = collect($submission['stageData'])->take($loop->index)->contains('statusString', 'ditolak');
                                    if ($hasPreviousReject) {
                                        $lineColor = 'bg-gray-100';
                                    }

                                    // 3. Pastikan class warna menggunakan Emerald agar seragam dengan bulatan Anda
                                    // (Opsional: Jika di Controller pakai bg-teal-500, ganti ke bg-emerald-500 di sini)
                                    $lineColor = str_replace('bg-teal-500', 'bg-emerald-500', $lineColor);
                                @endphp

                                <div class="absolute top-5 left-1/2 w-full h-[2.5px] z-0 {{ $lineColor }}"></div>
                            @endif

                            {{-- Container Bulatan dengan Efek Radar --}}
                            <div class="relative flex items-center justify-center z-10 group">
                                @if($isCurr)
                                    <span class="absolute inline-flex h-9 w-9 rounded-full bg-orange-400 opacity-20 animate-ping"></span>
                                @endif

                                {{-- KOTAK ALASAN (Hanya untuk Atasan & Muncul di Area Garis) --}}
                                @php
                                    $forbiddenStages = ['Pengajuan Awal', 'Pengajuan', 'Selesai', 'Akhir'];
                                @endphp

                                @if(isset($stage['comment']) && $stage['comment'] && !in_array($stage['stageName'], $forbiddenStages))
                                    <div class="absolute bottom-[110%] left-[0%] -translate-x-0 mb-2
                                                invisible group-hover:visible opacity-0 group-hover:opacity-100
                                                w-48 p-3 bg-gray-900 text-white text-[10px] rounded-xl shadow-2xl
                                                z-50 transition-all duration-300 pointer-events-none">

                                        <div class="font-bold border-b border-gray-700 pb-1 mb-1 uppercase tracking-widest text-amber-400">
                                            Catatan {{ $stage['stageName'] }}:
                                        </div>
                                        <p class="italic text-gray-200 leading-relaxed">"{{ $stage['comment'] }}"</p>

                                        {{-- Segitiga (Geser sedikit ke kiri agar pas di atas garis) --}}
                                        <div class="absolute top-full left-4 border-8 border-transparent border-t-gray-900"></div>
                                    </div>
                                @endif

                                {{-- Bulatan Utama --}}
                                <div class="w-10 h-10 rounded-full {{ $circleClass }} border-[5px] flex items-center justify-center shadow-sm cursor-help transition-all duration-300">
                                    @if ($isFail)
                                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @elseif ($isDone)
                                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    @endif
                                </div>
                            </div>

                            {{-- Label & Info Waktu --}}
                            <div class="mt-4 text-center px-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tahap {{ $loop->iteration }}</p>
                                <p class="text-[11px] font-semibold text-gray-900 mt-1 leading-tight">{{ $stage['stageName'] }}</p>

                                <p class="text-[9px] font-bold {{ $isDone ? 'text-emerald-600' : ($isFail ? 'text-red-600' : ($isCurr ? 'text-orange-600' : 'text-gray-400')) }} mt-1 italic uppercase">
                                    {{ $stage['statusText'] }}
                                </p>

                                @if ($stage['updatedAt'])
                                    <p class="text-[8px] text-gray-500 mt-1 whitespace-nowrap">
                                        {{ $stage['updatedAt'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    {{-- TITIK AKHIR (SELESAI) - DENGAN GARIS PENGHUBUNG --}}
                    @php
                        $lastStage = collect($submission['stageData'])->last();
                        $isHRODone = ($lastStage['stageName'] === 'HRO' && $lastStage['statusString'] === 'disetujui');
                        $isHROFail = ($lastStage['stageName'] === 'HRO' && $lastStage['statusString'] === 'ditolak');
                    @endphp

                    <div class="flex flex-col items-center flex-1 relative">
                        {{-- GARIS PENGHUBUNG MANUAL (Dari HRO ke Selesai) --}}
                        <div class="absolute top-5 -left-1/2 w-full h-[2.5px] z-0
                            {{ $isHRODone ? 'bg-emerald-500' : ($isHROFail ? 'bg-red-500' : 'bg-gray-100') }}">
                        </div>

                        {{-- Bulatan Selesai --}}
                        <div class="w-10 h-10 rounded-full {{ $isHRODone ? 'bg-emerald-500 border-emerald-100' : 'bg-gray-100' }} border-[5px] flex items-center justify-center z-10 shadow-sm transition-all duration-300">
                            @if($isHRODone)
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            @endif
                        </div>

                        <div class="mt-4 text-center {{ $isHRODone ? '' : 'opacity-40' }}">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Akhir</p>
                            <p class="text-[11px] font-semibold text-gray-400 mt-1">Selesai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- CARD 2: Detail Data Cuti/Lembur/Pensiun --}}
    <div class="bg-white rounded-l-md shadow-lg max-w-full">
        <div class="mb-2 p-4 shadow-sm">
            <span class="text-sm font-semibold text-blue-700"># Section 2: Detail Pengajuan {{ $submissionType ?? 'Pengajuan' }}</span>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-sm mt-4">
                {{-- 4 Kolom Utama --}}
                <div>
                    <p class="text-gray-500">{{ $submission['display_info']['jenis_label'] }}</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $submission['display_info']['jenis_val'] }}</p>
                </div>
                <div>
                    <p class="text-gray-500">{{ $submission['display_info']['tgl_mulai_label'] }}</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $submission['display_info']['tgl_mulai_val'] }}</p>
                </div>
                @if ($submissionType != 'Kenaikan Pangkat/Gaji/Tunjangan')
                <div>
                    <p class="text-gray-500">{{ $submission['display_info']['tgl_selesai_label'] }}</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $submission['display_info']['tgl_selesai_val'] }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-500">{{ $submission['display_info']['total_label'] }}</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $submission['display_info']['total_val'] }}</p>
                </div>

                {{-- Kolom Tambahan (Alasan/Sisa Cuti) --}}
                @if(isset($submission['display_info']['saldo_akhir']) && $submission['display_info']['saldo_akhir'])
                <div>
                    <p class="text-gray-500">Sisa Cuti (Hari)</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $submission['display_info']['saldo_akhir'] }}</p>
                </div>
                @endif

                @if(isset($submission['display_info']['alasan_val']) && $submission['display_info']['alasan_val'])
                <div>
                    <p class="text-gray-500">{{ $submission['display_info']['alasan_label'] }}</p>
                    <p class="font-medium text-blue-600 mt-1">{{ $submission['display_info']['alasan_val'] }}</p>
                </div>
                @endif

                {{-- Alasan Disetujui/Ditolak (Variabel dari Controller utama) --}}
                <div>
                    <p class="text-gray-500">Alasan Disetujui / Ditolak</p>
                    <p class="font-medium text-blue-600 mt-1">{{ $komentarStatus ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tambahkan Section 3 untuk Dokumen --}}
    @if ($submissionType === 'Kenaikan Pangkat/Gaji/Tunjangan' || $submissionType === 'Pensiun')

        {{-- Alias Variabel: Standarisasi nama variabel menjadi $submissionData untuk konsistensi --}}
        @php
            $submissionData = null;
            if (isset($pengajuankenaikan)) {
                $submissionData = $pengajuankenaikan;
            } elseif (isset($pengajuanpensiun)) {
                $submissionData = $pengajuanpensiun;
            }
        @endphp

        <div class="bg-white rounded-l-md shadow-xl max-w-full mb-4 overflow-hidden">
            <div class="p-3 bg-gray-50 border-b border-gray-200">
                <span class="text-sm font-semibold text-blue-700"># Section 3: Dokumen Persyaratan</span>
            </div>

            {{-- Tabel Dokumen --}}
            <div class="mt-0 overflow-x-auto mb-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nama File</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Syarat Dokumen</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        {{-- Loop melalui relasi dokumenPersyaratan HANYA JIKA submissionData ada dan memiliki files --}}
                        @foreach($submissionData?->files ?? [] as $doc)
                            {{-- Siapkan variabel bantuan untuk JS --}}
                            @php
                                $extension = strtolower(pathinfo($doc->nama_file_asli, PATHINFO_EXTENSION));
                                // $url = route('view.document', $doc->id);
                                if ($submissionType === 'Pensiun') {
                                    $url = route('view.document.pensiun', $doc->id); // Gunakan rute baru pensiun
                                } else {
                                    $url = route('view.document', $doc->id); // Gunakan rute lama pangkat/gaji
                                }
                            @endphp

                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $doc->nama_file_asli }}</td>

                                {{-- Ambil nilai Tipe Dokumen LANGSUNG dari kolom DB --}}
                                <td class="px-5 py-3 whitespace-nowrap text-left">
                                    @if ($extension === 'pdf')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $doc->tipe_dokumen }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $doc->tipe_dokumen }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    {{-- Teruskan ekstensi ke fungsi openModal untuk logika tampilan --}}
                                    <button onclick="openModal('{{ $url }}', '{{ $extension }}')" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow hover:shadow-md transition duration-150 ease-in-out">
                                        Lihat Dokumen
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pengecekan data yang aman menggunakan alias variabel --}}
                @if(empty($submissionData?->files) || $submissionData->files->isEmpty())
                    <p class="p-6 text-gray-500 text-sm italic">Tidak ada dokumen yang diunggah.</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Pastikan Anda MENYISIPKAN JavaScript dan Struktur Modal --}}
    <div id="documentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 backdrop-blur-sm">
        <div class="bg-white p-4 rounded-lg shadow-xl w-3/4 max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-md font-bold">Lihat Dokumen</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="documentContent" class="w-full h-[75vh]">
                {{-- Konten dokumen akan dimuat di sini --}}
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('documentModal');
    const contentArea = document.getElementById('documentContent');

    function openModal(url, fileType) {
        if (fileType.toLowerCase() === 'pdf' || url.toLowerCase().endsWith('.pdf')) {
            contentArea.innerHTML = `<iframe src="${url}" frameborder="0" class="w-full h-full"></iframe>`;
        } else {
            contentArea.innerHTML = `<p class="p-4 text-center">Pratinjau tidak tersedia untuk tipe file ini. Silakan <a href="${url}" class="text-blue-600 hover:underline font-semibold">unduh dokumen</a> untuk melihatnya.</p>`;
        }
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        contentArea.innerHTML = '';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

@endsection
