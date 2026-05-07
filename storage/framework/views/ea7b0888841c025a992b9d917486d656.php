<?php $__env->startSection('content'); ?>

<div class="bg-[#f8fafc] rounded-tl-md border border-gray-100 shadow-sm h-full overflow-y-auto" id="mainContent">

    <!-- Tab Navigation Header -->
    <div class="flex justify-between items-center mb-5 ml-2 mt-2">
        <!-- Tabs List -->
        <div class="flex space-x-2 border-b border-gray-100 w-full">
            <!-- Tab 1 -->
            <button onclick="switchTab('data-pengajuan')" id="tab-pengajuan" class="py-2.5 px-4 text-xs font-bold uppercase tracking-wider text-blue-600 border-b-2 border-blue-600 focus:outline-none transition-all duration-200">
                Data Pengajuan
            </button>
            <!-- Tab 2 -->
            
        </div>
    </div>

    <!-- Tab Content Container -->
    <div class="mt-2">

        <!-- ================= CONTENT: DATA PENGAJUAN ================= -->
        <div id="content-data-pengajuan" class="opacity-100 transition-opacity duration-300">

            <!-- 1. Stats Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 ml-2 mr-2">
                <!-- Card 1: Menunggu Persetujuan -->
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Menunggu Persetujuan</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo e(number_format($totalMenunggu)); ?> <span class="text-xs text-gray-400 font-medium">Data</span></p>
                    </div>
                    <div class="p-2.5 bg-yellow-50 rounded-lg border border-yellow-100">
                        <svg xmlns="http://w3.org" class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Card 2: Disetujui -->
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Disetujui</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo e(number_format($totalDisetujui)); ?> <span class="text-xs text-gray-400 font-medium">Data</span></p>
                    </div>
                    <div class="p-2.5 bg-green-50 rounded-lg border border-green-100">
                        <svg xmlns="http://w3.org" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <!-- Card 3: Ditolak -->
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Ditolak</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo e(number_format($totalDitolak)); ?> <span class="text-xs text-gray-400 font-medium">Data</span></p>
                    </div>
                    <div class="p-2.5 bg-red-50 rounded-lg border border-red-100">
                        <svg xmlns="http://w3.org" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- 2. Garis batas horizontal antara card dan informasi -->
            <hr class="border-b border-gray-100 mt-6">

            <!-- 3. Informasi & Pengumuman Section -->
            <div class="mt-4 ml-2 mr-2 max-w-full mx-auto"
                x-data="{
                    openModal: null,
                    totalRead: 0,
                    totalUnread: 0,
                    hitungStatus() {
                        let readCount = 0;
                        <?php $__currentLoopData = $daftar_berita; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            if (localStorage.getItem('berita_<?php echo e($berita->id_pengumuman); ?>') === 'true') {
                                readCount++;
                            }
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        this.totalRead = readCount;
                        this.totalUnread = <?php echo e($daftar_berita->count()); ?> - readCount;
                    }
                }"
                x-init="hitungStatus()"
                wire:poll.60m>

                <!-- Header & Navigasi -->
                <div class="flex flex-col sm:flex-row items-center justify-between mb-4 border-b border-gray-100 pb-2 gap-4">

                    <!-- Judul & Kedua Notifikasi -->
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-megaphone'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 text-blue-600']); ?>
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

                        <!-- Badge "Baru" -->
                        <template x-if="totalUnread > 0">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-[9px] font-bold leading-none text-white bg-red-500 rounded-full animate-pulse">
                                <span x-text="totalUnread" class="mr-1"></span> Baru
                            </span>
                        </template>
                    </h3>

                    <!-- Navigasi Halaman -->
                    <div class="flex items-center gap-4">
                        <p class="hidden sm:block text-xs font-semibold text-gray-400 whitespace-nowrap">
                            Menampilkan <?php echo e($daftar_berita->firstItem()); ?> - <?php echo e($daftar_berita->lastItem()); ?> dari <?php echo e($daftar_berita->total()); ?> data
                        </p>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
                            <a href="<?php echo e($daftar_berita->previousPageUrl() ?? '#'); ?>" class="px-2 py-1 border-r hover:bg-gray-50 <?php echo e($daftar_berita->onFirstPage() ? 'text-gray-300 pointer-events-none' : 'text-gray-600'); ?>">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                            </a>
                            <span class="px-2.5 py-1 bg-gray-50 text-xs font-bold text-gray-700 border-r"><?php echo e($daftar_berita->currentPage()); ?></span>
                            <a href="<?php echo e($daftar_berita->nextPageUrl() ?? '#'); ?>" class="px-2 py-1 hover:bg-gray-50 <?php echo e(!$daftar_berita->hasMorePages() ? 'text-gray-300 pointer-events-none' : 'text-gray-600'); ?>">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Daftar Berita/Card Berita -->
                <div class="grid gap-3">
                    <?php $__empty_1 = true; $__currentLoopData = $daftar_berita; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div x-data="{
                            isRead: localStorage.getItem('berita_<?php echo e($berita->id_pengumuman); ?>') === 'true',
                            bacaBerita() {
                                this.isRead = true;
                                localStorage.setItem('berita_<?php echo e($berita->id_pengumuman); ?>', 'true');
                                this.openModal = <?php echo e($berita->id_pengumuman); ?>;
                                hitungStatus();
                            }
                        }" class="group p-4 border border-gray-100 rounded-xl shadow-sm bg-white hover:border-blue-100 transition-all duration-300 relative">

                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-1.5">
                                <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                    <?php echo e($berita->judul); ?>

                                    <template x-if="!isRead">
                                        <span class="flex h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                                    </template>
                                </h4>
                                <div class="flex items-center text-xs text-gray-400 whitespace-nowrap pt-0.5">
                                    <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <?php echo e(\Carbon\Carbon::parse($berita->tanggal_posting)->shiftTimezone('Asia/Jakarta')->diffForHumans()); ?>

                                </div>
                            </div>

                            <p class="text-gray-500 text-xs leading-relaxed">
                                <?php echo e(Str::limit($berita->deskripsi_singkat, 150)); ?>

                                <button @click="bacaBerita()" class="text-blue-600 font-bold hover:text-blue-800 ml-0.5 focus:outline-none">
                                    Baca selengkapnya...
                                </button>
                            </p>

                            <!-- Modal Detail Berita -->
                            <div x-show="openModal === <?php echo e($berita->id_pengumuman); ?>" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>

                                <!-- Backdrop (Layar Gelap Transparan) -->
                                <div class="fixed inset-0 bg-gray-500/60 backdrop-blur-sm transition-opacity" @click="openModal = null"></div>

                                <!-- Konten Modal -->
                                <div class="relative min-h-screen flex items-center justify-center p-4">
                                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 overflow-hidden transform transition-all"
                                        x-show="openModal === <?php echo e($berita->id_pengumuman); ?>"
                                        x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100">

                                        <!-- Header Modal -->
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="bg-blue-50 px-3 py-1 rounded-lg text-blue-700 text-[10px] font-bold uppercase tracking-wider">
                                                Detail Pengumuman
                                            </div>
                                            <button @click="openModal = null" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>

                                        <h2 class="text-sm font-bold text-gray-900 mb-3"><?php echo e($berita->judul); ?></h2>

                                        <div class="flex items-center text-xs text-gray-500 mb-4 pb-3 border-b border-gray-100">
                                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Dipublikasikan pada: <?php echo e(\Carbon\Carbon::parse($berita->tanggal_posting)->format('d F Y - H:i')); ?> WIB
                                        </div>

                                        <!-- Isi Berita Lengkap -->
                                        <div class="prose max-w-none text-gray-700 leading-loose text-xs">
                                            <?php echo e($berita->deskripsi_singkat); ?>

                                        </div>

                                        <!-- Footer Modal -->
                                        <div class="mt-5 pt-4 border-t border-gray-100 flex justify-end">
                                            <button @click="openModal = null" class="px-5 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-200 transition-colors">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-10 flex flex-col items-center justify-center text-gray-400">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-chat-bubble-bottom-center-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-10 w-10 text-gray-200 mb-2']); ?>
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
                            <p class="text-xs font-medium">Belum ada informasi yang diumumkan.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Navigasi Pagination (Tetap Dikomen) -->
                
            </div>
        </div>

        <!-- ================= CONTENT: ABSENSI KEHADIRAN ================= -->
        
    </div>

    <!-- TOMBOL BACK TO TOP -->
    <button id="backToTop" style="display: none;" class="fixed bottom-6 right-6 bg-blue-500 text-white p-2.5 rounded-full shadow-lg hover:bg-blue-600 hover:scale-105 transition-all duration-200 z-50 flex items-center justify-center" title="Kembali ke atas">
        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-chevron-up'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'arrowIcon','class' => 'h-5 w-5']); ?>
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

<?php echo $__env->make('layouts.app-direktur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/direktur/dashboarddirektur.blade.php ENDPATH**/ ?>