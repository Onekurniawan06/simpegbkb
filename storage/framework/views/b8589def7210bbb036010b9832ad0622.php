<?php $__env->startSection('content'); ?>
    <div class="bg-gray-100 rounded-l-md shadow-lg p-4 h-full">
        <!-- Tab Navigation Header -->
        <div class="flex justify-between items-center mb-4">
            <!-- Tabs List -->
            <div class="flex space-x-4 border-b border-gray-200">
                <!-- Tambahkan onclick handler yang memanggil fungsi JS global -->
                <button onclick="switchTab('data-pengajuan')" id="tab-pengajuan" class="py-2 px-3 text-sm font-medium focus:outline-none transition duration-150 ease-in-out">
                    Data Pengajuan
                </button>
                <button onclick="switchTab('absensi-kehadiran')" id="tab-absensi" class="py-2 px-3 text-sm font-medium focus:outline-none transition duration-150 ease-in-out">
                    Absensi Kehadiran
                </button>
            </div>
        </div>

        <!-- Tab Content Container -->
        <div>
            <!-- Content for 'Data Pengajuan' (Visible by default) -->
            <div id="content-data-pengajuan" class="opacity-0 transition-opacity duration-300">
                <!-- Stats Cards Section (Dipindahkan ke dalam tab ini) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Card 1: Menunggu Persetujuan -->
                    <div class="bg-white p-5 rounded-lg shadow-md flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pengajuan Menunggu Persetujuan</p>
                            <!-- Menampilkan hasil hitung -->
                            <p class="text-3xl font-semibold text-gray-900"><?php echo e($totalMenunggu); ?></p>
                            <p class="text-xs text-gray-400">Data Pengajuan</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg xmlns="http://www.w3.org" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Card 2: Disetujui -->
                    <div class="bg-white p-5 rounded-lg shadow-md flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pengajuan Disetujui</p>
                            <!-- Tampilkan hasil hitung disetujui -->
                            <p class="text-3xl font-semibold text-gray-900"><?php echo e($totalDisetujui); ?></p>
                            <p class="text-xs text-gray-400">Data Pengajuan</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg xmlns="http://www.w3.org" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Card 3: Ditolak -->
                    <div class="bg-white p-5 rounded-lg shadow-md flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pengajuan Ditolak</p>
                            <!-- Tampilkan hasil hitung ditolak -->
                            <p class="text-3xl font-semibold text-gray-900"><?php echo e($totalDitolak); ?></p>
                            <p class="text-xs text-gray-400">Data Pengajuan</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <svg xmlns="http://www.w3.org" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Content for 'Absensi Kehadiran' (Hidden by default) -->
            <div id="content-absensi-kehadiran" class="opacity-0 transition-opacity duration-300">
                <!-- == KODE BARU DIMULAI DI SINI == -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Card 1: Total Pegawai Belum Absen -->
                    <div class="bg-white p-5 rounded-lg shadow-md flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pegawai Belum Absen</p>
                            <p class="text-3xl font-semibold text-gray-900">55</p>
                            <p class="text-xs text-gray-400">Pegawai</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <!-- Icon minus -->
                            <svg xmlns="http://www.w3.org" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                            </svg>
                        </div>
                    </div>

                    <!-- Card 2: Total Pegawai Sudah Absen -->
                    <div class="bg-white p-5 rounded-lg shadow-md flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pegawai Sudah Absen</p>
                            <p class="text-3xl font-semibold text-gray-900">5</p>
                            <p class="text-xs text-gray-400">Pegawai</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <!-- Icon checkmark -->
                            <svg xmlns="http://www.w3.org" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- == KODE BARU BERAKHIR DI SINI == -->
            </div>
        </div>

        <!-- Garis batas horizontal antara card dan informasi -->
        <hr class="border-b border-gray-100 mt-6">

        <!-- Informasi & Pengumuman Section -->
        <div class="mt-3 max-w-full mx-auto" x-data="{ openModal: null, totalRead: 0, totalUnread: 0, hitungStatus() { let readCount = 0;
                <?php $__currentLoopData = $daftar_berita; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    if (localStorage.getItem('berita_<?php echo e($berita->id_pengumuman); ?>') === 'true') {
                        readCount++;
                    }
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                this.totalRead = readCount;
                this.totalUnread = <?php echo e($daftar_berita->count()); ?> - readCount; }
            }"
            x-init="hitungStatus()"
            wire:poll.60m>

            <!-- Header & Navigasi (Tampilan Rapi seperti gambar) -->
<div class="flex flex-col sm:flex-row items-center justify-between mb-4 border-b border-gray-200 pb-2 gap-4">

    <!-- Judul & Kedua Notifikasi (RATA KIRI) -->
    <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2 w-full sm:w-auto justify-center sm:justify-start">
        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-megaphone'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 text-indigo-600']); ?>
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
        <span>Informasi & Pengumuman</span>

        <!-- Badge "Baru" (Menggunakan Alpine.js totalUnread) -->
        <template x-if="totalUnread > 0">
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full animate-pulse">
                <span x-text="totalUnread" class="mr-1"></span> Informasi Baru
            </span>
        </template>
    </h3>

    <!-- Navigasi & Info Data (RATA KANAN) -->
    <div class="flex items-center gap-4 w-full sm:w-auto justify-center sm:justify-end">
        <p class="hidden sm:block text-[13px] font-semibold text-gray-500 whitespace-nowrap">
            Menampilkan <?php echo e($daftar_berita->firstItem()); ?> - <?php echo e($daftar_berita->lastItem()); ?> dari <?php echo e($daftar_berita->total()); ?> data
        </p>
        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
            <a href="<?php echo e($daftar_berita->previousPageUrl() ?? '#'); ?>" class="px-2 py-1.5 border-r hover:bg-gray-50 <?php echo e($daftar_berita->onFirstPage() ? 'text-gray-300 pointer-events-none' : 'text-gray-600'); ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <span class="px-3 py-1.5 bg-gray-50 text-xs font-bold text-gray-700 border-r"><?php echo e($daftar_berita->currentPage()); ?></span>
            <a href="<?php echo e($daftar_berita->nextPageUrl() ?? '#'); ?>" class="px-2 py-1.5 hover:bg-gray-50 <?php echo e(!$daftar_berita->hasMorePages() ? 'text-gray-300 pointer-events-none' : 'text-gray-600'); ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</div>


            <!-- Daftar Berita/Card Berita -->
            <div class="grid gap-4">
                <?php $__empty_1 = true; $__currentLoopData = $daftar_berita; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div x-data="{
                        isRead: localStorage.getItem('berita_<?php echo e($berita->id_pengumuman); ?>') === 'true',
                        bacaBerita() {
                            this.isRead = true;
                            localStorage.setItem('berita_<?php echo e($berita->id_pengumuman); ?>', 'true');
                            this.openModal = <?php echo e($berita->id_pengumuman); ?>;
                            // Panggil fungsi hitungStatus() agar badge atas langsung update
                            hitungStatus();
                        }
                    }" class="group p-3 border border-gray-200 rounded-xl shadow-sm bg-white hover:bg-gray-100 transition-all duration-300 relative">

                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-2">
                            <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <?php echo e($berita->judul); ?>

                                <template x-if="!isRead">
                                    <span class="flex h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                                </template>
                            </h4>
                            <div class="flex items-center text-xs text-gray-400 whitespace-nowrap pt-1">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <?php echo e(\Carbon\Carbon::parse($berita->tanggal_posting)->shiftTimezone('Asia/Jakarta')->diffForHumans()); ?>

                            </div>
                        </div>

                        <p class="text-gray-600 text-sm leading-relaxed">
                            <?php echo e(Str::limit($berita->deskripsi_singkat, 150)); ?>

                            <button @click="bacaBerita()" class="text-indigo-600 font-semibold hover:text-indigo-800 ml-1 focus:outline-none">
                                Baca selengkapnya...
                            </button>
                        </p>

                        <!-- Modal Detail Berita -->
                        <div x-show="openModal === <?php echo e($berita->id_pengumuman); ?>" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>

                            <!-- Backdrop (Layar Gelap) -->
                            <div class="fixed inset-0 bg-gray-500/60 backdrop-blur-sm transition-opacity" @click="openModal = null"></div>

                            <!-- Konten Modal -->
                            <div class="relative min-h-screen flex items-center justify-center p-4">
                                <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 overflow-hidden transform transition-all"
                                    x-show="openModal === <?php echo e($berita->id_pengumuman); ?>"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100">

                                    <!-- Header Modal -->
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="bg-indigo-50 px-3 py-1 rounded-lg text-indigo-700 text-xs font-bold uppercase tracking-wider">
                                            Detail Pengumuman
                                        </div>
                                    </div>

                                    <h2 class="text-sm font-semibold text-gray-900 mb-4"><?php echo e($berita->judul); ?></h2>

                                    <div class="flex items-center text-sm text-gray-500 mb-4 pb-4 border-b border-gray-100">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Dipublikasikan pada: <?php echo e(\Carbon\Carbon::parse($berita->tanggal_posting)->format('d F Y - H:i')); ?> WIB
                                    </div>

                                    <!-- Isi Berita Lengkap -->
                                    <div class="prose max-w-none text-gray-700 leading-loose text-sm">
                                        <?php echo e($berita->deskripsi_singkat); ?>

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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-gray-500 py-10">Tidak ada berita.</p>
                <?php endif; ?>
            </div>

            <!-- Navigasi Pagination -->
            
        </div>
        

        <!-- TOMBOL BACK TO TOP (Ditempatkan di sini, akan melayang di atas mainContent) -->
        <button id="backToTop" style="display: none;" class="fixed bottom-10 right-10 bg-blue-300 text-white p-3 rounded-full shadow-2xl hover:bg-blue-600 transition-all duration-300 z-50 flex items-center justify-center" title="Kembali ke atas">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-chevron-up'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'arrowIcon','class' => 'h-6 w-6 ']); ?>
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

    </div>
</div>


<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app-hro', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/hro/dashboardhro.blade.php ENDPATH**/ ?>