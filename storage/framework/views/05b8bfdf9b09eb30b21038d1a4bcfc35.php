<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pegawai</title>
    <!-- Pastikan Anda telah menginstal Tailwind CSS dan mengkompilasi aset. -->
    <!-- Contoh menggunakan Vite: -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="<?php echo e(asset('js/pdf.js')); ?>"></script>
    <script src="<?php echo e(asset('js/pdf.worker.js')); ?>"></script>

</head>
<body class="flex flex-col min-h-screen bg-moving">

    <div class="flex h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg shrink-0 rounded-md m-2 self-start">
            <!-- Logo di tengah secara horizontal -->
            <div class="p-8 flex justify-center items-center">
                <img src="<?php echo e(asset('images/logobkb.png')); ?>" alt="Logo Bank Kota Bogor" class="h-11 w-auto">
            </div>

            <!-- Garis batas horizontal antara logo dan menu -->
            <hr class="border-8 border-gray-100">

            <nav class="mt-3 text-sm">
                <!-- Menu Beranda -->
                <a href="<?php echo e(Auth::user()->dashboard_link); ?>" class="flex items-center py-2 px-4 text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg mx-2 active:bg-blue-600">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-home'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3']); ?>
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
                </a>

                <!-- Menu Absen Kehadiran -->
                <a href="#" class="flex items-center py-2 px-4 text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg mx-2 active:bg-blue-600">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-clock'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3']); ?>
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
                    Absensi Kehadiran
                </a>

                <!-- Menu Dropdown/Accordion Data Aktivitas Pegawai -->
                <!-- Gunakan div untuk mengelompokkan item menu utama dan sub-menu -->
                <div class="mx-2 mb-6">
                    <!-- Tombol Menu Utama yang bisa di-klik untuk toggle sub-menu -->
                    <button id="toggleAktivitasPegawai" class="flex items-center justify-between w-full py-2 px-4 text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg focus:outline-none">
                        <span class="flex items-center">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-archive-box'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3']); ?>
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
                            Data Aktivitas Pegawai
                        </span>
                        <!-- Ikon panah yang akan berputar saat di-klik (chevron down/up) -->
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'arrowIcon','class' => 'h-4 w-4 transition-transform duration-300']); ?>
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
                    </button>

                    <!-- Sub-menu Container (awalnya tersembunyi dengan class hidden) -->
                    <div id="subMenuAktivitas" class="pl-8 mt-1 space-y-1 hidden">
                        <!-- Sub Menu Data Pengajuan -->
                        <a href="<?php echo e(route('datapengajuan.formDataPengajuan')); ?>" class="flex items-center py-2 px-3 text-gray-600 hover:bg-blue-400 hover:text-white rounded-lg transition duration-150 ease-in-out">
                            <!-- Anda bisa menambahkan ikon kecil di sini jika mau -->
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-book-open'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3']); ?>
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
                            Data Pengajuan
                        </a>

                        <!-- Sub Menu Data Absensi -->
                        <a href="#" class="flex items-center py-2 px-3 text-gray-600 hover:bg-blue-400 hover:text-white rounded-lg transition duration-150 ease-in-out">
                            <!-- Anda bisa menambahkan ikon kecil di sini jika mau -->
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-document-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3']); ?>
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
                            Data Absensi
                        </a>
                    </div>
                </div>
                <!-- ... menu lainnya ... -->
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col overflow-hidden min-h-screen w-full">
            <!-- Header/Navbar -->
            <header class="bg-white rounded-l-md shadow-md mt-2 mb-2">

                <!-- Konten Atas: Profil & Notifikasi -->
                <!-- Garis pembatas (border-b) sekarang ada di sini, membentang penuh -->
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
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
                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-c-bell-alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-6 w-6 text-blue-400']); ?>
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
                            </button>

                            <!-- Dropdown Menu Notifikasi -->
                            <div x-show="openNotify"
                                @click.outside="openNotify = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-100">

                                <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Berita Terbaru</h3>
                                    <span class="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full"><?php echo e($newNewsCount); ?> Baru</span>
                                </div>

                                <div class="max-h-80 overflow-y-auto">
                                    <?php $__empty_1 = true; $__currentLoopData = $latestNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $news): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <a href="<?php echo e(url('/berita/' . $news->id)); ?>" class="block px-4 py-3 hover:bg-blue-50 transition border-b border-gray-50 last:border-0">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-gray-800 line-clamp-1"><?php echo e($news->judul); ?></span>
                                                <span class="text-[11px] text-gray-500 mt-1 flex items-center">
                                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-clock'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-3 w-3 mr-1']); ?>
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
                                                    
                                                    <?php if($news->tanggal_posting): ?>
                                                        <?php echo e(\Carbon\Carbon::parse($news->tanggal_posting)->diffForHumans()); ?>

                                                    <?php else: ?>
                                                        Tanggal tidak tersedia
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        
                                    <?php endif; ?>
                                </div>

                                <div class="px-4 py-2 border-t border-gray-100">
                                    <a href="<?php echo e(url('/berita')); ?>" class="block text-center text-xs font-semibold text-blue-600 hover:text-blue-700 underline">
                                        Lihat Semua Berita
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Divider Vertikal -->
                        <div class="border-l border-gray-200 h-10"></div>
                        <!-- Profil & Dropdown Container (pastikan ini memiliki 'relative') -->
                        <div class="relative">

                            <!-- Checkbox Tersembunyi -->
                            <input type="checkbox" id="dropdown-toggle" class="hidden">

                            <!-- Label membungkus area profil, berfungsi sebagai tombol klik untuk checkbox -->
                            <label for="dropdown-toggle" class="flex items-center space-x-2 cursor-pointer p-2">
                                
                                <div class="h-10 w-10 bg-gray-100 rounded-full overflow-hidden flex items-center justify-center">

                                    <?php if(Auth::user()->detailPribadi && Auth::user()->detailPribadi->photo_selfie): ?>
                                        
                                        <img src="<?php echo e(asset('storage/' . Auth::user()->detailPribadi->photo_selfie)); ?>?v=<?php echo e(time()); ?>"
                                            class="h-full w-full object-cover"
                                            alt="Foto Profil">
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
<?php $component->withAttributes(['class' => 'h-10 w-10 text-gray-400 group-hover:text-yellow-500']); ?>
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
                                <div class="flex flex-col text-sm">
                                    <span class="font-small text-gray-800"><?php echo e(Auth::user()->name ?? 'User'); ?></span>
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

                                    <?php
                                        $userAuth = Auth::user();
                                        $isDataIncomplete = empty($userAuth->nomor_urut_pegawai) || empty($userAuth->email);
                                    ?>

                                    <!-- Tautan Data Diri dengan Validasi -->
                                    <?php if($isDataIncomplete): ?>
                                        <!-- Tampilan Notifikasi Warning Jika Data Belum Lengkap -->
                                        <div class="mx-2 mb-2 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded-md shadow-sm">
                                            <div class="flex">
                                                <div class="shrink-0">
                                                    <!-- Menggunakan Heroicon untuk indikasi peringatan -->
                                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-s-exclamation-triangle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 text-yellow-500']); ?>
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
                                                    <p class="text-xs text-yellow-700 leading-relaxed">
                                                        <strong>Perhatian:</strong> Nomor pegawai atau email belum terdaftar.
                                                        Silakan lengkapi profil Anda.
                                                        <br>
                                                        <a href="<?php echo e(route('profile.edit', ['form_type' => 'new'])); ?>" class="text-xs text-blue-700 hover:text-green-600 font-bold underline decoration-2 underline-offset-2">
                                                            Isi Data Diri Sekarang
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Menu Data Diri (Status: Belum Diisi / Mode New) -->
                                        <a href="<?php echo e(route('profile.edit', ['form_type' => 'new'])); ?>" class="flex items-center px-4 py-2 text-sm text-gray-400 hover:bg-gray-100 rounded-md group">
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-identification'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3 text-gray-400 group-hover:text-yellow-500']); ?>
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
                                            <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-medium">Lengkapi</span>
                                        </a>
                                    <?php else: ?>
                                        <!-- Tampilan Menu Normal Saat Data Sudah Lengkap (Mode Edit) -->
                                        <a href="<?php echo e(route('profile.edit', ['form_type' => 'edit'])); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md group">
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-identification'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3 text-blue-400 group-hover:text-blue-600']); ?>
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
                                            Data Diri
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-s-arrow-small-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-4 ml-auto text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity']); ?>
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

                                    <!-- Tautan Ubah Password (Tetap Tersedia) -->
                                    <a href="<?php echo e(url('/change-password')); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md group">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-lock-closed'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3 text-blue-400 group-hover:text-blue-600']); ?>
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
                                        Ubah Kata Sandi
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-s-arrow-small-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-4 ml-auto text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity']); ?>
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

                                <!-- Tautan Logout -->
                                <div class="p-2 border-t border-gray-200">
                                    <a href="<?php echo e(url('/logout')); ?>" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md w-full text-left group">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-x-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3 text-red-600 group-hover:scale-110 transition-transform']); ?>
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
                                <?php if(isset($breadcrumbs) && count($breadcrumbs) > 0): ?>
                                    <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="inline-flex items-center">
                                            
                                            <?php if(!$loop->first): ?>
                                                <span class="mx-2 text-gray-400">/</span>
                                            <?php endif; ?>

                                            <?php if($url): ?>
                                                
                                                <a href="<?php echo e($url); ?>" class="text-gray-700 hover:text-blue-600 flex items-center text-sm font-medium">
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
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3']); ?>
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
                                                
                                                <span class="text-gray-700 font-semibold-medium flex items-center text-sm">
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
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3']); ?>
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
                                        <span class="text-gray-700 font-semibold-medium flex items-center text-sm">
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-m-home'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 mr-3']); ?>
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

            <!-- Page Content -->
            
            
            <main id="mainContent" class="flex-1 flex flex-col h-screen overflow-hidden">
                <?php echo $__env->yieldContent('content'); ?>
            </main>

            <!-- Modal Sukses Dinamis -->
            <div id="successModal" class="<?php echo e(Session::has('success') ? 'flex' : 'hidden'); ?> fixed inset-0 bg-black/50 items-center justify-center z-50 backdrop-blur-sm">
                <div class="bg-gray-50 p-8 rounded-xl shadow-2xl w-full max-w-lg mx-4 relative">
                    <!-- Tombol X Close -->
                    <button onclick="document.getElementById('successModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="text-center">
                        <svg class="mx-auto h-16 w-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        
                        <!-- Judul Dinamis -->
                        <h2 class="mt-4 text-2xl font-bold text-gray-900">
                            <?php echo e(Session::get('modal_title') ?? 'Pengajuan Berhasil Dibuat!'); ?>

                        </h2>
                        
                        <!-- Pesan Dinamis -->
                        <p class="mt-2 text-sm text-gray-500">
                            <?php echo e(Session::get('success')); ?> <br>
                            Permintaan Anda telah tercatat dan menunggu persetujuan atasan. Silahkan tunggu notifikasi secara berkala.
                        </p>
                    </div>

                    <div class="mt-6 flex justify-center">
                        <!-- Tombol Link Dinamis -->
                        <a href="<?php echo e(Session::get('modal_link') ?? '#'); ?>" class="px-6 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            Lihat Pengajuan
                        </a>
                    </div>
                </div>
            </div>


        </div>
            <?php echo $__env->yieldPushContent('scripts'); ?>
    </div>

    <!-- Bagian JavaScript untuk mengaktifkan toggle sub-menu -->
    <script>
        // Pastikan skrip ini diletakkan setelah elemen HTML dimuat,
        // idealnya tepat sebelum tag penutup </body>
        document.addEventListener('DOMContentLoaded', (event) => {
            const toggleButton = document.getElementById('toggleAktivitasPegawai');
            const subMenu = document.getElementById('subMenuAktivitas');
            const arrowIcon = document.getElementById('arrowIcon');

            toggleButton.addEventListener('click', () => {
                // Toggle class 'hidden' pada container sub-menu
                subMenu.classList.toggle('hidden');

                // Opsional: Memutar ikon panah (chevron)
                arrowIcon.classList.toggle('rotate-180');

                // Logika tambahan jika Anda ingin menu lain tertutup saat menu ini terbuka bisa ditambahkan di sini.
            });
        });
    </script>

    <script>
        // Fungsi JavaScript untuk memperbarui jam setiap detik
        function updateClock() {
            // Kita menggunakan objek Date bawaan JavaScript (menggunakan waktu lokal browser)
            var now = new Date();

            // Format waktu menjadi HH:MM:SS
            // getHours(), getMinutes(), getSeconds() mengembalikan angka tunggal jika < 10,
            // jadi kita perlu padding nol agar formatnya konsisten (07:05:01)
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');
            var seconds = now.getSeconds().toString().padStart(2, '0');

            var timeString = hours + ':' + minutes + ':' + seconds;

            // Masukkan waktu ke dalam elemen HTML dengan ID "jam-dinamis"
            document.getElementById('jam-dinamis').textContent = timeString;
        }

        // Panggil fungsi updateClock() setiap 1000 milidetik (1 detik)
        setInterval(updateClock, 1000);

        // Panggil fungsi segera saat halaman dimuat untuk menghindari tampilan kosong
        updateClock();
    </script>

    <script>
        // Menutup modal jika area luar modal diklik
        window.onclick = function(event) {
            const successModal = document.getElementById('successModal');
            const errorModal = document.getElementById('errorModal');
            if (event.target == successModal) successModal.classList.add('hidden');
            if (event.target == errorModal) errorModal.classList.add('hidden');
        }
    </script>



<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


</body>
</html>
<?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/layouts/app-pegawai.blade.php ENDPATH**/ ?>