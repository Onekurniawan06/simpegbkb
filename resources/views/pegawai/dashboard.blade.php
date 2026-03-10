@extends('layouts.app-pegawai')

@section('content')

    <!-- Ini adalah satu-satunya elemen root sekarang -->
    <!-- Ini adalah satu-satunya elemen root sekarang dengan padding yang dikurangi -->
    @if(session('warning'))
        <div class="alert alert-warning rounded-lg shadow-sm mb-2 py-2" role="alert" style="background-color: #fff3cd; color: #856404; border-left: 5px solid #ffc107 !important;">
            <!-- Container utama untuk seluruh konten dalam satu baris -->
            <div class="d-flex align-items-center justify-content-between">

                <!-- Kontainer Kiri: Ikon, Judul, Pesan, Teks Tautan -->
                <div class="d-flex align-items-center gap-2 text-sm text-gray-600 ml-2">
                    {{-- Profil Belum Lengkap! {{ session('warning') }} Klik Tautan <a href="{{ route('pegawai.profile', ['form_type' => 'new']) }}" class="text-sm text-gray-800 hover:text-green-400 font-semibold">Isi Data Diri</a> --}}
                    Profil Belum Lengkap! {{ session('warning') }} Klik Tautan <a href="{{ route('pegawai.profile', ['form_type' => 'new']) }}" class="text-sm text-gray-800 hover:text-green-400 font-semibold">Isi Data Diri</a> Nomor Urut Pegawai Anda: <strong>{{ auth()->user()->nomor_urut_pegawai }}</strong>.
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white p-2 md:p-4 rounded-l-md shadow-lg max-w-full mx-auto">

        <!-- Welcome Section (centered content below date) -->
        <div class="flex items-center justify-center flex-col text-center mb-2">
            <!-- Ukuran font diatur menjadi text-sm (14px) -->
            <h2 class="font-semibold text-gray-800 text-md">Selamat Datang, {{ Auth::user()->name ?? '' }} 👋</h2>
            <!-- Ukuran font diatur menjadi text-sm (14px) -->
            <p class="text-gray-600 text-sm">Mau Buat Pengajuan Apa?</p>
        </div>

        <!-- Garis pemisah horizontal -->
        <hr class="mb-2 border-gray-200">

        <!-- Menu Grid Section (5 columns) -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <!-- Card Cuti & Izin -->
            @if($hasPendingCuti)
                {{-- Tautan dinonaktifkan secara visual dan fungsional dengan teks inline --}}
                <div class="block h-full cursor-not-allowed group" title="Anda memiliki pengajuan cuti yang masih dalam proses persetujuan.">
                    <div class="bg-white p-4 rounded-lg shadow flex items-center flex-col text-center h-full opacity-50">
                        <div class="h-12 w-12 bg-orange-100 rounded-full mb-2">
                            <img src="{{ asset('images/ico/user-travel.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                        </div>
                        <p class="font-semibold text-gray-700 text-sm">Cuti & Izin</p>
                        {{-- Teks peringatan inline berwarna merah dan kecil --}}
                        <p class="text-xs text-red-500 mt-1 mb-2">Pengajuan sedang diproses</p>
                        {{-- Tambahan item menu untuk Lacak Pengajuan --}}
                        <a href="{{ route('datapengajuan.formDataPengajuan') }}" class="block h-full">
                            <div class="bg-white p-2 rounded-lg shadow hover:shadow-md hover:bg-blue-50 transition duration-300 cursor-pointer flex items-center flex-col text-center h-full">
                                <p class="font-semibold text-gray-700 text-xs">Lacak Pengajuan</p>
                            </div>
                        </a>
                    </div>
                </div>
            @else
                {{-- Tautan normal jika tidak ada yang pending --}}
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

            @if($hasPendingLembur)
                {{-- Tautan dinonaktifkan secara visual dan fungsional dengan teks inline --}}
                <div class="block h-full cursor-not-allowed group" title="Anda memiliki pengajuan Lembur yang masih dalam proses persetujuan.">
                    <div class="bg-white p-4 rounded-lg shadow flex items-center flex-col text-center h-full opacity-50">
                        <div class="h-12 w-12 bg-orange-100 rounded-full mb-2">
                            <img src="{{ asset('images/ico/over-time.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                        </div>
                        <p class="font-semibold text-gray-700 text-sm">Lembur</p>
                        {{-- Teks peringatan inline berwarna merah dan kecil --}}
                        <p class="text-xs text-red-500 mt-1 mb-2">Pengajuan sedang diproses</p>
                        {{-- Tambahan item menu untuk Lacak Pengajuan --}}
                        <a href="{{ route('datapengajuan.formDataPengajuan') }}" class="block h-full">
                            <div class="bg-white p-2 rounded-lg shadow hover:shadow-md hover:bg-green-50 transition duration-300 cursor-pointer flex items-center flex-col text-center h-full">
                                <p class="font-semibold text-gray-900 text-xs">Lacak Pengajuan</p>
                            </div>
                        </a>
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

            @if($hasPendingPensiun)
                {{-- Tautan dinonaktifkan secara visual dan fungsional dengan teks inline --}}
                <div class="block h-full cursor-not-allowed group" title="Anda memiliki pengajuan Pensiun yang masih dalam proses persetujuan.">
                    <div class="bg-white p-4 rounded-lg shadow flex items-center flex-col text-center h-full opacity-50">
                        <div class="h-12 w-12 bg-orange-100 rounded-full mb-2">
                            <img src="{{ asset('images/ico/work-retirement.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                        </div>
                        <p class="font-semibold text-gray-700 text-sm">Pensiun</p>
                        {{-- Teks peringatan inline berwarna merah dan kecil --}}
                        <p class="text-xs text-red-500 mt-1 mb-2">Pengajuan sedang diproses</p>
                        {{-- Tambahan item menu untuk Lacak Pengajuan --}}
                        <a href="{{ route('datapengajuan.formDataPengajuan') }}" class="block h-full">
                            <div class="bg-white p-2 rounded-lg shadow hover:shadow-md hover:bg-green-200 transition duration-300 cursor-pointer flex items-center flex-col text-center h-full">
                                <p class="font-semibold text-gray-900 text-xs">Lacak Pengajuan</p>
                            </div>
                        </a>
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
            {{-- <a href="{{ route('cuti.create') }}" class="block h-full"> --}}
                <div class="bg-white p-4 rounded-lg shadow hover:shadow-md hover:bg-red-50 transition duration-300 cursor-pointer flex items-center flex-col text-center h-full">
                    <div class="h-12 w-12 bg-yellow-100 rounded-full mb-2">
                        <img src="{{ asset('images/ico/service-award.svg') }}" alt="User Travel Icon" class="w-8 h-8 m-2 mt-2 text-gray-500">
                    </div>
                    <p class="font-semibold text-gray-700 text-sm">Penghargaan Masa Kerja</p>
                    <p class="text-xs text-gray-500 mt-1">Klik untuk buat pengajuan</p>
                </div>
            {{-- </a> --}}
        </div>
    </div>

    <!-- Informasi & Pengumuman Section || Kontainer Utama: Pastikan x-data membungkus SEMUA elemen termasuk Modal -->
    <div class="bg-white shadow-sm rounded-tl-md overflow-hidden border border-gray-100 mt-2 h-full flex flex-col">
        <div class="mb-4 p-4 shadow-sm">
            {{-- <div class="max-w-full mx-auto" x-data="{ openModal: null }" wire:poll.60m> --}}
            <!-- Container Utama Livewire & Alpine.js -->
            <div class="max-w-full mx-auto"
                x-data="{
                    openModal: null,
                    totalRead: 0,
                    totalUnread: 0,
                    hitungStatus() {
                        let readCount = 0;
                        // Hitung berapa item dari halaman ini yang sudah ditandai 'true' di localStorage
                        @foreach($daftar_berita as $berita)
                            if (localStorage.getItem('berita_{{ $berita->id_pengumuman }}') === 'true') {
                                readCount++;
                            }
                        @endforeach
                        this.totalRead = readCount;
                        // Hitung yang belum dibaca: total item di halaman dikurangi yang dibaca
                        this.totalUnread = {{ $daftar_berita->count() }} - readCount;
                    }
                }"
                x-init="hitungStatus()"
                wire:poll.60m>

                <!-- Header & Navigasi (Tampilan Rapi seperti gambar) -->
                <div class="flex flex-col sm:flex-row items-center justify-between mb-4 border-b border-gray-200 pb-2 gap-4">

                    <!-- Judul & Kedua Notifikasi -->
                    <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-megaphone class="h-5 w-5 text-indigo-600" />
                        <span>Informasi & Pengumuman</span>

                        <!-- Badge "Baru" (Menggunakan Alpine.js totalUnread) -->
                        <template x-if="totalUnread > 0">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full animate-pulse">
                                <span x-text="totalUnread" class="mr-1"></span> Informasi Baru
                            </span>
                        </template>

                        <!-- Badge "Dibaca" (Menggunakan Alpine.js totalRead) -->
                        {{-- <template x-if="totalRead > 0">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-green-600 rounded-full">
                                <span x-text="totalRead" class="mr-1"></span> Sudah dibaca
                            </span>
                        </template> --}}
                    </h3>

                    <!-- ... (Kode Navigasi tetap sama) ... -->
                    <div class="flex items-center gap-4">
                        <p class="hidden sm:block text-[13px] font-semibold text-gray-500 whitespace-nowrap">
                            Menampilkan {{ $daftar_berita->firstItem() }} - {{ $daftar_berita->lastItem() }} dari {{ $daftar_berita->total() }} data
                        </p>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
                            <a href="{{ $daftar_berita->previousPageUrl() ?? '#' }}" class="px-2 py-1.5 border-r hover:bg-gray-50 {{ $daftar_berita->onFirstPage() ? 'text-gray-300 pointer-events-none' : 'text-gray-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </a>
                            <span class="px-3 py-1.5 bg-gray-50 text-xs font-bold text-gray-700 border-r">{{ $daftar_berita->currentPage() }}</span>
                            <a href="{{ $daftar_berita->nextPageUrl() ?? '#' }}" class="px-2 py-1.5 hover:bg-gray-50 {{ !$daftar_berita->hasMorePages() ? 'text-gray-300 pointer-events-none' : 'text-gray-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Daftar Berita/Card Berita -->
                <div class="grid gap-4">
                    @forelse($daftar_berita as $berita)
                        <div x-data="{
                            isRead: localStorage.getItem('berita_{{ $berita->id_pengumuman }}') === 'true',
                            bacaBerita() {
                                this.isRead = true;
                                localStorage.setItem('berita_{{ $berita->id_pengumuman }}', 'true');
                                this.openModal = {{ $berita->id_pengumuman }};
                                // Panggil fungsi hitungStatus() agar badge atas langsung update
                                hitungStatus();
                            }
                        }" class="group p-3 border border-gray-200 rounded-xl shadow-sm bg-white hover:bg-gray-100 transition-all duration-300 relative">

                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-2">
                                <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                    {{ $berita->judul }}
                                    <template x-if="!isRead">
                                        <span class="flex h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                                    </template>
                                </h4>
                                <div class="flex items-center text-xs text-gray-400 whitespace-nowrap pt-1">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ \Carbon\Carbon::parse($berita->tanggal_posting)->shiftTimezone('Asia/Jakarta')->diffForHumans() }}
                                </div>
                            </div>

                            <p class="text-gray-600 text-sm leading-relaxed">
                                {{ Str::limit($berita->deskripsi_singkat, 150) }}
                                <button @click="bacaBerita()" class="text-indigo-600 font-semibold hover:text-indigo-800 ml-1 focus:outline-none">
                                    Baca selengkapnya...
                                </button>
                            </p>

                            <!-- Modal Detail Berita -->
                            <div x-show="openModal === {{ $berita->id_pengumuman }}" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>

                                <!-- Backdrop (Layar Gelap) -->
                                <div class="fixed inset-0 bg-gray-500/60 backdrop-blur-sm transition-opacity" @click="openModal = null"></div>

                                <!-- Konten Modal -->
                                <div class="relative min-h-screen flex items-center justify-center p-4">
                                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 overflow-hidden transform transition-all"
                                        x-show="openModal === {{ $berita->id_pengumuman }}"
                                        x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100">

                                        <!-- Header Modal -->
                                        <div class="flex justify-between items-start mb-6">
                                            <div class="bg-indigo-50 px-3 py-1 rounded-lg text-indigo-700 text-xs font-bold uppercase tracking-wider">
                                                Detail Pengumuman
                                            </div>
                                        </div>

                                        <h2 class="text-sm font-semibold text-gray-900 mb-4">{{ $berita->judul }}</h2>

                                        <div class="flex items-center text-sm text-gray-500 mb-4 pb-4 border-b border-gray-100">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Dipublikasikan pada: {{ \Carbon\Carbon::parse($berita->tanggal_posting)->format('d F Y - H:i') }} WIB
                                        </div>

                                        <!-- Isi Berita Lengkap -->
                                        <div class="prose max-w-none text-gray-700 leading-loose text-sm">
                                            {{ $berita->deskripsi_singkat }}
                                        </div>

                                        <!-- Footer Modal -->
                                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                                            <button @click="openModal = null" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-10">Tidak ada berita.</p>
                    @endforelse
                </div>

                <!-- Navigasi Pagination -->
                {{-- <div class="mt-8">
                    {{ $daftar_berita->links() }}
                </div> --}}
            </div>
        </div>
    </div>
    {{-- end Section informasi --}}

    <!-- TOMBOL BACK TO TOP (Ditempatkan di sini, akan melayang di atas mainContent) -->
    <button id="backToTop" style="display: none;" class="fixed bottom-10 right-10 bg-blue-300 text-white p-3 rounded-full shadow-2xl hover:bg-blue-600 transition-all duration-300 z-50 flex items-center justify-center" title="Kembali ke atas">
        <x-heroicon-o-chevron-up id="arrowIcon" class="h-6 w-6 " />
    </button>

@endsection

{{-- Skrip JavaScript yang menargetkan #mainContent --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const backToTopButton = document.getElementById('backToTop');
        const scrollableElement = document.getElementById('mainContent');

        if (!scrollableElement) {
            console.error('Elemen #mainContent tidak ditemukan.');
            return;
        }

        function toggleBackToTop() {
            // Cek posisi scroll dari elemen target
            if (scrollableElement.scrollTop > 100) { // Nilai diubah menjadi 100
                backToTopButton.style.display = 'flex';
            } else {
                backToTopButton.style.display = 'none';
            }
        }

        function scrollToTop() {
            scrollableElement.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        scrollableElement.addEventListener('scroll', toggleBackToTop);
        backToTopButton.addEventListener('click', scrollToTop);

        toggleBackToTop();
    });
</script>
@endpush

