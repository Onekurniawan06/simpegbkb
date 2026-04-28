@extends('layouts.app-hro')

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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 overflow-visible">

            <!-- 1. CARD CUTI & IZIN -->
            <div class="relative h-full group">
                @if($isAnyPending && !$pendingCutiManager)
                    <div class="absolute inset-0 z-20 bg-red-50/30 rounded-md flex flex-col items-center justify-center border-2 border-red-200 cursor-not-allowed">
                        <div class="bg-red-600 text-white p-2 rounded-full shadow-lg mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z""")/>></svg>
                        </div>
                        <span class="text-[10px] font-black text-red-600 uppercase tracking-widest bg-white px-3 py-1 rounded-full shadow-md border border-red-200">Akses Terkunci</span>
                    </div>
                @endif

                @if($pendingCutiManager)
                    <div class="bg-white p-4 rounded-xl shadow-md border-2 border-orange-500 flex flex-col items-center text-center h-full ring-4 ring-orange-50">
                        <div class="h-12 w-12 bg-orange-100 rounded-full mb-2 flex items-center justify-center"><img src="{{ asset('images/ico/user-travel.svg') }}" class="w-8 h-8"></div>
                        <p class="font-bold text-gray-800 text-sm">Cuti & Izin</p>
                        <div class="mt-2 px-3 py-1 bg-orange-500 rounded-full animate-pulse shadow-sm"><p class="text-[9px] font-black text-white uppercase tracking-widest">Sedang Diproses</p></div>
                    </div>
                @else
                    <a href="{{ route('cuti.formCutiIzin') }}" class="block h-full group">
                        <div class="bg-white p-4 rounded-xl shadow-sm hover:shadow-xl hover:bg-orange-50 transition-all duration-300 flex flex-col items-center text-center h-full border border-gray-100 group-hover:border-orange-300">
                            <div class="h-12 w-12 bg-orange-50 rounded-full mb-2 flex items-center justify-center group-hover:scale-110 transition-transform"><img src="{{ asset('images/ico/user-travel.svg') }}" class="w-8 h-8"></div>
                            <p class="font-semibold text-gray-700 text-sm">Cuti & Izin</p>
                            <p class="text-[10px] text-gray-400 mt-1 italic font-medium">Klik untuk buat pengajuan</p>
                        </div>
                    </a>
                @endif
            </div>

            <!-- 2. CARD LEMBUR -->
            <div class="relative h-full group">
                @if($isAnyPending && !$pendingLemburManager)
                    <div class="absolute inset-0 z-20 bg-red-50/30 rounded-md flex flex-col items-center justify-center border-2 border-red-200 cursor-not-allowed">
                        <div class="bg-red-600 text-white p-2 rounded-full shadow-lg mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z""")/>></svg>
                        </div>
                        <span class="text-[10px] font-black text-red-600 uppercase tracking-widest bg-white px-3 py-1 rounded-full shadow-md border border-red-200">Akses Terkunci</span>
                    </div>
                @endif

                @if($pendingLemburManager)
                    <div class="bg-white p-4 rounded-xl shadow-md border-2 border-blue-500 flex flex-col items-center text-center h-full ring-4 ring-blue-50">
                        <div class="h-12 w-12 bg-blue-100 rounded-full mb-2 flex items-center justify-center"><img src="{{ asset('images/ico/over-time.svg') }}" class="w-8 h-8"></div>
                        <p class="font-bold text-gray-800 text-sm">Lembur</p>
                        <div class="mt-2 px-3 py-1 bg-blue-500 rounded-full animate-pulse shadow-sm"><p class="text-[9px] font-black text-white uppercase tracking-widest">Sedang Diproses</p></div>
                    </div>
                @else
                    <a href="{{ route('lembur.formLembur') }}" class="block h-full group">
                        <div class="bg-white p-4 rounded-xl shadow-sm hover:shadow-xl hover:bg-blue-50 transition-all duration-300 flex flex-col items-center text-center h-full border border-gray-100 group-hover:border-blue-300">
                            <div class="h-12 w-12 bg-blue-50 rounded-full mb-2 flex items-center justify-center group-hover:scale-110 transition-transform"><img src="{{ asset('images/ico/over-time.svg') }}" class="w-8 h-8"></div>
                            <p class="font-semibold text-gray-700 text-sm">Lembur</p>
                            <p class="text-[10px] text-gray-400 mt-1 italic font-medium">Klik untuk buat pengajuan</p>
                        </div>
                    </a>
                @endif
            </div>

            <!-- 3. CARD PENSIUN -->
            <div class="relative h-full group">
                @if($isAnyPending && !$pendingPensiunManager)
                    <div class="absolute inset-0 z-20 bg-red-50/30 rounded-md flex flex-col items-center justify-center border-2 border-red-200 cursor-not-allowed">
                        <div class="bg-red-600 text-white p-2 rounded-full shadow-lg mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z""")/>></svg>
                        </div>
                        <span class="text-[10px] font-black text-red-600 uppercase tracking-widest bg-white px-3 py-1 rounded-full shadow-md border border-red-200">Akses Terkunci</span>
                    </div>
                @endif

                @if($pendingPensiunManager)
                    <div class="bg-white p-4 rounded-xl shadow-md border-2 border-yellow-500 flex flex-col items-center text-center h-full ring-4 ring-yellow-50">
                        <div class="h-12 w-12 bg-yellow-100 rounded-full mb-2 flex items-center justify-center"><img src="{{ asset('images/ico/work-retirement.svg') }}" class="w-8 h-8"></div>
                        <p class="font-bold text-gray-800 text-sm">Pensiun</p>
                        <div class="mt-2 px-3 py-1 bg-yellow-500 rounded-full animate-pulse shadow-sm"><p class="text-[9px] font-black text-white uppercase tracking-widest">Sedang Diproses</p></div>
                    </div>
                @else
                    <a href="{{ route('pensiun.formPensiun') }}" class="block h-full group">
                        <div class="bg-white p-4 rounded-xl shadow-sm hover:shadow-xl hover:bg-yellow-50 transition-all duration-300 flex flex-col items-center text-center h-full border border-gray-100 group-hover:border-yellow-300">
                            <div class="h-12 w-12 bg-yellow-50 rounded-full mb-2 flex items-center justify-center group-hover:scale-110 transition-transform"><img src="{{ asset('images/ico/work-retirement.svg') }}" class="w-8 h-8"></div>
                            <p class="font-semibold text-gray-700 text-sm">Pensiun</p>
                            <p class="text-[10px] text-gray-400 mt-1 italic font-medium">Klik untuk buat pengajuan</p>
                        </div>
                    </a>
                @endif
            </div>

            <!-- 4. CARD PANGKAT, GAJI & TUNJANGAN -->
            <div class="relative h-full group">
                @if($isAnyPending && !$pendingPangkatManager)
                    <div class="absolute inset-0 z-20 bg-red-50/30 rounded-md flex flex-col items-center justify-center border-2 border-red-200 cursor-not-allowed">
                        <div class="bg-red-600 text-white p-2 rounded-full shadow-lg mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z""")/>></svg>
                        </div>
                        <span class="text-[10px] font-black text-red-600 uppercase tracking-widest bg-white px-3 py-1 rounded-full shadow-md border border-red-200">Akses Terkunci</span>
                    </div>
                @endif

                @if($pendingPangkatManager)
                    <div class="bg-white p-4 rounded-xl shadow-md border-2 border-green-500 flex flex-col items-center text-center h-full ring-4 ring-green-50">
                        <div class="h-12 w-12 bg-green-100 rounded-full mb-2 flex items-center justify-center"><img src="{{ asset('images/ico/promotion-in-salary.svg') }}" class="w-8 h-8"></div>
                        <p class="font-bold text-gray-800 text-sm">Pangkat & Gaji</p>
                        <div class="mt-2 px-3 py-1 bg-green-500 rounded-full animate-pulse shadow-sm"><p class="text-[9px] font-black text-white uppercase tracking-widest">Sedang Diproses</p></div>
                    </div>
                @else
                    <a href="{{ route('kenaikanpangkatgajitunjangan.pangkatgajitunjangan') }}" class="block h-full group">
                        <div class="bg-white p-4 rounded-xl shadow-sm hover:shadow-xl hover:bg-green-50 transition-all duration-300 flex flex-col items-center text-center h-full border border-gray-100 group-hover:border-green-300">
                            <div class="h-12 w-12 bg-green-50 rounded-full mb-2 flex items-center justify-center group-hover:scale-110 transition-transform"><img src="{{ asset('images/ico/promotion-in-salary.svg') }}" class="w-8 h-8"></div>
                            <p class="font-semibold text-gray-700 text-sm">Pangkat & Gaji</p>
                            <p class="text-[10px] text-gray-400 mt-1 italic font-medium">Klik untuk buat pengajuan</p>
                        </div>
                    </a>
                @endif
            </div>

            <!-- 5. CARD PENGHARGAAN (SELALU ENABLED) -->
            <div class="h-full">
                <a href="#" class="block h-full group">
                    <div class="bg-white p-4 rounded-l-md shadow-sm hover:shadow-xl hover:bg-purple-50 transition-all duration-300 flex flex-col items-center text-center h-full border border-gray-100 group-hover:border-purple-300">
                        <div class="h-12 w-12 bg-purple-50 rounded-full mb-2 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <img src="{{ asset('images/ico/service-award.svg') }}" class="w-8 h-8">
                        </div>
                        <p class="font-semibold text-gray-700 text-sm">Masa Kerja</p>
                        {{-- <p class="text-[10px] text-gray-400 mt-1 italic font-medium">Klik untuk buat pengajuan</p> --}}
                    </div>
                </a>
            </div>

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
