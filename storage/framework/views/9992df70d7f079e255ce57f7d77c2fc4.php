<?php $__env->startSection('content'); ?>

    
    <!-- Form Tunggal untuk Semua Tab -->
    <!-- PENTING: enctype="multipart/form-data" tetap harus ada di sini -->
    <form action="<?php echo e(route('lembur.updateLembur')); ?>" method="POST" id="leaveFormLembur">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

    <div class="flex-1 overflow-y-auto h-[calc(100vh-120px)] space-y-2 custom-scroll-container">
        <div class="bg-white rounded-lg shadow-lg max-w-full mx-auto">
            <div style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.1)), url('<?php echo e(asset('images/vecteezylight.jpg')); ?>')" class="bg-cover bg-bottom p-2 rounded-t-lg relative">
                    <img src="<?php echo e(asset('images/overtime.png')); ?>" alt="Overtime" class="absolute right-0 top-0 h-40">
                    
                    
                    
                    <div class="flex items-center mt-2 ml-2 mb-2">
                        
                        <div class="h-28 w-28 rounded-full overflow-hidden flex items-center justify-center">
                            <?php if(Auth::user()->detailPribadi && Auth::user()->detailPribadi->photo_selfie): ?>
                                
                                <img src="<?php echo e(asset('storage/' . Auth::user()->detailPribadi->photo_selfie)); ?>?v=<?php echo e(time()); ?>"
                                    class="h-32 w-32 rounded-full object-cover border border-gray-200 group-hover:border-green-500 transition-all duration-300"
                                    alt="Foto Selfie Pegawai">
                            <?php else: ?>
                                
                                <div class="h-28 w-28 rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-200 group-hover:border-yellow-500 transition-all duration-300">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-x-person-profile'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-20 w-20 text-gray-400 group-hover:text-yellow-500']); ?>
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
                            <?php endif; ?>
                        </div>

                    <div class="ml-5">
                        <h1 class="text-gray-800 text-1xl font-bold">
                            <?php echo e(Auth::user()->name ?? 'User'); ?> -
                            <a class="text-gray-600 font-semibold text-sm">
                                <?php echo e(Auth::user()->pegawai->nomor_urut_pegawai ?? 'Nomor Urut Pegawai tidak ditemukan'); ?>

                            </a>
                        </h1>
                        <p class="text-gray-600 font-semibold text-sm"><?php echo e($pekerjaanData->jabatan ?? 'Jabatan Tidak Ditemukan'); ?></p>
                        <p class="text-gray-600 font-semibold text-sm">
                            <?php echo e(($pekerjaanData->pangkat ?? '') . ' - ' . ($pekerjaanData->grade ?? '')); ?>

                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-2 p-4 shadow-sm">
                <span class="text-md font-semibold text-blue-700 mb-4"># Section : Pengajuan Lembur</span>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <!-- Nama Pegawai -->
                    <div>
                        <label for="nama_pegawai" class="block text-sm font-medium text-gray-700 mb-1">Nama Pegawai</label>
                        <input type="text" id="nama_pegawai" value="<?php echo e(auth()->user()->name ?? 'NamaPegawaiDefault'); ?>" readonly class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <!-- NUP Pegawai -->
                    <div>
                        <label for="nomor_urut_pegawai" class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut Pegawai</label>
                        <input type="text" id="nomor_urut_pegawai" name="nomor_urut_pegawai" value="<?php echo e(auth()->user()->nomor_urut_pegawai); ?>" readonly class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <!-- Jabatan saat ini -->
                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <input type="text" id="jabatan" value="<?php echo e($pekerjaanData->jabatan ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <!-- Unit Kerja -->
                    <div>
                        <label for="unit_kerja" class="block text-sm font-medium text-gray-700 mb-1">Pangkat</label>
                        <input type="text" id="unit_kerja" value="<?php echo e($pekerjaanData->pangkat ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm sm:text-sm">
                    </div>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <!-- Tanggal Lembur -->
                    <div>
                        <label for="tanggal_lembur" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lembur</label>
                        <div class="relative">
                            <input type="date" id="tanggal_lembur" name="tanggal_lembur" min="<?php echo e(now()->format('Y-m-d')); ?>" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" placeholder="MM/DD/YYYY">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- Calendar Icon (Heroicon) -->
                                <svg class="h-5 w-5 text-gray-400" xmlns="www.w3.org" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Mulai Lembur -->
                    <div>
                        <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai Lembur</label>
                        <div class="relative">
                            <input type="time" id="jam_mulai" name="jam_mulai" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" value="00:00">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- Clock Icon (Heroicon) -->
                                <svg class="h-5 w-5 text-gray-400" xmlns="www.w3.org" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Selesai Lembur -->
                    <div>
                        <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai Lembur</label>
                        <div class="relative">
                            <input type="time" id="jam_selesai" name="jam_selesai" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" value="00:00">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- Clock Icon (Heroicon) -->
                            <svg class="h-5 w-5 text-gray-400" xmlns="www.w3.org" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Jam Lembur -->
                    <div>
                        <label for="total_jam" class="block text-sm font-medium text-gray-700 mb-1">Total Jam Lembur</label>
                        <!-- Pastikan type="text" -->
                        <input type="text" id="total_jam" name="total_jam_lembur" value="" readonly class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm focus:outline-none sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="uraian_tugas" class="block text-sm font-medium text-gray-700 mb-1 mt-4">Uraian Tugas/Kegiatan</label>
                    <textarea id="uraian_tugas" name="uraian_tugas" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" placeholder="Jelaskan uraian tugas/kegiatan selama lembur.."></textarea>
                </div>

                <!-- Buttons Section -->
                <div class="mt-4 flex justify-end space-x-4">
                    <button type="button" id="openModalButtonLembur" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded rounded-lg text-sm font-semibold">
                        Buat Pengajuan Lembur
                    </button>
                </div>
            </div>

            <!-- === POPUP MODAL REVIEW === -->
            <div id="leaveModallembur" class="fixed inset-0 bg-black-50 flex items-center justify-center hidden z-50 backdrop-blur-sm">
                <!-- Konten Modal (Ubah max-w-lg jadi max-w-4xl agar lebih lebar) -->
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl mx-auto">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center border-b pb-3">
                        <h2 class="text-sm font-semibold">Surat Pengajuan Lembur</h2>
                    </div>

                    <!-- Modal Body dengan Container Scroll -->
                    <div class="mt-4">
                        <div class="custom-scroll-container p-4" style="max-height: 70vh; overflow-y: auto;">
                            <?php echo $__env->make('partials.lembur_letter_content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end mt-6 pt-4 border-t">
                        <button type="button" id="cancelButton" class="px-4 py-2 mr-3 text-sm text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                        <button id="submitButton" class="px-4 py-2 text-white bg-blue-600 text-sm rounded-lg hover:bg-blue-700">Ya, Ajukan Lembur</button>
                    </div>
                </div>
            </div>

            <!-- Modal Loading Overlay -->
            <div id="loadingModalLembur" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm">
                <div class="bg-gray-50 p-8 rounded-xl shadow-2xl flex flex-col items-center">
                    <!-- Heroicon Paper Airplane dengan Animasi Pulse -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-blue-600 animate-fly">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>

                    <p class="mt-4 text-gray-800 text-md font-semibold">Sedang Mengirim Data Pengajuan...</p>
                </div>
            </div>

            <!-- Modal Sukses (Sesuai Gambar) -->
            
        </div>
    </div>
</form>

<?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/overtime.js', 'resources/js/reviewlembur.js']); ?>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->user()->layout_file, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/lembur/lembur.blade.php ENDPATH**/ ?>