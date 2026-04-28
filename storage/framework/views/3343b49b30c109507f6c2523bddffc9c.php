<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepegawaian Bank Kota Bogor</title>

    <!-- Sertakan CSS dan file JavaScript melalui Vite -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/sidebar-toggle.js', 'resources/js/flyout-edge-detection.js', 'resources/js/dashboard.js', 'resources/js/approval-handler.js', 'resources/js/app.js']); ?>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script defer src="https://unpkg.com/alpinejs@3.13.10/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://unpkg.com" defer></script>
    

    <link rel="icon" href="<?php echo e(asset('my-favicon.png')); ?>">
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
        <aside id="sidebar" class="w-72 bg-emerald-950 text-white flex flex-col shadow-2xl transition-all duration-300 ease-in-out rounded-r-2xl border-r border-emerald-800/30 h-screen overflow-hidden">
            <!-- Header Logo -->
            <div class="p-4 flex items-center justify-between h-20 border-b border-emerald-800/30">
                <div class="flex justify-center items-center w-full">
                    <img src="<?php echo e(asset('images/logoputih.png')); ?>" alt="Logo Bank Kota Bogor" class="h-9 w-auto object-contain filter drop-shadow-md">
                </div>
            </div>

            <!-- Konten Navigasi -->
            <nav id="sidebar-content" class="flex-1 px-3 py-3 space-y-1 overflow-y-auto scrollbar-none">
                <?php
                    // Mengambil nama jabatan secara dinamis dari user yang sedang login
                    $namaJabatanAsli = auth()->user()->jabatan->nama_jabatan ?? 'Jabatan Tidak Ditemukan';

                    // Normalisasi: jika mengandung "skk" atau "kepatuhan", kita seragamkan tulisannya
                    if (str_contains(strtolower($namaJabatanAsli), 'skk') || str_contains(strtolower($namaJabatanAsli), 'kepatuhan')) {
                        $jabatanTampil = 'Kepala Satker Kepatuhan & M.R.';
                    } else {
                        $jabatanTampil = $namaJabatanAsli;
                    }
                ?>

                <!-- Informasi Akses Level -->
                <div class="flex flex-col px-4 py-2.5 bg-emerald-900/40 rounded-xl border border-emerald-800/50 mb-2 shadow-inner">
                    <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-amber-500">
                        Akses Level
                    </span>
                    <span class="text-sm font-bold uppercase tracking-wide text-white mt-0.5 break-words leading-tight">
                        <?php echo e($jabatanTampil); ?>

                    </span>
                </div>

                <!-- Menu Dashboard Utama -->
                <a href="<?php echo e(auth()->user()->dashboard_link); ?>"
                    class="flex items-center justify-between p-2.5 text-xs font-semibold rounded-xl hover:bg-amber-500 hover:text-white transition-all duration-200 group">
                    <span class="flex items-center">
                        <svg xmlns="http://w3.org" class="h-5 w-5 mr-3 text-amber-500 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2-2m0 0l7-7 7 7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="sidebar-text whitespace-nowrap">Dashboard</span>
                    </span>
                </a>

                <!-- Menu Manajemen Pengajuan -->
                <div class="relative group">
                    <button class="w-full flex items-center justify-between p-2.5 text-xs font-semibold rounded-xl hover:bg-amber-500 hover:text-white transition-all duration-200 cursor-pointer group">
                        <span class="flex items-center">
                            <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 mr-3 text-amber-500 group-hover:text-white transition-colors">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                            </svg>
                            <span class="sidebar-text whitespace-nowrap">Manajemen Pengajuan</span>
                        </span>
                        <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-white/50 group-hover:text-white transform group-hover:rotate-45 transition-transform duration-200">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>

                    <!-- Submenu Dropdown -->
                    <div class="max-h-0 overflow-hidden group-hover:max-h-40 transition-all duration-300 ease-in-out bg-emerald-900/30 rounded-lg mx-1 border border-emerald-800/30">
                        <a href="<?php echo e(route('skkmr.pengajuanskkmr')); ?>" class="block p-2 text-xs font-semibold text-white/80 hover:bg-amber-500/10 hover:text-amber-500 transition-colors border-b border-emerald-800/20 pl-11">
                            Pengajuan Saya
                        </a>
                        <a href="<?php echo e(route('skkmr.manajemenpengajuan')); ?>" class="block p-2 text-xs font-semibold text-white/80 hover:bg-amber-500/10 hover:text-amber-500 transition-colors pl-11">
                            Approval Pengajuan
                        </a>
                    </div>
                </div>

                <!-- Menu Data Pegawai -->
                <div class="relative group">
                    <a href="#" class="flex items-center justify-between p-2.5 text-xs font-semibold rounded-xl hover:bg-amber-500 hover:text-white transition-all duration-200 cursor-pointer">
                        <span class="flex items-center overflow-hidden">
                            <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 mr-3 text-amber-500 group-hover:text-white transition-colors shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                            </svg>
                            <span class="sidebar-text whitespace-nowrap overflow-hidden text-ellipsis">Data Pegawai
                            </span>
                        </span>
                    </a>
                </div>

                <!-- Divider Sub-Judul -->
                <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-[0.15em] text-emerald-400/70 sidebar-text">
                    Menu Operasional
                </div>

                <!-- Menu Absensi -->
                <div class="relative group">
                    <a href="#" class="flex items-center justify-between p-2.5 text-xs font-semibold rounded-xl hover:bg-amber-500 hover:text-white transition-all duration-200 cursor-pointer">
                        <span class="flex items-center">
                            <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 mr-3 text-amber-500 group-hover:text-white transition-colors">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span class="sidebar-text whitespace-nowrap">Data Absensi Saya</span>
                        </span>
                    </a>
                </div>

                <!-- Divider Sub-Judul -->
                <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-[0.15em] text-emerald-400/70 sidebar-text">
                    Laporan
                </div>

                <!-- Menu Laporan Absensi Pegawai -->
                <a href="#" class="flex items-center justify-between p-2.5 text-xs font-semibold rounded-xl hover:bg-amber-500 hover:text-white transition-all duration-200 cursor-pointer group">
                    <span class="flex items-center overflow-hidden">
                        <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 mr-3 text-amber-500 group-hover:text-white transition-colors shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden text-ellipsis">Absensi Pegawai
                        </span>
                    </span>
                </a>

                <!-- Menu Laporan Pengajuan Pegawai -->
                <a href="<?php echo e(route('laporan.index')); ?>" class="flex items-center justify-between p-2.5 text-xs font-semibold rounded-xl hover:bg-amber-500 hover:text-white transition-all duration-200 cursor-pointer group">
                    <span class="flex items-center overflow-hidden">
                        <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 mr-3 text-amber-500 group-hover:text-white transition-colors shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden text-ellipsis">Pengajuan Pegawai
                        </span>
                    </span>
                </a>
            </nav>
        </aside>

        <!-- Konten Utama -->
        <div class="flex-1 flex flex-col">
            <!-- Header Utama -->
            <header class="bg-white rounded-l-md shadow-sm border border-gray-100 ml-2">
                <div class="flex justify-between items-center py-2.5">
                    <!-- Bagian Kiri: Tanggal & Jam -->
                    <div class="px-4">
                        <h1 class="text-xs font-semibold text-gray-700 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 flex items-center gap-2">
                            <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-emerald-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                            </svg>
                            <div id="tanggal-statis" class="tracking-wide">
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
                                ?>
                                / <span id="jam-dinamis" class="font-bold text-emerald-600"></span> WIB
                            </div>
                        </h1>
                    </div>

                    <!-- Bagian Kanan: Notifikasi & Profil -->
                    <div class="flex items-center space-x-3 px-4">
                        <!-- Tombol Notifikasi (Mengganti aksen biru ke Amber agar serasi) -->
                        <div class="relative" x-data="{ openNotify: false }">
                            <button @click="openNotify = ! openNotify" class="relative p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-colors">
                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-c-bell-alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 text-amber-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                <!-- Ping Indicator -->
                                <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                </span>
                            </button>
                        </div>

                        <!-- Pembatas Tipis -->
                        <div class="border-l border-gray-200 h-8"></div>

                        <!-- Dropdown Profil Mode Hover -->
                        <div class="relative group">
                            <div class="flex items-center space-x-3 cursor-pointer p-1.5 rounded-xl hover:bg-gray-50 transition-colors">
                                <div class="h-9 w-9 bg-emerald-100 rounded-full overflow-hidden flex items-center justify-center border-2 border-white shadow-sm">
                                    <?php if(Auth::user()->detailPribadi && Auth::user()->detailPribadi->photo_selfie): ?>
                                        <img src="<?php echo e(asset('storage/' . Auth::user()->detailPribadi->photo_selfie)); ?>?v=<?php echo e(time()); ?>" class="h-full w-full object-cover" alt="Foto Profil">
                                    <?php else: ?>
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-x-person-profile'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-7 w-7 text-emerald-600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col text-xs">
                                    <span class="font-bold text-gray-800"><?php echo e(Auth::user()->name ?? 'User'); ?></span>
                                    <span class="flex items-center gap-1.5 text-gray-500 font-medium mt-0.5">
                                        <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full inline-block"></span>
                                        Aktif
                                    </span>
                                </div>
                                <svg xmlns="http://w3.org" class="h-4 w-4 text-gray-400 group-hover:rotate-180 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <div class="absolute right-0 pt-2 w-60 z-20 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 ease-in-out">
                                <div class="bg-white rounded-xl shadow-xl py-1.5 border border-gray-100">
                                    <div class="p-2">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase px-3 py-2 tracking-wider">Pengaturan Akun</p>

                                        <?php
                                            $userAuth = Auth::user();
                                            $isDataIncomplete = empty($userAuth->nomor_urut_pegawai) || empty($userAuth->email);
                                        ?>

                                        <?php if($isDataIncomplete): ?>
                                            <div class="mx-2 mb-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                                <div class="flex">
                                                    <div class="shrink-0">
                                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-s-exclamation-triangle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 text-amber-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-xs text-amber-800 leading-relaxed font-medium">
                                                            Nomor pegawai atau email belum terdaftar.
                                                            <br>
                                                            <a href="<?php echo e(route('profile.edit', ['form_type' => 'new'])); ?>" class="text-emerald-600 hover:text-emerald-700 font-bold underline underline-offset-2">Lengkapi Sekarang</a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="<?php echo e(route('profile.edit', ['form_type' => 'new'])); ?>" class="flex items-center px-3 py-2 text-xs font-semibold text-gray-400 hover:bg-gray-50 rounded-lg group">
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-identification'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3 text-gray-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                <span class="flex-1">Data Diri</span>
                                                <span class="text-[10px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold">Lengkapi</span>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('profile.edit', ['form_type' => 'edit'])); ?>" class="flex items-center px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 rounded-lg group transition-colors">
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-identification'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3 text-emerald-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?> Data Diri
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-s-arrow-small-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-4 ml-auto text-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?php echo e(url('/change-password')); ?>" class="flex items-center px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 rounded-lg group transition-colors">
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-lock-closed'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3 text-emerald-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?> Ubah Kata Sandi
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-s-arrow-small-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-4 ml-auto text-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="p-2 border-t border-gray-100">
                                        <a href="<?php echo e(url('/logout')); ?>" class="flex items-center px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-lg w-full text-left group transition-colors">
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-x-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3 text-red-500 group-hover:scale-110 transition-transform']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                            Keluar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN BREADCRUMB -->
                <div class="px-4 py-2 text-xs font-semibold text-gray-600 bg-emerald-50/50 rounded-bl-md border border-emerald-100/50">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center">
                            <?php if(isset($breadcrumbs) && count($breadcrumbs) > 0): ?>
                                <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="inline-flex items-center">
                                        <?php if(!$loop->first): ?>
                                            <!-- Mengurangi margin pemisah agar lebih rapat dan pas -->
                                            <span class="mx-1.5 text-emerald-300">/</span>
                                        <?php endif; ?>

                                        <?php if($url): ?>
                                            <a href="<?php echo e($url); ?>" class="text-gray-600 hover:text-emerald-600 flex items-center transition-colors">
                                                <?php if($loop->first): ?>
                                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-m-home'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-4 w-4 mr-1.5 text-emerald-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                <?php endif; ?>
                                                <?php echo e($title); ?>

                                            </a>
                                        <?php else: ?>
                                            <span class="text-emerald-700 font-bold flex items-center">
                                                <?php if($loop->first): ?>
                                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-m-home'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-4 w-4 mr-1.5 text-emerald-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                                <?php endif; ?>
                                                <?php echo e($title); ?>

                                            </span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <li class="inline-flex items-center">
                                    <span class="text-emerald-700 font-bold flex items-center">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-m-home'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-4 w-4 mr-1.5 text-emerald-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                        Beranda
                                    </span>
                                </li>
                            <?php endif; ?>
                        </ol>
                    </nav>
                </div>
            </header>

            <!-- Konten Utama (Main) -->
            <main id="mainContent" class="flex-1 ml-2 mt-2 flex flex-col h-screen overflow-hidden">
                <?php echo $__env->yieldContent('content'); ?>
            </main>

            <?php echo $__env->make('partials.modal-success', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->yieldPushContent('scripts'); ?>
        </div>

    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        function updateJam() {
            const sekarang = new Date();
            const jam = String(sekarang.getHours()).padStart(2, '0');
            const menit = String(sekarang.getMinutes()).padStart(2, '0');
            const detik = String(sekarang.getSeconds()).padStart(2, '0');

            document.getElementById('jam-dinamis').innerText = `${jam}:${menit}:${detik}`;
        }

        // Jalankan fungsi setiap 1 detik
        setInterval(updateJam, 1000);

        // Jalankan langsung saat halaman dimuat
        updateJam();
    </script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/layouts/app-skkmr.blade.php ENDPATH**/ ?>