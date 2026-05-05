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
            
            <div class="flex justify-between items-center mt-6">
                
                <div class="w-full flex justify-between items-start overflow-visible">
                    
                    <?php $__currentLoopData = $submission['stageData']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            // Pemetaan status untuk style agar sinkron dengan atasan
                            $isDone = ($stage['statusString'] == 'disetujui');
                            $isFail = ($stage['statusString'] == 'ditolak');
                            $isCurr = $stage['isCurrent'];

                            $circleClass = $isDone ? 'bg-emerald-500 border-emerald-100 text-white' :
                                        ($isFail ? 'bg-red-500 border-red-100 text-white' :
                                        ($isCurr ? 'bg-orange-500 border-orange-100 text-white' : 'bg-gray-200 border-gray-50 text-gray-400'));
                        ?>

                        <div class="flex flex-col items-center flex-1 relative">
                            
                            
                            <?php if(!$loop->last): ?>
                                <?php
                                    // 1. Ambil warna yang sudah ditentukan di Controller (stageData)
                                    $lineColor = $stage['lineColor'] ?? 'bg-gray-100';

                                    // 2. Tambahkan logika pengaman: Jika ada penolakan sebelumnya, garis tetap abu-abu (putus)
                                    $hasPreviousReject = collect($submission['stageData'])->take($loop->index)->contains('statusString', 'ditolak');
                                    if ($hasPreviousReject) {
                                        $lineColor = 'bg-gray-100';
                                    }

                                    // 3. Pastikan class warna menggunakan Emerald agar seragam dengan bulatan Anda
                                    // (Opsional: Jika di Controller pakai bg-teal-500, ganti ke bg-emerald-500 di sini)
                                    $lineColor = str_replace('bg-teal-500', 'bg-emerald-500', $lineColor);
                                ?>

                                <div class="absolute top-5 left-1/2 w-full h-[2.5px] z-0 <?php echo e($lineColor); ?>"></div>
                            <?php endif; ?>

                            
                            <div class="relative flex items-center justify-center z-10 group">
                                <?php if($isCurr): ?>
                                    <span class="absolute inline-flex h-9 w-9 rounded-full bg-orange-400 opacity-20 animate-ping"></span>
                                <?php endif; ?>

                                
                                <?php
                                    $forbiddenStages = ['Pengajuan Awal', 'Pengajuan', 'Selesai', 'Akhir'];
                                ?>

                                <?php if(isset($stage['comment']) && $stage['comment'] && !in_array($stage['stageName'], $forbiddenStages)): ?>
                                    <div class="absolute bottom-[110%] left-[0%] -translate-x-0 mb-2
                                                invisible group-hover:visible opacity-0 group-hover:opacity-100
                                                w-48 p-3 bg-gray-900 text-white text-[10px] rounded-xl shadow-2xl
                                                z-50 transition-all duration-300 pointer-events-none">

                                        <div class="font-bold border-b border-gray-700 pb-1 mb-1 uppercase tracking-widest text-amber-400">
                                            Catatan <?php echo e($stage['stageName']); ?>:
                                        </div>
                                        <p class="italic text-gray-200 leading-relaxed">"<?php echo e($stage['comment']); ?>"</p>

                                        
                                        <div class="absolute top-full left-4 border-8 border-transparent border-t-gray-900"></div>
                                    </div>
                                <?php endif; ?>

                                
                                <div class="w-10 h-10 rounded-full <?php echo e($circleClass); ?> border-[5px] flex items-center justify-center shadow-sm cursor-help transition-all duration-300">
                                    <?php if($isFail): ?>
                                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <?php elseif($isDone): ?>
                                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <?php else: ?>
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            
                            <div class="mt-4 text-center px-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tahap <?php echo e($loop->iteration); ?></p>
                                <p class="text-[11px] font-semibold text-gray-900 mt-1 leading-tight"><?php echo e($stage['stageName']); ?></p>

                                <p class="text-[9px] font-bold <?php echo e($isDone ? 'text-emerald-600' : ($isFail ? 'text-red-600' : ($isCurr ? 'text-orange-600' : 'text-gray-400'))); ?> mt-1 italic uppercase">
                                    <?php echo e($stage['statusText']); ?>

                                </p>

                                <?php if($stage['updatedAt']): ?>
                                    <p class="text-[8px] text-gray-500 mt-1 whitespace-nowrap">
                                        <?php echo e($stage['updatedAt']); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php
                        $lastStage = collect($submission['stageData'])->last();
                        $isHRODone = ($lastStage['stageName'] === 'HRO' && $lastStage['statusString'] === 'disetujui');
                        $isHROFail = ($lastStage['stageName'] === 'HRO' && $lastStage['statusString'] === 'ditolak');
                    ?>

                    <div class="flex flex-col items-center flex-1 relative">
                        
                        <div class="absolute top-5 -left-1/2 w-full h-[2.5px] z-0
                            <?php echo e($isHRODone ? 'bg-emerald-500' : ($isHROFail ? 'bg-red-500' : 'bg-gray-100')); ?>">
                        </div>

                        
                        <div class="w-10 h-10 rounded-full <?php echo e($isHRODone ? 'bg-emerald-500 border-emerald-100' : 'bg-gray-100'); ?> border-[5px] flex items-center justify-center z-10 shadow-sm transition-all duration-300">
                            <?php if($isHRODone): ?>
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            <?php else: ?>
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4 text-center <?php echo e($isHRODone ? '' : 'opacity-40'); ?>">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Akhir</p>
                            <p class="text-[11px] font-semibold text-gray-400 mt-1">Selesai</p>
                        </div>
                    </div>
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

                
                <?php if(isset($submission['display_info']['saldo_akhir']) && $submission['display_info']['saldo_akhir']): ?>
                <div>
                    <p class="text-gray-500">Sisa Cuti (Hari)</p>
                    <p class="font-medium text-gray-800 mt-1"><?php echo e($submission['display_info']['saldo_akhir']); ?></p>
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