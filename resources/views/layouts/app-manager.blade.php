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
    </style>

</head>
<body class="flex flex-col min-h-screen bg-moving">
    <!-- Menghapus overflow-hidden dari app-container -->
    <div id="app-container" class="flex h-screen">
        <!-- Sidebar - Dimulai dengan lebar default W-72 -->
        <aside id="sidebar" class="w-68 bg-gray-800 text-white flex flex-col shadow-lg transition-all duration-300 ease-in-out rounded-r-md">
            <div class="p-4 flex items-center justify-between h-20 shadow-md shadow-white-50">
                <div class="p-8 flex justify-center items-center">
                    <img src="{{ asset('images/logoputih.png') }}" alt="Logo Bank Kota Bogor" class="h-11 w-auto">
                </div>
                {{-- <div id="logo-section" class="flex items-center">
                    <span class="text-2xl font-bold mr-3 text-sky-400">S</span>
                    <span id="bank-name" class="text-sm font-semibold whitespace-nowrap">Bank Kota Bogor</span>
                </div> --}}
                {{-- <button id="sidebar-toggle-btn" class="text-black p-1 rounded-full hover:bg-sky-400 transition duration-150 focus:outline-none cursor-pointer">
                    <svg id="arrow-icon-collapse" xmlns="http://www.w3.org" class="h-4 w-4 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </button> --}}
            </div>

            <!-- Menghapus overflow-y-auto dari sidebar-content -->
            <nav id="sidebar-content" class="flex-1 px-2 py-4 space-y-2">
                <div class="text-xs font-semibold uppercase tracking-wider text-white pt-3 sidebar-text">Navigasi</div>
                <!-- Garis batas horizontal antara logo dan menu -->
                {{-- <hr class="border border-gray-100 shadow-md flex"> --}}
                <a href="{{ Auth::user()->dashboard_link }}"
                    class="flex items-center justify-between p-3 text-xs font-medium rounded-lg hover:bg-amber-500 hover:text-white transition duration-150">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2-2m0 0l7-7 7 7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="sidebar-text whitespace-nowrap">Dashboard</span>
                    </span>
                    <!-- Icon Chevron: class group-hover:text-white tetap berfungsi karena parent-nya sudah benar -->
                    <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 group-hover:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>

                <!-- Menu Manajemen Pengajuan (Flyout Horizontal Hanya CSS) -->
                <div class="relative group">
                    <!-- Tombol Induk (Area hover yang memicu flyout) -->
                    <a href="#" class="flex items-center justify-between p-3 text-xs font-medium rounded-lg hover:bg-amber-500 hover:text-white transition duration-150 cursor-pointer">
                        <span class="flex items-center">
                            <!-- Icon Utama -->
                            <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                            </svg>
                            <span class="sidebar-text whitespace-nowrap">Manajemen Pengajuan</span>
                        </span>
                        <!-- Icon Chevron: class group-hover:text-white tetap berfungsi karena parent-nya sudah benar -->
                        <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 group-hover:text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>

                    <!-- Submenu Flyout (Menggunakan CSS Murni) -->
                    <div class="absolute left-full top-0 ml-4 w-52 bg-gray-800 shadow-lg rounded-r-lg invisible group-hover:visible opacity-0 group-hover:opacity-100 transition duration-300 ease-in-out z-50">
                        <!-- Setiap item sekarang menjadi link flat di dalam flyout -->
                        <a href="{{ url('/manager/pengajuanmanager') }}" class="block p-2 text-xs hover:bg-amber-500 hover:text-white font-semibold rounded-tr-lg">Pengajuan Saya</a>
                        <a href="{{ url('/manager/manajemenpengajuanmanager') }}" class="block p-2 text-xs hover:bg-amber-500 hover:text-white font-semibold rounded-br-lg">Approval Pengajuan Pegawai</a>
                    </div>
                </div>

                <div class="relative group">
                    <!-- Tombol Induk (Area hover yang memicu flyout) -->
                    <a href="{{ route('manager.pegawaidivisi') }}" class="flex items-center justify-between p-3 text-xs font-medium rounded-lg hover:bg-amber-500 hover:text-white transition duration-150 cursor-pointer">
                        <span class="flex items-center">
                            <!-- Icon Utama -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 mr-2 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                            </svg>

                            {{-- Teks Dinamis: Mengambil dari Relasi Divisi User --}}
                            <span class="sidebar-text whitespace-nowrap overflow-hidden text-ellipsis">
                                Data Pegawai Divisi {{ auth()->user()->divisi->nama_divisi ?? 'Divisi' }}
                            </span>
                        </span>

                        <!-- Icon Chevron -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 shrink-0 ml-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>

                    <!-- Submenu Flyout (Menggunakan CSS Murni) -->
                    {{-- <div class="absolute left-full top-0 ml-4 w-52 bg-gray-800 shadow-lg rounded-r-lg invisible group-hover:visible opacity-0 group-hover:opacity-100 transition duration-300 ease-in-out z-50">
                        <!-- Setiap item sekarang menjadi link flat di dalam flyout -->
                        <a href="#" class="block p-2 text-sm hover:bg-amber-500 hover:text-white font-semibold rounded-tr-lg">Data Pegawai</a>
                        <a href="#" class="block p-2 text-sm hover:bg-amber-500 hover:text-white font-semibold">Data Absensi Kehadiran</a>
                        <a href="#" class="block p-2 text-sm hover:bg-amber-500 hover:text-white font-semibold rounded-br-lg">Data Tugas/ Dinas</a>
                    </div> --}}
                </div>

                <!-- Bagian Absensi Saya -->
                <div class="text-xs font-semibold uppercase tracking-wider text-white pt-3 sidebar-text">Absensi</div>
                    <div class="relative group">
                    <!-- Tombol Induk (Area hover yang memicu flyout) -->
                    <a href="#" class="flex items-center justify-between p-3 text-xs font-medium rounded-lg hover:bg-amber-500 hover:text-white transition duration-150 cursor-pointer">
                        <span class="flex items-center">
                            <!-- Icon Utama -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                            <span class="sidebar-text whitespace-nowrap">Data Absensi Saya</span>
                        </span>
                        <!-- Icon Chevron: class group-hover:text-white tetap berfungsi karena parent-nya sudah benar -->
                        {{-- <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 group-hover:text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                        </svg> --}}
                    </a>
                </div>

                <!-- Bagian Laporan -->
                <div class="text-xs font-semibold uppercase tracking-wider text-white pt-3 sidebar-text">Laporan</div>
                <div class="relative group" id="laporan-menu-container">
                    <a href="#" class="flex items-center justify-between p-3 text-xs font-medium rounded-lg hover:bg-amber-500 hover:text-white transition duration-150 cursor-pointer">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                            </svg>
                            <span class="sidebar-text whitespace-nowrap">Absensi Pegawai Divisi {{ auth()->user()->divisi->nama_divisi ?? 'Divisi' }}</span>
                        </span>
                        <!-- Icon Chevron: Tambahkan class 'group-hover:text-white' di sini -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 group-hover:text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                    <a href="{{ route('laporan.index') }}" class="flex items-center justify-between p-3 text-xs font-medium rounded-lg hover:bg-amber-500 hover:text-white transition duration-150 cursor-pointer">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <span class="sidebar-text whitespace-nowrap">Pengajuan Pegawai Divisi {{ auth()->user()->divisi->nama_divisi ?? 'Divisi' }}</span>
                        </span>
                        <!-- Icon Chevron: Tambahkan class 'group-hover:text-white' di sini -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3 group-hover:text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Konten Utama -->
        <!-- Menghapus overflow-hidden dari kontainer konten utama -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white rounded-l-md shadow-md ml-2">
                <!-- Konten Atas: Profil & Notifikasi -->
                <!-- Garis pembatas (border-b) sekarang ada di sini, membentang penuh -->
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <h1 class="text-sm font-semibold text-gray-800 px-4">
                        <div id="tanggal-statis">
                            <?php
                                // Tetapkan zona waktu ke Jakarta (WIB)
                                date_default_timezone_set('Asia/Jakarta');

                                $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni','Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                                $dayOfWeek = date('w');
                                $dayOfMonth = date('d');
                                $monthOfYear = date('n') - 1;
                                $year = date('Y');

                                $formattedDate = $hari[$dayOfWeek] . ', ' . $dayOfMonth . ' ' . $bulan[$monthOfYear] . ' ' . $year;

                                echo $formattedDate;
                            ?>
                            / <span id="jam-dinamis"></span> WIB
                        </div>
                    </h1>
                    <div class="flex items-center space-x-4 px-4">
                        <!-- Notifikasi -->
                        <div class="relative" x-data="{ openNotify: false }">
                            <button @click="openNotify = ! openNotify" class="relative p-2 rounded-full bg-blue-50 hover:bg-blue-100 text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <!-- Icon Bell -->
                                <x-heroicon-c-bell-alert class="h-6 w-6 text-blue-400" />
                            </button>
                        </div>

                        <!-- Divider Vertikal -->
                        <div class="border-l border-gray-200 h-10"></div>
                        <!-- Profil & Dropdown Container (pastikan ini memiliki 'relative') -->
                        <div class="relative">

                            <!-- Checkbox Tersembunyi -->
                            <input type="checkbox" id="dropdown-toggle" class="hidden">

                            <!-- Label membungkus area profil, berfungsi sebagai tombol klik untuk checkbox -->
                            <label for="dropdown-toggle" class="flex items-center space-x-2 cursor-pointer p-2">
                                {{-- Container untuk Foto Profil atau Placeholder --}}
                                <div class="h-10 w-10 bg-gray-100 rounded-full overflow-hidden flex items-center justify-center">

                                    @if(Auth::user()->detailPribadi && Auth::user()->detailPribadi->photo_selfie)
                                        <img src="{{ asset('storage/' . Auth::user()->detailPribadi->photo_selfie) }}?v={{ time() }}"
                                            class="h-full w-full object-cover"
                                            alt="Foto Profil">
                                    @else
                                        <x-heroicon-x-person-profile class="h-8 w-8 text-gray-400 group-hover:text-yellow-500" />
                                    @endif

                                </div>
                                <div class="flex flex-col text-sm">
                                    <span class="font-small text-gray-800">{{ Auth::user()->name ?? 'User' }}</span>
                                    <span class="text-xs text-blue-600">Aktif</span>
                                </div>
                                <!-- Icon Dropdown -->
                                <svg xmlns="www.w3.org" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </label>

                            <!-- Menu Dropdown (Harus menjadi sibling dari checkbox dan label) -->
                            <!-- Menu ini awalnya disembunyikan dengan kelas 'hidden' dari Tailwind -->
                            <div id="dropdown-menu" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-20 hidden">

                                <!-- Bagian Pengaturan Akun -->
                                <div class="p-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase px-4 py-2">Pengaturan Akun</p>

                                    @php
                                        $userAuth = Auth::user();
                                        $isDataIncomplete = empty($userAuth->nomor_urut_pegawai) || empty($userAuth->email);
                                    @endphp

                                    <!-- Tautan Data Diri dengan Validasi -->
                                    @if($isDataIncomplete)
                                        <!-- Tampilan Notifikasi Warning Jika Data Belum Lengkap -->
                                        <div class="mx-2 mb-2 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded-md shadow-sm">
                                            <div class="flex">
                                                <div class="shrink-0">
                                                    <!-- Menggunakan Heroicon untuk indikasi peringatan -->
                                                    <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-yellow-500" />
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-xs text-yellow-700 leading-relaxed">
                                                        <strong>Perhatian:</strong> Nomor pegawai atau email belum terdaftar.
                                                        Silakan lengkapi profil Anda.
                                                        <br>
                                                        <a href="{{ route('profile.edit', ['form_type' => 'new']) }}" class="text-xs text-blue-700 hover:text-green-600 font-bold underline decoration-2 underline-offset-2">
                                                            Isi Data Diri Sekarang
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Menu Data Diri (Status: Belum Diisi / Mode New) -->
                                        <a href="{{ route('profile.edit', ['form_type' => 'new']) }}" class="flex items-center px-4 py-2 text-sm text-gray-400 hover:bg-gray-100 rounded-md group">
                                            <x-heroicon-o-identification class="h-5 w-5 mr-3 text-gray-400 group-hover:text-yellow-500" />
                                            <span class="flex-1">Data Diri</span>
                                            <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-medium">Lengkapi</span>
                                        </a>
                                    @else
                                        <!-- Tampilan Menu Normal Saat Data Sudah Lengkap (Mode Edit) -->
                                        <a href="{{ route('profile.edit', ['form_type' => 'edit']) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md group">
                                            <x-heroicon-o-identification class="h-5 w-5 mr-3 text-blue-400 group-hover:text-blue-600" />
                                            Data Diri
                                            <x-heroicon-s-arrow-small-right class="h-5 w-4 ml-auto text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity" />
                                        </a>
                                    @endif

                                    <!-- Tautan Ubah Password (Tetap Tersedia) -->
                                    <a href="{{ url('/change-password') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md group">
                                        <x-heroicon-o-lock-closed class="h-5 w-5 mr-3 text-blue-400 group-hover:text-blue-600" />
                                        Ubah Kata Sandi
                                        <x-heroicon-s-arrow-small-right class="h-5 w-4 ml-auto text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity" />
                                    </a>
                                </div>

                                <!-- Tautan Logout -->
                                <div class="p-2 border-t border-gray-200">
                                    <a href="{{ url('/logout') }}" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md w-full text-left group">
                                        <x-heroicon-o-x-circle class="h-5 w-5 mr-3 text-red-600 group-hover:scale-110 transition-transform" />
                                        Logout
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-b-lg">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center">
                            @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
                                @foreach ($breadcrumbs as $title => $url)
                                    <li class="inline-flex items-center">
                                        {{-- Tambahkan separator jika bukan item pertama --}}
                                        @if(!$loop->first)
                                            <span class="mx-2 text-gray-400">/</span>
                                        @endif

                                        @if($url)
                                            {{-- Jika ada URL, render sebagai link --}}
                                            <a href="{{ $url }}" class="text-gray-700 hover:text-blue-600 flex items-center text-sm font-medium">
                                                @if($loop->first)
                                                    <x-heroicon-m-home class="h-5 w-5 mr-3" />
                                                @endif
                                                {{ $title }}
                                            </a>
                                        @else
                                            {{-- Jika URL null (halaman saat ini), render sebagai teks aktif --}}
                                            <span class="text-gray-700 font-semibold-medium flex items-center text-sm">
                                                @if($loop->first)
                                                    <x-heroicon-m-home class="h-5 w-5 mr-3" />
                                                @endif
                                                {{ $title }}
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            @else
                                {{-- DEFAULT: Tampilkan breadcrumb default jika $breadcrumbs tidak disetel di controller --}}
                                <li class="inline-flex items-center">
                                    <span class="text-gray-700 font-semibold-medium flex items-center text-sm">
                                        <x-heroicon-m-home class="h-5 w-5 mr-3" />
                                        Beranda
                                    </span>
                                </li>
                            @endif
                        </ol>
                    </nav>
                </div>
            </header>

            <!-- Page Content -->
            <main id="mainContent" class="flex-1 pt-2 pl-2 flex flex-col h-screen overflow-hidden">
                @yield('content')
            </main>

            @include('partials.modal-success')

        {{-- Pindahkan stack ke sini agar script dari file blade bisa terbaca dengan benar --}}
        @stack('scripts')
        </div>
    </div>

{{-- Tautan JavaScript yang benar --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
