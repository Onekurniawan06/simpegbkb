<?php $__env->startSection('content'); ?>

    <div class="bg-gray-100 rounded-tl-md shadow-lg p-4 h-full overflow-y-auto" id="mainContent">
        

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
        <!-- Container Utama Livewire & Alpine.js -->
        
        

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



<?php echo $__env->make('layouts.app-direktur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/direktur/dashboarddirektur.blade.php ENDPATH**/ ?>