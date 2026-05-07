<?php $__env->startSection('content'); ?>

<div class="h-screen max-w-full bg-gray-50 rounded-tl-md flex flex-col p-4 overflow-hidden">

    <!-- Summary Cards (Lebih Compact) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
        <!-- Card Disetujui -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-500 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">TOTAL DISETUJUI</p>
                    <h3 class="text-2xl font-black text-gray-800 leading-none"><?php echo e($totalDisetujui); ?></h3>
                </div>
                <div class="text-green-500 opacity-50">
                    <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                </div>
            </div>
            <div class="mt-3 flex gap-4 text-[11px] border-t pt-2 text-gray-500">
                <?php $__currentLoopData = $detailDisetujui; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span><?php echo e($detail['label']); ?>: <b class="text-gray-700"><?php echo e($detail['jumlah']); ?></b></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Card Ditolak -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-red-500 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">TOTAL DITOLAK</p>
                    <h3 class="text-2xl font-black text-gray-800 leading-none"><?php echo e($totalDitolak); ?></h3>
                </div>
                <div class="text-red-500 opacity-50">
                    <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                </div>
            </div>
            <div class="mt-3 flex gap-4 text-[11px] border-t pt-2 text-gray-500">
                <?php $__currentLoopData = $detailDitolak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span><?php echo e($detail['label']); ?>: <b class="text-gray-700"><?php echo e($detail['jumlah']); ?></b></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- Filter & Table Container -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 flex flex-col h-full overflow-hidden">

        <!-- Bagian Form Filter (Tinggi tetap sesuai konten) -->
        <form action="" method="GET" class="p-3 border-b bg-white shadow-sm rounded-t-lg flex-none">
            <div class="flex items-end gap-2 flex-wrap md:flex-nowrap">
                <!-- Pencarian -->
                <div class="flex-1 min-w-[150px]">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-0.5 block ml-1">Pencarian</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nama / Nomor Urut Pegawai..."
                        class="text-[11px] border border-gray-200 rounded-md px-2 w-full h-8 focus:ring-1 focus:ring-blue-400 outline-none transition-all">
                </div>

                <!-- Jenis Pengajuan -->
                <div class="w-28">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-0.5 block ml-1">Jenis Pengajuan</label>
                    <select name="jenis" class="text-[11px] border border-gray-200 rounded-md px-2 w-full h-8 outline-none bg-white cursor-pointer">
                        <option value="">Semua Jenis</option>
                        <option value="Cuti" <?php echo e(request('jenis') == 'Cuti' ? 'selected' : ''); ?>>Cuti</option>
                        <option value="Lembur" <?php echo e(request('jenis') == 'Lembur' ? 'selected' : ''); ?>>Lembur</option>
                        <option value="pensiun" <?php echo e(request('jenis') == 'pensiun' ? 'selected' : ''); ?>>Pensiun</option>
                        <option value="pangkat" <?php echo e(request('jenis') == 'pangkat' ? 'selected' : ''); ?>>Kenaikan Pangkat, Gaji dan Tunjangan</option>
                    </select>
                </div>

                <!-- ➕ Filter Divisi -->
                <div class="w-36">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-0.5 block ml-1">Divisi</label>
                    <select name="divisi_filter" class="text-[11px] border border-gray-200 rounded-md px-2 w-full h-8 outline-none bg-white cursor-pointer">
                        <option value="">Semua Divisi</option>
                        <?php $__currentLoopData = $listDivisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $div): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($div->nama_divisi); ?>" <?php echo e(request('divisi_filter') == $div->nama_divisi ? 'selected' : ''); ?>>
                                <?php echo e($div->nama_divisi); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Status -->
                <div class="w-28">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-0.5 block ml-1">Status Pengajuan</label>
                    <select name="status" class="text-[11px] border border-gray-200 rounded-md px-2 w-full h-8 outline-none bg-white cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="disetujui" <?php echo e(request('status') == 'disetujui' ? 'selected' : ''); ?>>Disetujui</option>
                        <option value="ditolak" <?php echo e(request('status') == 'ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                    </select>
                </div>

                <!-- Rentang Tanggal -->
                <div class="w-64">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-0.5 block ml-1">Rentang Tanggal</label>
                    <div class="flex items-center gap-1">
                        <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="text-[10px] border border-gray-200 rounded-md px-1 w-full h-8 outline-none">
                        <span class="text-gray-300 text-xs">-</span>
                        <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="text-[10px] border border-gray-200 rounded-md px-1 w-full h-8 outline-none">
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center gap-1 ml-auto">
                    <button type="submit" class="bg-blue-600 text-white text-[11px] font-bold rounded-md px-3 h-8 hover:bg-blue-700 transition flex items-center gap-1 shadow-sm" title="Filter Data">
                        <svg xmlns="http://w3.org" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                        Filter
                    </button>
                    <a href="<?php echo e(request()->url()); ?>" class="bg-gray-100 text-gray-600 text-[11px] font-bold rounded-md px-3 h-8 hover:bg-gray-200 transition flex items-center gap-1 border border-gray-200" title="Reset">
                        <svg xmlns="http://w3.org" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Refresh
                    </a>
                    <a href="<?php echo e(route('laporan.cetak', request()->query())); ?>" target="_blank" class="bg-red-600 text-white text-[11px] font-bold rounded-md px-3 h-8 hover:bg-red-700 transition flex items-center gap-1 shadow-sm" title="Cetak PDF">
                        <svg xmlns="http://w3.org" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Cetak
                    </a>
                </div>
            </div>
        </form>

        <!-- AREA SCROLL TABEL (Otomatis mengambil sisa ruang yang ada) -->
        <div class="flex-1 overflow-y-auto min-h-0 custom-scroll-container">
            <table class="w-full text-left">
                <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                    <tr class="text-[10px] font-bold text-gray-500 uppercase">
                        <th class="px-4 py-3">Tanggal Pengajuan</th>
                        <th class="px-4 py-3">Pegawai</th>
                        <th class="px-4 py-3">Divisi/Jabatan</th>
                        <th class="px-4 py-3">Jenis Pengajuan</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-center">Status Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $dataPengajuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-4 py-3 text-gray-600 text-[12px]"><?php echo e(date('d/m/y', strtotime($row->tanggal))); ?></td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800"><?php echo e($row->nama); ?></div>
                                <div class="text-[10px] text-gray-400"><?php echo e($row->nup); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-gray-700"><?php echo e($row->nama_divisi); ?></div>
                                <div class="text-[10px] text-gray-400 italic"><?php echo e($row->jabatan); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo e($row->sumber == 'cuti' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'); ?>">
                                    <?php echo e($row->jenis); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($row->sumber == 'lembur'): ?>
                                    
                                    <div class="text-[11px] font-bold text-gray-700">
                                        <?php echo e(\Carbon\Carbon::parse($row->tgl_awal)->translatedFormat('l')); ?>, <?php echo e(date('d/m/y', strtotime($row->tgl_awal))); ?>

                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="bg-indigo-100 text-indigo-700 text-[10px] px-1.5 py-0.5 rounded-md font-extrabold border border-indigo-200">
                                            <?php echo e($row->total_jam_lembur ?? '0'); ?>

                                        </span>
                                        <span class="text-[10px] text-gray-400 uppercase italic tracking-tighter">Total Lembur</span>
                                    </div>

                                <?php elseif($row->sumber == 'cuti'): ?>
                                    
                                    <div class="text-[10px] text-gray-400 uppercase italic tracking-tighter mb-0.5">Rentang Cuti</div>
                                    <div class="text-[11px] font-bold text-gray-700 leading-tight">
                                        <?php echo e(\Carbon\Carbon::parse($row->tgl_awal)->translatedFormat('l')); ?>, <?php echo e(date('d/m/y', strtotime($row->tgl_awal))); ?>

                                        <div class="text-[10px] text-gray-300 font-normal my-0.5">s/d</div>
                                        <?php echo e(\Carbon\Carbon::parse($row->tgl_akhir)->translatedFormat('l')); ?>, <?php echo e(date('d/m/y', strtotime($row->tgl_akhir))); ?>

                                    </div>

                                <?php elseif($row->sumber == 'pensiun'): ?>
                                    
                                    <div class="text-[10px] text-gray-700 font-bold uppercase italic tracking-tighter mb-0.5">TMT Pensiun</div>
                                    <div class="text-[11px] font-bold text-gray-700">
                                        <?php echo e(date('d/m/y', strtotime($row->tgl_awal))); ?>

                                    </div>

                                <?php elseif($row->sumber == 'pangkat'): ?>
                                    
                                    <div class="text-[10px] text-gray-700 font-bold uppercase italic tracking-tighter mb-0.5">TMT Kenaikan</div>
                                    <div class="text-[11px] font-bold text-gray-700">
                                        <?php echo e(date('d/m/y', strtotime($row->tgl_awal))); ?>

                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="relative group inline-block">
                                    
                                    <span class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase border cursor-help <?php echo e($row->status == 'disetujui' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200'); ?>">
                                        <?php echo e($row->status); ?>

                                    </span>

                                    
                                    <div class="invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-300 absolute z-50 right-0 top-full mt-3 p-6 bg-white shadow-2xl rounded-2xl border border-gray-100 pointer-events-none w-max overflow-visible">

                                        <div class="flex items-start">
                                            <?php $__currentLoopData = $row->histori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="flex flex-col items-center relative">

                                                    
                                                    <div class="flex items-center justify-center w-32 h-5 relative">

                                                        
                                                        <?php if(!$loop->last): ?>
                                                            <?php
                                                                $nextStep = $row->histori[$index+1] ?? null;
                                                                $nextStatus = $nextStep ? (strtolower($row->sumber) == 'cuti' ? $nextStep->status_pengajuan : $nextStep->status_persetujuan) : '';

                                                                // 🔄 PERBAIKAN: Gunakan warna abu-abu yang lebih gelap (gray-300) agar terlihat sama tebalnya dengan hijau
                                                                $lineColor = ($nextStatus == 'disetujui' || $nextStatus == 'diproses') ? 'bg-green-500' : 'bg-gray-300';
                                                            ?>

                                                            
                                                            <div class="absolute left-1/2 w-full h-[2px] <?php echo e($lineColor); ?> z-0"></div>
                                                        <?php endif; ?>

                                                        <?php
                                                            $stLog = (strtolower($row->sumber) == 'cuti') ? $h->status_pengajuan : $h->status_persetujuan;

                                                            // Penentuan Warna Dot & Efek Glow
                                                            if ($stLog == 'disetujui') {
                                                                $dotColor = 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]';
                                                            } elseif ($stLog == 'ditolak') {
                                                                $dotColor = 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]'; // Glow Merah jika Ditolak
                                                            } else {
                                                                $dotColor = 'bg-yellow-400 shadow-[0_0_8px_rgba(250,204,21,0.4)]'; // Glow Kuning jika Diproses
                                                            }

                                                            // Penentuan Warna Garis Penghubung
                                                            $nextStep = $row->histori[$index+1] ?? null;
                                                            $nextStatus = $nextStep ? (strtolower($row->sumber) == 'cuti' ? $nextStep->status_pengajuan : $nextStep->status_persetujuan) : '';

                                                            if ($nextStatus == 'disetujui') {
                                                                $lineColor = 'bg-green-500';
                                                            } elseif ($nextStatus == 'ditolak') {
                                                                $lineColor = 'bg-red-500'; // Garis jadi merah menuju titik penolakan
                                                            } else {
                                                                $lineColor = 'bg-gray-300';
                                                            }
                                                        ?>

                                                        <div class="w-4 h-4 rounded-full border-2 border-white shadow-md z-10 flex-shrink-0 <?php echo e($dotColor); ?>"></div>
                                                    </div>

                                                    
                                                    <div class="mt-3 text-center w-32">
                                                        <p class="text-[11px] font-extrabold text-gray-800 leading-tight uppercase tracking-tight break-words px-1">
                                                            <?php echo e(str_replace(['Direktur ', 'Kepala '], ['Dir. ', 'Kpl. '], $h->tahap_persetujuan)); ?>

                                                        </p>
                                                        <p class="text-[10px] text-gray-500 mt-1 italic font-medium leading-tight line-clamp-2 px-1">
                                                            <?php echo e($h->nama_penyetuju ?? 'Proses'); ?>

                                                        </p>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>

                                        
                                        <div class="absolute -top-1.5 right-6 w-3 h-3 bg-white border-l border-t border-gray-100 rotate-45"></div>
                                    </div>
                                </div>
                            </td>


                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-400 italic text-xs">Data pengajuan tidak ditemukan..</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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



<?php echo $__env->make($layout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/laporan/laporanpengajuan.blade.php ENDPATH**/ ?>