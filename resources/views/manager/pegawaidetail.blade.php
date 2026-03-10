@extends($layout)

@section('content')

{{-- Container Utama dengan Background Gray dan Rounded sesuai permintaan --}}
{{-- Script Alpine.js untuk Live Filter --}}
<script src="https://unpkg.com" defer></script>

<div class="h-full w-full shadow-lg flex flex-col overflow-hidden">
    <!-- Area Konten: Scrollable -->
    <div class="flex-1 overflow-y-auto custom-scroll-container">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">

            <!-- KOLOM KIRI: Foto & Identitas Utama -->
            <div class="md:col-span-1 space-y-2">
                <div class="bg-white pt-3 rounded-md shadow-sm flex flex-col items-center text-center group">
                    <div class="relative mb-4">
                        @if($pegawai->photo_path && file_exists(storage_path('app/public/' . $pegawai->photo_path)))
                            {{-- Tampilkan Foto Jika Ada --}}
                            <img src="{{ asset('storage/'.$pegawai->photo_path) }}"
                                class="w-28 h-328 rounded-full object-cover border-4 border-blue-50 shadow-md group-hover:border-yellow-500 transition-all duration-300">
                        @else
                            {{-- Placeholder jika foto belum diunggah --}}
                            <div class="h-28 w-28 rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-200 group-hover:border-yellow-500 transition-all duration-300">
                                <x-heroicon-s-user class="h-20 w-20 text-gray-400 group-hover:text-yellow-500" />
                            </div>
                        @endif
                    </div>

                    <div class="bg-[#001A4E] text-white rounded-b-md w-full p-1">
                        <h3 class="text-[14px] font-bold">{{ $pegawai->nama }}</h3>
                        <p class="text-[12px] font-normal opacity-70 mt-1"> Divisi {{ $pegawai->nama_divisi }} - {{ $pegawai->nomor_urut_pegawai }}</p>
                    </div>
                </div>

                <div class="bg-[#001A4E] p-2 rounded-md text-white shadow-lg">
                    <h6 class="text-[11px] font-bold uppercase opacity-50 mb-3">Masa Kerja</h6>
                    <div class="text-xl font-bold">
                        @php
                            $tmt = \Carbon\Carbon::parse($pegawai->tmt_pegawai);
                            $tahun = $tmt->diffInYears(now());
                            $bulan = $tmt->addYears($tahun)->diffInMonths(now());
                        @endphp

                        {{ intval($tahun) }} <span class="text-xs font-normal opacity-70">Tahun</span>
                        {{ intval($bulan) }} <span class="text-xs font-normal opacity-70">Bulan</span>
                    </div>
                    <p class="text-[11px] opacity-60 mt-1">Bergabung sejak {{ \Carbon\Carbon::parse($pegawai->tmt_pegawai)->format('d M Y') }}</p>
                </div>
            </div>

            <!-- KOLOM KANAN: Detail Informasi -->
            <div class="md:col-span-3 space-y-2">
                <!-- Data Pekerjaan -->
                <div class="bg-white rounded-md shadow-sm border border-gray-100 p-3">
                    <h3 class="text-xs font-black text-gray-800 uppercase pb-3">
                        Informasi Data Pegawai
                    </h3>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-y-8 gap-x-6">
                        {{-- Baris 1 --}}
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase mb-1">Pangkat / Grade</p>
                            <p class="text-xs font-bold text-gray-800">{{ $pegawai->pangkat }} / {{ $pegawai->grade }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase mb-1">Golongan Pajak</p>
                            <p class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg w-fit">
                                {{ $pegawai->golongan_pajak ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase mb-1">Status Kepegawaian</p>
                            <p class="text-xs font-bold text-gray-800">{{ $pegawai->status_pegawai }}</p>
                        </div>

                        {{-- Baris 2: Bagian Periode dengan Background Berbeda --}}
                        <div class="col-span-1 md:col-span-2 grid grid-cols-2 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-[9px] text-gray-400 font-bold uppercase mb-1">Periode Kenaikan Gapok</p>
                                <p class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg w-fit">
                                    {{ $pegawai->periode_kenaikan_gapok ? \Carbon\Carbon::parse($pegawai->periode_kenaikan_gapok)->format('d M Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 font-bold uppercase mb-1">Periode Kenaikan Grade</p>
                                <p class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg w-fit">
                                    {{ $pegawai->periode_kenaikan_grade ? \Carbon\Carbon::parse($pegawai->periode_kenaikan_grade )->format('d M Y') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Pribadi -->
                <div class="bg-white rounded-md shadow-sm border border-gray-100 p-3">
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest pb-3">Informasi Pribadi</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Tempat, Tanggal Lahir</p>
                            <p class="text-sm font-bold text-gray-700">
                                {{-- Data diambil dari tabel detail_pribadi --}}
                                {{ $pegawai->tempat_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tanggal_lahir ?? '-')->format('d M Y') }}
                            </p>
                            {{-- Menampilkan Umur secara otomatis --}}
                            <p class="text-[11px] text-blue-500 font-medium">
                                (Usia: {{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->age ?? '-'}} Tahun)
                            </p>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">Agama</p>
                            <p class="text-xs font-bold text-gray-700">{{ $pegawai->agama ?? '-'}}</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">Email</p>
                            <p class="text-xs font-bold text-gray-700">{{ $pegawai->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">No. Telpon</p>
                            <p class="text-xs font-bold text-gray-700">{{ $pegawai->no_telpon ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">Pendidikan Terakhir</p>
                            <p class="text-xs font-bold text-gray-700">{{ $pegawai->pendidikan_terakhir ?? '-' }} ({{ $pegawai->jurusan ?? '-'}})</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">Status Perkawinan</p>
                            <p class="text-xs font-bold text-gray-700">{{ $pegawai->status_perkawinan ?? '-'}}</p>
                        </div>
                        <div class="col-span-2 md:col-span-3">
                            <p class="text-[9px] text-gray-400 font-bold uppercase">Alamat Lengkap</p>
                            <p class="text-xs font-bold text-gray-700 leading-relaxed">{{ $pegawai->alamat ?? '-'}}</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection
