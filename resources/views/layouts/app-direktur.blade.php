<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepegawaian Bank Kota Bogor</title>

    <!-- Sertakan CSS dan file JavaScript melalui Vite -->
    @vite(['resources/css/app.css', 'resources/js/sidebar-toggle.js', 'resources/js/flyout-edge-detection.js', 'resources/js/dashboard.js', 'resources/js/approval-handler.js', 'resources/js/app.js'])

    {{-- Tautan CSS yang benar (sebaiknya di dalam tag <head>) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    {{-- Tautan CDN Tailwind CSS Play --}}
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <script src="https://cdn.jsdelivr.net"></script> --}}
    <script defer src="https://unpkg.com/alpinejs@3.13.10/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://unpkg.com" defer></script>
    {{-- Tautan JS Flatpickr yang benar --}}

    <link rel="icon" href="{{ asset('my-favicon.png') }}">
    <style>
        /* 1. Atur lebar scrollbar (untuk Chrome, Safari, dan Edge) */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px; /* Bikin tipis di sini */
        }

        /* 2. Bagian jalurnya (track) - dibuat transparan saja biar bersih */
        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        /* 3. Bagian batang yang digeser (thumb) */
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background-color: #d1d5db; /* Warna abu-abu muda (Tailwind gray-300) */
            border-radius: 20px;       /* Bikin ujungnya bulat (rounded) */
            border: 1px solid transparent; /* Kasih jarak dikit */
        }

        /* 4. Efek pas mouse di atas scrollbar (hover) */
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background-color: #9ca3af; /* Jadi agak gelap dikit pas di-hover */
        }

        /* Untuk Firefox (lebih simpel) */
        .overflow-y-auto {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
        }
        /* 1. Scrollbar halus agar tidak kaku saat menu banyak */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.5);
        }

        /* 2. Efek Cahaya (Glow) pada Ikon saat kursor di atas menu (Warna Berani) */
        .group:hover svg {
            filter: drop-shadow(0 0 5px rgba(245, 158, 11, 0.6));
        }

        /* 3. Jeda waktu halus agar Flyout Submenu tidak langsung hilang/muncul mendadak */
        .group-hover\:visible {
            transition-delay: 100ms;
        }
    </style>

</head>
<body class="flex flex-col min-h-screen bg-moving">
    <!-- Menghapus overflow-hidden dari app-container -->
    <div id="app-container" class="flex h-screen">
        <!-- Sidebar Container -->
        <aside id="sidebar" class="w-72 bg-gradient-to-b from-[#06101e] to-[#0b1523] text-white flex flex-col mt-2 ml-2 mr-2 transition-all duration-300 ease-in-out">
            <!-- Header Logo (Floating Card Style) -->
            <div class="p-5">
                <div class="flex justify-center items-center">
                    <img src="{{ asset('images/logoputih.png') }}" alt="Logo Bank Kota Bogor" class="h-10 w-auto filter drop-shadow-[0_4px_12px_rgba(59,130,246,0.3)]">
                </div>
            </div>

            <!-- Konten Navigasi -->
            <nav id="sidebar-content" class="flex-1 space-y-1.5 overflow-y-auto mt-3">
                @php
                    $namaJabatan = auth()->user()->jabatan->nama_jabatan ?? '-';
                @endphp

                <!-- Informasi Akses Level (Clean & Subtle) -->
                <div class="px-2 mb-2">
                    <span class="text-[9px] font-extrabold uppercase tracking-[0.3em] text-blue-400">Akses Level</span>
                    <div class="flex items-center mt-1 space-x-2">
                        <div class="h-2 w-2 ml-2 rounded-full bg-emerald-400 animate-pulse"></div>
                        <span class="text-xs font-semibold text-white/90 truncate">{{ $namaJabatan }}</span>
                    </div>
                </div>

                <!-- 1. Garis Pembatas Akses Level & Dashboard -->
                <div class="h-[1px] w-full bg-white/10 my-2 mt-2"></div>

                <!-- Menu Dashboard Utama (Standar / No Highlight) -->
                <a href="{{ Auth::user()->dashboard_link }}"
                    class="flex items-center justify-between p-3.5 text-xs font-semibold rounded-xl transition-all duration-300 text-white/70 hover:bg-white/5 hover:text-white border-l-4 border-transparent hover:border-white/10 group">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org" class="h-5 w-5 mr-3 text-white/40 group-hover:text-blue-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2-2m0 0l7-7 7 7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="sidebar-text tracking-wider text-md">Beranda</span>
                    </span>
                </a>

                <!-- Menu Manajemen Pengajuan (Hover Glow Effect) -->
                <div class="relative group">
                    <a href="#" class="flex items-center justify-between p-3.5 text-xs font-semibold rounded-xl transition-all duration-300 text-white/70 hover:bg-white/5 hover:text-white border-l-4 border-transparent hover:border-white/10 group">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 mr-3 text-white/40 group-hover:text-blue-400 transition-colors">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                            </svg>
                            <span class="sidebar-text truncate text-md tracking-wider">Manajemen Pengajuan Pegawai</span>
                        </span>
                        <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3 opacity-30 group-hover:opacity-100 group-hover:text-blue-400 group-hover:rotate-90 transition-all duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>

                    <!-- Submenu -->
                    <div class="max-h-0 overflow-hidden group-hover:max-h-20 transition-all duration-500 ease-in-out bg-white/[0.02] rounded-xl mx-2 mt-1">
                        <a href="{{ url('/direktur/manajemenpengajuan') }}" class="block py-3 pl-10 pr-4 text-[11px] font-medium text-white/60 hover:text-blue-400 transition-colors">
                            Persetujuan Pengajuan
                        </a>
                    </div>
                </div>

                <!-- MENU DATA PEGAWAI -->
                <div class="relative group">
                    <a href="{{ route('pegawai.data') }}" class="flex items-center justify-between p-3.5 text-xs font-semibold rounded-xl transition-all duration-300 text-white/70 hover:bg-white/5 hover:text-white border-l-4 border-transparent hover:border-white/10 group">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 mr-3 text-white/40 group-hover:text-blue-400 transition-colors shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                            </svg>
                            <span class="sidebar-text truncate text-md tracking-wider">Data Pegawai</span>
                        </span>
                        <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3 shrink-0 opacity-30 group-hover:opacity-100 group-hover:text-blue-400 transition-all">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>

                <!-- 2. Garis Pembatas Atas Laporan -->
                <div class="h-[1px] w-full bg-white/10 mt-5 my-2"></div>

                <!-- Bagian Laporan (Teks di-highlight biru) -->
                <div class="text-[9px] font-extrabold uppercase tracking-[0.3em] text-blue-400 pt-1 px-2 pb-2">Laporan</div>

                <div class="relative group">
                    <a href="{{ route('laporan.index') }}" class="flex items-center justify-between p-3.5 text-xs font-semibold rounded-xl transition-all duration-300 text-white/70 hover:bg-white/5 hover:text-white border-l-4 border-transparent hover:border-white/10 group">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 mr-3 text-white/40 group-hover:text-blue-400 transition-colors">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <span class="sidebar-text truncate text-md tracking-wider">Pengajuan Pegawai</span>
                        </span>
                    </a>
                </div>
            </nav>
        </aside>


        <!-- Konten Utama -->
        <!-- Menghapus overflow-hidden dari kontainer konten utama -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white/80 backdrop-blur-md rounded-l-md border border-gray-100 shadow-lg mt-2 sticky top-2 z-50">
                <!-- BARIS ATAS: Navigasi Utama, Jam & Profil (Lebih Tipis) -->
                <div class="flex justify-between items-center py-1.5 px-4">
                    <!-- Bagian Tanggal dan Jam -->
                    <h1 class="text-xs font-semibold text-gray-700">
                        <div id="tanggal-statis" class="flex items-center">
                            <svg xmlns="http://w3.org" class="h-4 w-4 text-blue-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <?php
                                date_default_timezone_set('Asia/Jakarta');
                                $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni','Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $dayOfWeek = date('w');
                                $dayOfMonth = date('d');
                                $monthOfYear = date('n') - 1;
                                $year = date('Y');
                                $formattedDate = $hari[$dayOfWeek] . ', ' . $dayOfMonth . ' ' . $bulan[$monthOfYear] . ' ' . $year;
                                echo $formattedDate;
                            ?> / <span id="jam-dinamis" class="text-blue-600 font-bold ml-0.5"></span>&nbsp;WIB
                        </div>
                    </h1>

                    <div class="flex items-center space-x-3">
                        <!-- Notifikasi -->
                        <div class="relative" x-data="{ openNotify: false }">
                            <button @click="openNotify = ! openNotify" class="relative p-1.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-500 focus:outline-none">
                                <x-heroicon-c-bell-alert class="h-4.5 w-4.5 text-blue-500" />
                                <span class="absolute top-1 right-1 h-1.5 w-1.5 bg-red-500 rounded-full"></span>
                            </button>
                        </div>

                        <div class="border-l border-gray-200 h-5"></div>

                        <!-- Profil & Dropdown -->
                        <div class="relative group py-1">
                            <div class="flex items-center space-x-2 cursor-pointer p-1 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="flex flex-col text-xs text-right">
                                    <span class="font-semibold text-gray-800 leading-none">{{ Auth::user()->name ?? 'User' }}</span>
                                    <span class="text-[10px] text-emerald-600 font-bold mt-0.5">Aktif</span>
                                </div>
                                <div class="h-8 w-8 bg-gray-100 rounded-full overflow-hidden flex items-center justify-center border border-gray-200">
                                    @if(Auth::user()->detailPribadi && Auth::user()->detailPribadi->photo_selfie)
                                        <img src="{{ asset('storage/' . Auth::user()->detailPribadi->photo_selfie) }}?v={{ time() }}" class="h-full w-full object-cover" alt="Foto Profil">
                                    @else
                                        <x-heroicon-x-person-profile class="h-5 w-5 text-gray-400 group-hover:text-yellow-500" />
                                    @endif
                                </div>
                                <svg xmlns="http://w3.org" class="h-3.5 w-3.5 text-gray-400 group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 top-full mt-0 w-60 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-20 invisible opacity-0 group-hover:visible group-hover:opacity-100 translate-y-1 group-hover:translate-y-0 transition-all duration-300 ease-in-out">
                                <div class="p-1.5">
                                    <p class="text-[9px] font-bold text-gray-400 uppercase px-3 py-1.5 tracking-wider">Pengaturan Akun</p>
                                    @php
                                        $userAuth = Auth::user();
                                        $isDataIncomplete = empty($userAuth->nomor_urut_pegawai) || empty($userAuth->email);
                                    @endphp

                                    @if($isDataIncomplete)
                                        <div class="mx-1.5 mb-1.5 p-2 bg-yellow-50 border-l-4 border-yellow-400 rounded-md shadow-sm">
                                            <div class="flex">
                                                <div class="shrink-0">
                                                    <x-heroicon-s-exclamation-triangle class="h-4 w-4 text-yellow-500" />
                                                </div>
                                                <div class="ml-2">
                                                    <p class="text-[11px] text-yellow-700 leading-tight">
                                                        <strong>Perhatian:</strong> Lengkapi profil Anda. <br>
                                                        <a href="{{ route('profile.edit', ['form_type' => 'new']) }}" class="text-blue-700 hover:text-green-600 font-bold underline">
                                                            Isi Sekarang
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('profile.edit', ['form_type' => 'new']) }}" class="flex items-center px-3 py-1.5 text-xs text-gray-500 hover:bg-gray-50 rounded-md group">
                                            <x-heroicon-o-identification class="h-4.5 w-4.5 mr-2 text-gray-400 group-hover:text-yellow-500" />
                                            <span class="flex-1">Data Diri</span>
                                            <span class="text-[9px] bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded font-medium">Lengkapi</span>
                                        </a>
                                    @else
                                        <a href="{{ route('profile.edit', ['form_type' => 'edit']) }}" class="flex items-center px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 rounded-md group">
                                            <x-heroicon-o-identification class="h-4.5 w-4.5 mr-2 text-blue-500 group-hover:text-blue-600" />
                                            Data Diri
                                            <x-heroicon-s-arrow-small-right class="h-4 w-4 ml-auto text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity" />
                                        </a>
                                    @endif

                                    <a href="{{ url('/change-password') }}" class="flex items-center px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50 rounded-md group">
                                        <x-heroicon-o-lock-closed class="h-4.5 w-4.5 mr-2 text-blue-500 group-hover:text-blue-600" />
                                        Ubah Kata Sandi
                                        <x-heroicon-s-arrow-small-right class="h-4 w-4 ml-auto text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity" />
                                    </a>
                                </div>

                                <div class="p-1.5 border-t border-gray-100">
                                    <a href="{{ url('/logout') }}" class="flex items-center px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 rounded-md w-full text-left group">
                                        <x-heroicon-o-x-circle class="h-4.5 w-4.5 mr-2 text-red-600 group-hover:scale-105 transition-transform" />
                                        Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BARIS BAWAH: Breadcrumb Terintegrasi (Lebih Tipis) -->
                <div class="flex items-center px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-50/50 border-t border-gray-100 rounded-bl-md">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center">
                            @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
                                @foreach ($breadcrumbs as $title => $url)
                                    <li class="inline-flex items-center">
                                        @if(!$loop->first)
                                            <span class="mx-1.5 text-gray-400">/</span>
                                        @endif
                                        @if($url)
                                            <a href="{{ $url }}" class="text-gray-600 hover:text-blue-600 flex items-center text-xs font-medium transition-colors">
                                                @if($loop->first)
                                                    <x-heroicon-m-home class="h-4 w-4 mr-1.5 text-gray-400" />
                                                @endif
                                                {{ $title }}
                                            </a>
                                        @else
                                            <span class="text-gray-800 font-semibold flex items-center text-xs">
                                                @if($loop->first)
                                                    <x-heroicon-m-home class="h-4 w-4 mr-1.5 text-gray-400" />
                                                @endif
                                                {{ $title }}
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            @else
                                <li class="inline-flex items-center">
                                    <span class="text-gray-800 font-semibold flex items-center text-xs">
                                        <x-heroicon-m-home class="h-4 w-4 mr-1.5 text-gray-400" />
                                        Beranda
                                    </span>
                                </li>
                            @endif
                        </ol>
                    </nav>
                </div>
            </header>


            <!-- Page Content -->
            <main id="mainContent" class="flex-1 pt-2 flex flex-col h-screen overflow-hidden">
                @yield('content')
            </main>

            @include('partials.modal-success')

        {{-- Pindahkan stack ke sini agar script dari file blade bisa terbaca dengan benar --}}
        @stack('scripts')
        </div>
    </div>

{{-- Tautan JavaScript yang benar --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    function updateJam() {
        const waktu = new Date();
        const jam = String(waktu.getHours()).padStart(2, '0');
        const menit = String(waktu.getMinutes()).padStart(2, '0');
        const detik = String(waktu.getSeconds()).padStart(2, '0');

        document.getElementById('jam-dinamis').innerText = `${jam}:${menit}:${detik}`;
    }

    // Jalankan fungsi setiap detik
    setInterval(updateJam, 1000);

    // Panggil langsung saat halaman dimuat
    updateJam();
</script>

</body>
</html>
