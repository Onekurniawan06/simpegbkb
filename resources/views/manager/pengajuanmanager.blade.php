@extends('layouts.app-manager')

@section('content')
{{-- @dump(session()->all()) --}}

    <!-- Ini adalah satu-satunya elemen root sekarang -->
    @if(session('warning'))
        <div class="alert alert-warning rounded-lg shadow-sm mb-2 py-2" role="alert" style="background-color: #fff3cd; color: #856404; border-left: 5px solid #ffc107 !important;">
            <!-- Container utama untuk seluruh konten dalam satu baris -->
            <div class="d-flex align-items-center justify-content-between">

                <!-- Kontainer Kiri: Ikon, Judul, Pesan, Teks Tautan -->
                <div class="d-flex align-items-center gap-2 text-sm text-gray-600 ml-2">
                    Profil Belum Lengkap! {{ session('warning') }} Klik Tautan <a href="{{ route('pegawai.profile', ['form_type' => 'new']) }}" class="text-sm text-gray-800 hover:text-green-400 font-semibold">Isi Data Diri</a> Nomor Urut Pegawai Anda: <strong>{{ auth()->user()->nomor_urut_pegawai }}</strong>.
                </div>
            </div>
        </div>
    @endif

    {{-- <div class="bg-gray-100 p-2 md:p-4 rounded-lg shadow-lg max-w-full h-full mx-auto"> --}}
        <!-- Menu Grid Section (5 columns) -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <!-- Card Cuti & Izin -->
            @if($pendingCutiManager)
                <div class="block h-full cursor-not-allowed group" title="Anda memiliki pengajuan cuti yang masih dalam proses persetujuan.">
                    <div class="bg-white p-4 rounded-lg shadow flex items-center flex-col text-center h-full opacity-50">
                        <div class="h-12 w-12 bg-orange-100 rounded-full mb-2">
                            <img src="{{ asset('images/ico/user-travel.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                        </div>
                        <p class="font-semibold text-gray-700 text-sm">Cuti & Izin</p>
                        <p class="text-xs text-red-500 mt-1 mb-2">Pengajuan sedang diproses</p>
                    </div>
                </div>
            @else
                <a href="{{ route('cuti.formCutiIzin') }}" class="block h-full">
                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-md hover:bg-orange-50 transition duration-300 cursor-pointer flex items-center flex-col text-center h-full">
                        <div class="h-12 w-12 bg-orange-100 rounded-full mb-2">
                            <img src="{{ asset('images/ico/user-travel.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                        </div>
                        <p class="font-semibold text-gray-700 text-sm">Cuti & Izin</p>
                        <p class="text-xs text-gray-500 mt-1">Klik untuk buat pengajuan</p>
                    </div>
                </a>
            @endif

            @if($pendingLemburManager)
                <div class="block h-full cursor-not-allowed group" title="Anda memiliki pengajuan Lembur yang masih dalam proses persetujuan.">
                    <div class="bg-white p-4 rounded-lg shadow flex items-center flex-col text-center h-full opacity-50">
                        <div class="h-12 w-12 bg-orange-100 rounded-full mb-2">
                            <img src="{{ asset('images/ico/over-time.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                        </div>
                        <p class="font-semibold text-gray-700 text-sm">Lembur</p>
                        <p class="text-xs text-red-500 mt-1 mb-2">Pengajuan sedang diproses</p>
                    </div>
                </div>
            @else
                <!-- Card Lembur -->
                <a href="{{ route('lembur.formLembur') }}" class="block h-full">
                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-50 transition duration-300 cursor-pointer flex items-center flex-col text-center h-full">
                        <div class="h-12 w-12 bg-orange-100 rounded-full mb-2">
                            <img src="{{ asset('images/ico/over-time.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                        </div>
                        <p class="font-semibold text-gray-700 text-sm">Lembur</p>
                        <p class="text-xs text-gray-500 mt-1">Klik untuk buat pengajuan</p>
                    </div>
                </a>
            @endif

            @if($pendingPensiunManager)
                <div class="block h-full cursor-not-allowed group" title="Anda memiliki pengajuan Lembur yang masih dalam proses persetujuan.">
                    <div class="bg-white p-4 rounded-lg shadow flex items-center flex-col text-center h-full opacity-50">
                        <div class="h-12 w-12 bg-orange-100 rounded-full mb-2">
                            <img src="{{ asset('images/ico/work-retirement.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                        </div>
                        <p class="font-semibold text-gray-700 text-sm">Pensiun</p>
                        <p class="text-xs text-red-500 mt-1 mb-2">Pengajuan sedang diproses</p>
                    </div>
                </div>
            @else
                <!-- Card Pensiun -->
                <a href="{{ route('pensiun.formPensiun') }}" class="block h-full">
                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-md hover:bg-blue-50 transition duration-300 cursor-pointer flex items-center flex-col text-center h-full">
                        <div class="h-12 w-12 bg-yellow-100 rounded-full mb-2">
                            <img src="{{ asset('images/ico/work-retirement.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                        </div>
                        <p class="font-semibold text-gray-700 text-sm">Pensiun</p>
                        <p class="text-xs text-gray-500 mt-1">Klik untuk buat pengajuan</p>
                    </div>
                </a>
            @endif

            <!-- Card Kenaikan Pangkat, Gaji & Tunjangan -->
            <a href="{{ route('kenaikanpangkatgajitunjangan.pangkatgajitunjangan') }}" class="block h-full">
                <div class="bg-white p-4 rounded-lg shadow hover:shadow-md hover:bg-green-50 transition duration-300 cursor-pointer flex items-center flex-col text-center h-full">
                    <div class="h-12 w-12 bg-yellow-100 rounded-full mb-2">
                        <img src="{{ asset('images/ico/promotion-in-salary.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                    </div>
                    <p class="font-semibold text-gray-700 text-sm">Kenaikan Pangkat, Gaji & Tunjangan</p>
                    <p class="text-xs text-gray-500 mt-1">Klik untuk buat pengajuan</p>
                </div>
            </a>

            <!-- Card Penghargaan Masa Kerja -->
            <a href="#" class="block h-full">
                <div class="bg-white p-4 rounded-l-lg shadow hover:shadow-md hover:bg-red-50 transition duration-300 cursor-pointer flex items-center flex-col text-center h-full">
                    <div class="h-12 w-12 bg-yellow-100 rounded-full mb-2">
                        <img src="{{ asset('images/ico/service-award.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                    </div>
                    <p class="font-semibold text-gray-700 text-sm">Penghargaan Masa Kerja</p>
                    <p class="text-xs text-gray-500 mt-1">Klik untuk buat pengajuan</p>
                </div>
            </a>
        </div>

    {{-- SECTION RIWAYAT --}}
    <div class="bg-white shadow-sm rounded-l-lg overflow-hidden border border-gray-100 mt-2 h-full flex flex-col">
        <div class="px-4 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
            <span class="font-semibold text-sm tracking-tight">Riwayat Pengajuan Saya</span>
        </div>

        <!-- TAMBAHKAN max-h-96 (tinggi maksimal) DAN overflow-y-auto (scroll otomatis) DI SINI -->
        <div class="p-4 space-y-3 flex-grow overflow-y-auto custom-scroll-container">
            @forelse($paginatedSubmissions as $sub)
                <!-- Baris Pengajuan -->
                <div class="flex items-center p-2 bg-gray-100 border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                    {{-- ... konten Anda tetap sama ... --}}
                    <div class="flex items-center gap-5 w-1/3">
                        <div class="p-2 bg-teal-50 rounded-lg shrink-0">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Jenis Pengajuan</p>
                            <p class="text-sm font-bold text-gray-800">{{ $sub['display_info']['jenis_val'] }}</p>
                        </div>
                    </div>

                    <div class="hidden md:block w-64">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-1">Tanggal Pengajuan</p>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm font-bold text-gray-700">
                                {{ $sub['display_info']['tgl_pengajuan_val'] }}
                            </p>
                        </div>
                    </div>

                    <div class="text-center w-40">
                        <span class="px-3 py-1 rounded-lg text-[12px] font-bold {{ $sub['blade_class'] }}">
                            {{ $sub['blade_status_text'] }}
                        </span>
                        <p class="text-[10px] text-gray-400 mt-1 italic">{{ $sub['blade_stage'] }}</p>
                    </div>

                    <div class="w-54 text-right">
                        <a href="{{ route($sub['blade_route'], $sub['nomor_urut_pegawai'] ?? $sub['id']) }}"
                        class="inline-flex items-center px-4 py-2 bg-[#0f172a] text-white text-xs font-bold rounded-xl hover:bg-teal-700 transition-colors">
                            Lihat Detail <span class="ml-2">→</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="h-full flex items-center justify-center py-8 text-center text-gray-400 text-xs italic">
                    Belum ada data pengajuan yang ditemukan.
                </div>
            @endforelse
        </div>

        <div class="px-6 py-3 bg-gray-50 shrink-0">
            {{ $paginatedSubmissions->links() }}
        </div>
    </div>

@endsection
