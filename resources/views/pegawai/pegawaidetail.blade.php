@extends($layout)

@section('content')

{{-- Container Utama dengan Background Gray dan Rounded sesuai permintaan --}}
{{-- Script Alpine.js untuk Live Filter --}}
<script src="https://unpkg.com" defer></script>

<div class="h-full w-full flex flex-col overflow-hidden">
    <!-- AREA SCROLL UTAMA -->
    <div class="flex-1 overflow-y-auto custom-scroll-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-2">

            <!-- KOLOM KIRI: KARTU PROFIL & MASA KERJA (MENYATU) -->
            <div class="lg:col-span-3">
                <div class="bg-[#ecfdf5] border border-[#a7f3d0] rounded-lg overflow-hidden shadow-sm">
                    <!-- Bagian Foto (Header Gelap Tetap Dipertahankan sesuai design awal) -->
                    <div class="h-20 bg-slate-800"></div>
                    <div class="px-5 pb-6 flex flex-col items-center -mt-10">
                        <div class="relative mb-3">
                            {{-- Cek menggunakan kolom photo_selfie sesuai database Anda --}}
                            @if($pegawai->photo_selfie && file_exists(storage_path('app/public/' . $pegawai->photo_selfie)))
                                <img src="{{ asset('storage/' . $pegawai->photo_selfie) }}?v={{ time() }}"
                                    class="w-24 h-24 rounded-lg object-cover border-4 border-[#ecfdf5] shadow-md">
                            @else
                                {{-- Placeholder jika foto tidak ada --}}
                                <div class="w-24 h-24 rounded-lg bg-white flex items-center justify-center border-4 border-[#ecfdf5] shadow-md">
                                    <x-heroicon-s-user class="h-12 w-12 text-slate-300" />
                                </div>
                            @endif
                        </div>
                        <h3 class="text-base font-bold text-emerald-900 text-center leading-tight">{{ $pegawai->nama }}</h3>
                        <p class="text-xs font-bold text-emerald-600 mt-1.5 uppercase tracking-widest">{{ $pegawai->nomor_urut_pegawai }}</p>

                        <!-- Garis Pemisah Inner -->
                        <div class="w-full my-4 border-t border-emerald-200/50"></div>

                        <!-- Bagian Masa Kerja (Menyatu di bawah Profil) -->
                        <div class="w-full">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-3 text-center lg:text-left">Masa Kerja Pegawai</p>
                            <div class="flex justify-center lg:justify-start items-baseline gap-4 text-emerald-900">
                                @php
                                    $tmt = \Carbon\Carbon::parse($pegawai->tmt_pegawai);
                                    $tahun = $tmt->diffInYears(now());
                                    $bulan = $tmt->copy()->addYears($tahun)->diffInMonths(now());
                                @endphp
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-3xl font-black">{{ intval($tahun) }}</span>
                                    <span class="text-[10px] font-bold uppercase text-emerald-600">Thn</span>
                                </div>
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-3xl font-black">{{ intval($bulan) }}</span>
                                    <span class="text-[10px] font-bold uppercase text-emerald-600">Bln</span>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-emerald-200/50">
                                <p class="text-[10px] text-emerald-600 font-medium">Bergabung Sejak:</p>
                                <p class="text-xs font-bold text-emerald-900">{{ \Carbon\Carbon::parse($pegawai->tmt_pegawai)->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: DATA DETAIL -->
            <div class="lg:col-span-9 space-y-2">

                <!-- INFORMASI PEKERJAAN -->
                <div class="bg-white rounded-l-lg shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-3 border-b border-orange-100 bg-orange-50 flex items-center gap-3">
                        <div class="w-1.5 h-5 bg-blue-600 rounded-full"></div>
                        <h3 class="text-xs font-bold text-orange-800 uppercase tracking-widest">Informasi Pekerjaan</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2">Pangkat & Grade</label>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-3 py-1 bg-slate-100 rounded text-[11px] font-bold text-slate-700 border border-slate-200">{{ $pegawai->pangkat }}</span>
                                    <span class="px-3 py-1 bg-blue-50 rounded text-[11px] font-bold text-blue-600 border border-blue-100">{{ $pegawai->grade }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2">Jabatan</label>
                                <p class="text-sm font-bold text-slate-700">{{ $pegawai->jabatan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2">Divisi</label>
                                <p class="text-sm font-bold text-blue-700">{{ $pegawai->nama_divisi }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2">Status</label>
                                <span class="inline-flex px-3 py-1 rounded bg-emerald-50 text-emerald-600 text-[11px] font-bold border border-emerald-100">
                                    {{ $pegawai->status_pegawai }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-50 p-5 rounded-lg border border-slate-100">
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Golongan Pajak</p>
                                <p class="text-xs font-bold text-blue-600">{{ $pegawai->golongan_pajak ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Kenaikan Gaji Berkala</p>
                                <p class="text-xs font-bold text-slate-700">{{ $pegawai->periode_kenaikan_gapok ? \Carbon\Carbon::parse($pegawai->periode_kenaikan_gapok)->translatedFormat('d M Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Kenaikan Grade</p>
                                <p class="text-xs font-bold text-slate-700">{{ $pegawai->periode_kenaikan_grade ? \Carbon\Carbon::parse($pegawai->periode_kenaikan_grade )->translatedFormat('d M Y') : '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DATA PRIBADI -->
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-3 border-b border-blue-100 bg-blue-50 flex items-center gap-3">
                        <!-- Indikator Batang Biru Solid -->
                        <div class="w-1.5 h-5 bg-orange-500 rounded-full"></div>
                        <h3 class="text-xs font-bold text-blue-800 uppercase tracking-widest">Data Pribadi</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-y-6 gap-x-4">
                            {{-- Baris 1 --}}
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">Lahir</label>
                                <p class="text-xs font-bold text-slate-700">{{ $pegawai->tempat_lahir ?? '-' }}, {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->translatedFormat('d M Y') : '-' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">Pendidikan</label>
                                <p class="text-xs font-bold text-slate-700">{{ $pegawai->pendidikan_terakhir ?? '-' }} {{ $pegawai->jurusan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase mb-1.5">No. Telpon</p>
                                <p class="text-xs font-bold text-slate-700">{{ $pegawai->no_telpon ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase mb-1.5">Email</p>
                                <p class="text-xs font-bold text-slate-700">{{ $pegawai->email ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">Pernikahan</label>
                                <p class="text-xs font-bold text-slate-700">{{ $pegawai->status_perkawinan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">Agama</label>
                                <p class="text-xs font-bold text-slate-700">{{ $pegawai->agama ?? '-' }}</p>
                            </div>

                            {{-- Alamat (Mengambil sisa kolom) --}}
                            <div class="md:col-span-4 mt-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2">Alamat Lengkap Domisili</label>
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                                    <p class="text-sm font-medium text-slate-600 leading-relaxed italic">"{{ $pegawai->alamat ?? '-' }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- AKHIR KOLOM KANAN -->
        </div>
    </div>
</div>


@endsection

                        {{-- <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">Email</p>
                            <p class="text-xs font-bold text-gray-700">{{ $pegawai->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">No. Telpon</p>
                            <p class="text-xs font-bold text-gray-700">{{ $pegawai->no_telpon ?? '-' }}</p>
                        </div> --}}
