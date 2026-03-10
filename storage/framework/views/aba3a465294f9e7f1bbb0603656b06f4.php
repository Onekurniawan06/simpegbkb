<?php $__env->startSection('content'); ?>

<div class="flex-1 overflow-y-auto h-[calc(100vh-120px)] space-y-2 custom-scroll-container">
    <div class="bg-white rounded-l-md shadow-lg max-w-full">
        
        <div style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.1)), url('<?php echo e(asset('images/vecteezylight.jpg')); ?>')" class="bg-cover bg-bottom p-2 rounded-t-lg relative">
            <img src="<?php echo e(asset('images/trackingcheck.png')); ?>" alt="Overtime" class="absolute right-0 top-0 h-40">
            
            
            
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
                        <?php echo e(($pekerjaanData->pangkat ?? 'Pangkat Tidak Ditemukan') . ' - ' . ($pekerjaanData->grade ?? 'Grade Tidak Ditemukan')); ?>

                    </p>
                </div>
            </div>
        </div>
        <div class="mb-2 p-4 shadow-sm">
            <span class="text-sm font-semibold text-blue-700"># Section 1: Status Lacak Pengajuan <?php echo e($submissionType ?? 'Pengajuan'); ?></span>
            
            <div class="flex justify-between items-center mt-4">
                <div class="w-full flex justify-between items-start overflow-hidden">
                    
                    <?php $__currentLoopData = $submission['stageData']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex flex-col items-center flex-1 relative">
                            
                            <div class="relative z-10 flex items-center justify-center w-12 h-12 rounded-full border-2 shadow-md
                                <?php echo e($stage['statusString'] == 'disetujui' ? 'border-teal-500 bg-teal-500 text-white' :
                                ($stage['statusString'] == 'ditolak' ? 'border-red-500 bg-red-500 text-white' :
                                ($stage['isCurrent'] ? 'border-orange-500 bg-white text-orange-500' : 'border-gray-300 bg-white text-gray-400'))); ?>">
                                <?php if($stage['statusString'] == 'ditolak'): ?>
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" stroke-width="2")/></svg>
                                <?php elseif($stage['statusString'] == 'disetujui'): ?>
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 13l4 4L19 7" stroke-width="2")/></svg>
                                <?php else: ?>
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2")/></svg>
                                <?php endif; ?>
                            </div>

                            
                            <div class="mt-3 text-center w-full">
                                <p class="text-sm font-semibold text-gray-800"><?php echo e($stage['stageName']); ?></p>
                                <span class="block text-[12px] font-bold mt-1 <?php echo e($stage['statusBadge']); ?> rounded-full px-2 py-0.5 mx-auto w-fit">
                                    <?php echo e($stage['statusText']); ?>

                                </span>
                                <?php if($stage['updatedAt']): ?>
                                    <p class="text-[11px] text-gray-600 mt-1"><?php echo e($stage['updatedAt']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if(!$loop->last): ?>
                            <div class="flex-1 h-0.5 mt-5 <?php echo e($stage['lineColor']); ?> -mx-4 z-0"></div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-l-md shadow-lg max-w-full">
        <div class="mb-2 p-4 shadow-sm">
            <span class="text-sm font-semibold text-blue-700"># Section 2: Detail Pengajuan <?php echo e($submissionType ?? 'Pengajuan'); ?></span>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-sm mt-4">
                
                <div>
                    <p class="text-gray-500"><?php echo e($submission['display_info']['jenis_label']); ?></p>
                    <p class="font-medium text-gray-800 mt-1"><?php echo e($submission['display_info']['jenis_val']); ?></p>
                </div>
                <div>
                    <p class="text-gray-500"><?php echo e($submission['display_info']['tgl_mulai_label']); ?></p>
                    <p class="font-medium text-gray-800 mt-1"><?php echo e($submission['display_info']['tgl_mulai_val']); ?></p>
                </div>
                <?php if($submissionType != 'Kenaikan Pangkat/Gaji/Tunjangan'): ?>
                <div>
                    <p class="text-gray-500"><?php echo e($submission['display_info']['tgl_selesai_label']); ?></p>
                    <p class="font-medium text-gray-800 mt-1"><?php echo e($submission['display_info']['tgl_selesai_val']); ?></p>
                </div>
                <?php endif; ?>
                <div>
                    <p class="text-gray-500"><?php echo e($submission['display_info']['total_label']); ?></p>
                    <p class="font-medium text-gray-800 mt-1"><?php echo e($submission['display_info']['total_val']); ?></p>
                </div>

                
                <?php if(isset($submission['display_info']['sisa_cuti']) && $submission['display_info']['sisa_cuti']): ?>
                <div>
                    <p class="text-gray-500">Sisa Cuti (Hari)</p>
                    <p class="font-medium text-gray-800 mt-1"><?php echo e($submission['display_info']['sisa_cuti']); ?></p>
                </div>
                <?php endif; ?>

                <?php if(isset($submission['display_info']['alasan_val']) && $submission['display_info']['alasan_val']): ?>
                <div>
                    <p class="text-gray-500"><?php echo e($submission['display_info']['alasan_label']); ?></p>
                    <p class="font-medium text-blue-600 mt-1"><?php echo e($submission['display_info']['alasan_val']); ?></p>
                </div>
                <?php endif; ?>

                
                <div>
                    <p class="text-gray-500">Alasan Disetujui / Ditolak</p>
                    <p class="font-medium text-blue-600 mt-1"><?php echo e($komentarStatus ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($submissionType === 'Kenaikan Pangkat/Gaji/Tunjangan' || $submissionType === 'Pensiun'): ?>

        
        <?php
            $submissionData = null;
            if (isset($pengajuankenaikan)) {
                $submissionData = $pengajuankenaikan;
            } elseif (isset($pengajuanpensiun)) {
                $submissionData = $pengajuanpensiun;
            }
        ?>

        <div class="bg-white rounded-l-md shadow-xl max-w-full mb-4 overflow-hidden">
            <div class="p-3 bg-gray-50 border-b border-gray-200">
                <span class="text-sm font-semibold text-blue-700"># Section 3: Dokumen Persyaratan</span>
            </div>

            
            <div class="mt-0 overflow-x-auto mb-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nama File</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Syarat Dokumen</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        
                        <?php $__currentLoopData = $submissionData?->files ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php
                                $extension = strtolower(pathinfo($doc->nama_file_asli, PATHINFO_EXTENSION));
                                // $url = route('view.document', $doc->id);
                                if ($submissionType === 'Pensiun') {
                                    $url = route('view.document.pensiun', $doc->id); // Gunakan rute baru pensiun
                                } else {
                                    $url = route('view.document', $doc->id); // Gunakan rute lama pangkat/gaji
                                }
                            ?>

                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800"><?php echo e($doc->nama_file_asli); ?></td>

                                
                                <td class="px-5 py-3 whitespace-nowrap text-left">
                                    <?php if($extension === 'pdf'): ?>
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo e($doc->tipe_dokumen); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <?php echo e($doc->tipe_dokumen); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    
                                    <button onclick="openModal('<?php echo e($url); ?>', '<?php echo e($extension); ?>')" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow hover:shadow-md transition duration-150 ease-in-out">
                                        Lihat Dokumen
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                
                <?php if(empty($submissionData?->files) || $submissionData->files->isEmpty()): ?>
                    <p class="p-6 text-gray-500 text-sm italic">Tidak ada dokumen yang diunggah.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    
    
    
    
    <div id="documentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 backdrop-blur-sm">
        <div class="bg-white p-4 rounded-lg shadow-xl w-3/4 max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-md font-bold">Lihat Dokumen</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="documentContent" class="w-full h-[75vh]">
                
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('documentModal');
    const contentArea = document.getElementById('documentContent');

    function openModal(url, fileType) {
        if (fileType.toLowerCase() === 'pdf' || url.toLowerCase().endsWith('.pdf')) {
            contentArea.innerHTML = `<iframe src="${url}" frameborder="0" class="w-full h-full"></iframe>`;
        } else {
            contentArea.innerHTML = `<p class="p-4 text-center">Pratinjau tidak tersedia untuk tipe file ini. Silakan <a href="${url}" class="text-blue-600 hover:underline font-semibold">unduh dokumen</a> untuk melihatnya.</p>`;
        }
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        contentArea.innerHTML = '';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/datapengajuan/lacakpengajuan.blade.php ENDPATH**/ ?>