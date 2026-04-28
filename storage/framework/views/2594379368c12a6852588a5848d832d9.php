<?php $__env->startSection('content'); ?>

    <div class="bg-white rounded-lg shadow-lg max-w-full mx-auto">
        <div class="bg-white p-4 rounded-l-md shadow-md">
            <form action="<?php echo e(route('datapengajuan.formDataPengajuan')); ?>" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    
                    <div>
                        <label for="dari_tanggal" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" id="dari_tanggal" value="<?php echo e(request('dari_tanggal')); ?>"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    
                    <div>
                        <label for="hingga_tanggal" class="block text-sm font-medium text-gray-700 mb-1">Hingga Tanggal</label>
                        <input type="date" name="hingga_tanggal" id="hingga_tanggal" value="<?php echo e(request('hingga_tanggal')); ?>"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengajuan</label>
                        <select name="type" id="type"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Jenis</option>
                            
                            <option value="Cuti" <?php echo e(request('type') == 'Cuti' ? 'selected' : ''); ?>>Pengajuan Cuti</option>
                            <option value="Lembur" <?php echo e(request('type') == 'Lembur' ? 'selected' : ''); ?>>Pengajuan Lembur</option>
                            <option value="Pensiun" <?php echo e(request('type') == 'Pensiun' ? 'selected' : ''); ?>>Pengajuan Pensiun</option>
                            <option value="PangkatGajiTunjangan" <?php echo e(request('type') == 'PangkatGajiTunjangan' ? 'selected' : ''); ?>>Kenaikan Pangkat, Gaji dan Tunjangan</option>
                        </select>
                    </div>

                    
                    <div>
                        <label for="status_pengajuan_filter" class="block text-sm font-medium text-gray-700 mb-1">Status Pengajuan</label>
                        <select name="status_pengajuan_filter" id="status_pengajuan_filter"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            
                            <option value="diproses" <?php echo e(request('status_pengajuan_filter') == 'diproses' ? 'selected' : ''); ?>>Diproses</option>
                            <option value="disetujui" <?php echo e(request('status_pengajuan_filter') == 'disetujui' ? 'selected' : ''); ?>>Disetujui</option>
                            <option value="ditolak" <?php echo e(request('status_pengajuan_filter') == 'ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                        </select>
                    </div>
                </div>

                
                <div class="flex gap-3">
                    <button type="submit" class="h-10 px-4 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md transition duration-150 gap-2">
                        
                        <svg xmlns="http://www.w3.org" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span class="text-sm font-semibold">Filter Data</span>
                    </button>

                    <a href="<?php echo e(route('datapengajuan.formDataPengajuan')); ?>" class="h-10 px-4 flex items-center justify-center bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg shadow-md transition duration-150 gap-2">
                        
                        <svg xmlns="http://www.w3.org" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="text-sm font-semibold">Reset Filter</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-tl-md overflow-hidden border border-gray-100 mt-2 h-full flex flex-col">
        
        <?php if($submissions->isEmpty()): ?>
            <div class="flex flex-col items-center justify-center py-20 px-6 bg-white rounded-2xl border-2 border-dashed border-slate-100">
            
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-amber-100 rounded-full blur-2xl opacity-40 scale-150"></div>
                <div class="relative bg-amber-50 p-6 rounded-3xl border border-amber-100 shadow-inner">
                    <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>

            
            <div class="text-center max-w-sm">
                <span class="text-md font-black text-slate-800 uppercase tracking-widest mb-2">
                    Data Tidak Ditemukan
                </span>
                <p class="text-sm text-slate-400 leading-relaxed font-medium">
                    Sepertinya tidak ada data pengajuan yang sesuai dengan kriteria filter Anda saat ini.
                </p>
            </div>
        </div>

        <?php else: ?>
            
            <div class="flex-grow overflow-y-auto custom-scroll-container mt-2 px-2 space-y-3 pb-4">
                <?php $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="group relative flex items-center bg-white p-4 rounded-md border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300">

                        
                        <div class="absolute left-0 top-4 bottom-4 w-1 rounded-r-full
                            <?php echo e($submission['type'] === 'Cuti' ? 'bg-indigo-500' :
                            ($submission['type'] === 'Lembur' ? 'bg-orange-500' :
                            ($submission['type'] === 'Pensiun' ? 'bg-emerald-500' :
                            ($submission['type'] === 'PangkatGajiTunjangan' ? 'bg-cyan-500' : 'bg-blue-500')))); ?>">
                        </div>

                        
                        <div class="pl-3 w-[380px] shrink-0">
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="text-sm font-semibold">
                                    <?php echo e($submission['type'] == 'PangkatGajiTunjangan' ? ($submission['jenis_pengajuan'] ?? 'Kenaikan') : $submission['type']); ?>

                                </h4>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-500 uppercase tracking-tighter">
                                    <?php echo e(isset($submission['created_at']) ? \Carbon\Carbon::parse($submission['created_at'])->format('d M Y') : '-'); ?>

                                </span>
                            </div>

                            
                            <div class="text-[12px] text-slate-500 space-y-0.5">
                                <?php if($submission['type'] === 'Cuti'): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>Jenis Cuti : <b class="text-slate-700"><?php echo e($submission['jenis_cuti'] ?? 'Tidak ada keterangan'); ?></b></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>Tanggal : <b class="text-slate-700"><?php echo e(isset($submission['tanggal_mulai']) ? \Carbon\Carbon::parse($submission['tanggal_mulai'])->format('d F Y') : '-'); ?> - <?php echo e(isset($submission['tanggal_selesai']) ? \Carbon\Carbon::parse($submission['tanggal_selesai'])->format('d F Y') : '-'); ?></b></span>
                                    </div>
                                    <div class="flex items-center gap-2 italic">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>Alasan : <b class="text-slate-700">"<?php echo e($submission['keterangan'] ?? 'Tidak ada keterangan'); ?>"</b></span>
                                    </div>

                                <?php elseif($submission['type'] === 'Lembur'): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>Tanggal Lembur : <b class="text-slate-700"><?php echo e(isset($submission['tanggal_lembur']) ? \Carbon\Carbon::parse($submission['tanggal_lembur'])->format('d F Y') : '-'); ?></b></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>Jam : <b class="text-slate-700"><?php echo e($submission['jam_mulai'] ?? '-'); ?> - <?php echo e($submission['jam_selesai'] ?? '-'); ?></b></span>
                                    </div>
                                    <div class="flex items-center gap-2 italic">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>Tugas : <b class="text-slate-700">"<?php echo e($submission['uraian_tugas'] ?? 'Tidak ada keterangan'); ?>"</b></span>
                                    </div>

                                <?php elseif(($submission['type'] ?? '') === 'Pensiun'): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>TMT Pensiun : <b class="text-slate-700"><?php echo e(isset($submission['tmt_pensiun']) ? \Carbon\Carbon::parse($submission['tmt_pensiun'])->format('d F Y') : 'N/A'); ?></b></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>TMT Pegawai : <b class="text-slate-700"><?php echo e(isset($submission['tmt_pegawai']) ? \Carbon\Carbon::parse($submission['tmt_pegawai'])->format('d F Y') : 'N/A'); ?></b></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>Masa Kerja : <b class="text-slate-700"><?php echo e($submission['masa_kerja'] ?? 'N/A'); ?></b></span>
                                    </div>

                                <?php elseif(($submission['type'] ?? '') === 'PangkatGajiTunjangan'): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>TMT Pegawai : <b class="text-slate-700"><?php echo e(isset($submission['tmt_pegawai']) ? \Carbon\Carbon::parse($submission['tmt_pegawai'])->format('d F Y') : 'N/A'); ?></b></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span>Masa Kerja : <b class="text-slate-700"><?php echo e($submission['masa_kerja'] ?? 'N/A'); ?></b></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="px-8 hidden md:block border-l border-gray-100">
                            <div class="flex flex-col items-center">
                                <span class="text-[10px] font-bold text-slate-400 uppercase mb-1 tracking-tight">Status Pengajuan</span>
                                <div class="inline-flex items-center px-3 py-1 rounded-lg border <?php echo e($submission['blade_class'] ?? 'bg-amber-50 text-amber-700 border-amber-200'); ?> shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-2 animate-pulse"></span>
                                    <span class="text-[10px] font-extrabold uppercase whitespace-nowrap">
                                        <?php echo e($submission['blade_status_text']); ?> - <?php echo e($submission['blade_stage']); ?>

                                    </span>
                                </div>
                            </div>
                        </div>

                        
                        <div class="flex-1"></div>

                        
                        <div class="flex items-center gap-2 pl-6 shrink-0">
                            <?php if(isset($submission['blade_route'])): ?>
                                
                                
                                <a href="<?php echo e(route($submission['blade_route'], ($submission['type'] === 'Cuti') ? ($submission['id'] ?? $submission['nomor_urut_pegawai']) : ($submission['nomor_urut_pegawai'] ?? $submission['id']))); ?>"
                                class="px-5 py-1.5 bg-sky-100 hover:bg-sky-200 text-sky-700 text-[10px] font-bold uppercase tracking-widest rounded-full transition-all border border-sky-200">
                                Detail
                                </a>
                            <?php endif; ?>

                            <?php
                                $typeSegment = match($submission['type']) {
                                    'Cuti' => 'pengajuan-cuti',
                                    'Lembur' => 'pengajuan-lembur',
                                    'Pensiun' => 'pengajuan-pensiun',
                                    'PangkatGajiTunjangan' => 'pengajuan-pangkat',
                                    default => '',
                                };
                                $nup = $submission['nomor_urut_pegawai'] ?? '';
                            ?>

                            
                            <button onclick="event.preventDefault(); fetchAndOpenModal('<?php echo e($nup); ?>', '<?php echo e($typeSegment); ?>', '<?php echo e($submission['type']); ?>')"
                                    class="px-6 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold uppercase tracking-widest rounded-full transition-all shadow-sm shadow-emerald-100 active:scale-95">
                                Surat
                            </button>
                        </div>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="mt-0 px-4 py-3 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center gap-4 rounded-b-xl">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-white border border-slate-200 rounded-md text-[11px] font-bold text-slate-500 shadow-sm">
                        <?php echo e($submissions->total()); ?>

                    </span>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Records</span>
                </div>

                
                <div class="pagination-custom no-info">
                    <?php echo e($submissions->links()); ?>

                </div>
            </div>

        <?php endif; ?>

        <!-- Modal Container (Awal) -->
        <div id="view-leave-modal" class="fixed inset-0 bg-black-50 flex items-center justify-center hidden z-50 overflow-y-auto  backdrop-blur-sm">
            <div class="relative mx-auto p-5 w-11/12 md:max-w-4xl shadow-lg rounded-md bg-white">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-sm font-bold text-gray-900">Detail Surat Pengajuan <span id="modal-title-text"></span></span>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div id="modal-content-area" class="mt-4 p-4 max-h-[70vh] overflow-y-auto">
                    <div class="flex flex-col items-center justify-center h-18 text-gray-500">
                        <p class="text-sm font-semibold">Memuat data surat...</p>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div id="modal-footer" class="flex justify-end items-center pt-3 border-t text-sm mt-4">
                    <button
                        id="btn-download"
                        onclick="downloadPDF()"
                        class="flex items-center justify-center px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <!-- Kode SVG yang LENGKAP dan BENAR -->
                        <svg id="loading-spinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btn-text">Download PDF</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Modal Container (Akhir) -->
    </div>

    <script>
        window.appUrl = "<?php echo e(url('/')); ?>";
    </script>

    <?php $__env->startPush('scripts'); ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js', 'resources/js/filterpengajuan.js']); ?>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/datapengajuan/datapengajuan.blade.php ENDPATH**/ ?>