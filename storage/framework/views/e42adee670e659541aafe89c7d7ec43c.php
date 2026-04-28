<?php $__env->startSection('content'); ?>

<div class="bg-indigo-500 rounded-tl-md shadow-xl pl-4 pr-4 pb-4 pt-2">
    <span class="text-sm font-semibold text-white">#Section <?php echo e($pageTitle); ?></span>
    
    <p class="text-xs text-white">Tinjau informasi pengajuan dengan teliti sebelum memberikan keputusan.</p>
</div>

<?php
    // 1. AMBIL DATA TERAKHIR DULU
    $lastLog = $historiLog->last();

    // Gunakan null coalescing untuk menangani perbedaan nama kolom status di tabel Cuti dan lainnya
    $statusTerakhir = strtolower($lastLog->status_pengajuan ?? $lastLog->status_persetujuan ?? '');

    // 2. DEFINISIKAN VARIABLE CEK
    // Cek apakah tahap HRO sudah disetujui
    $isHRODone = ($statusTerakhir === 'disetujui' && $lastLog->tahap_persetujuan === 'HRO');

    // Cek apakah sudah masuk ke baris log "Selesai" (Tahap Akhir)
    $isSelesai = ($lastLog->tahap_persetujuan === 'Selesai');

    // Cek apakah ditolak di tahap mana pun
    $isFailFinal = ($statusTerakhir === 'ditolak');

    // 3. Tentukan Nama Tahap Berikutnya secara Dinamis
    // Untuk HRO, target berikutnya HANYA "Selesai" atau "-" jika sudah ditolak
    $nextStepName = '-';

    if (!$isFailFinal) {
        if ($isHRODone || $isSelesai) {
            $nextStepName = 'Selesai';
        } else {
            // Jika status masih diproses oleh HRO, maka langkah selanjutnya adalah Selesai
            $nextStepName = 'Selesai';
        }
    } else {
        $nextStepName = 'Ditolak';
    }
?>


<!-- Tracking Status Stepper -->
<div class="bg-white border-b border-gray-100 max-w-full py-4 shadow-sm">
    <div class="max-w-4xl mx-auto px-6">
        <div class="relative flex items-start">
            
            <?php $__currentLoopData = $historiLog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    // --- 1. DEFINISI STATUS (TETAP UTUH, TIDAK ADA YANG DIHAPUS) ---
                    $logStatus = strtolower($log->status_persetujuan ?? $log->status_pengajuan);
                    $isDone = ($logStatus === 'disetujui');
                    $isCurr = ($logStatus === 'diproses');
                    $isFail = ($logStatus === 'ditolak'); // Ini tetap ada untuk warna MERAH
                    $timeCol = ($sumber === 'pensiun') ? 'update_at' : 'updated_at'; // Ini tetap ada untuk TANGGAL
                    $nextLog = $historiLog[$index + 1] ?? null;
                    $nextStatus = $nextLog ? strtolower($nextLog->status_persetujuan ?? $nextLog->status_pengajuan) : null;

                    if ($isDone) {
                        $lineColor = ($nextStatus === 'disetujui' || $nextStatus === 'diproses' || $nextStatus === 'ditolak')
                                    ? 'bg-emerald-500' : 'bg-gray-200';
                    } elseif ($isCurr) {
                        $lineColor = 'bg-orange-500';
                    } elseif ($isFail) {
                        $lineColor = 'bg-red-500';
                    } else {
                        $lineColor = 'bg-gray-200';
                    }
                ?>

                <div class="flex flex-col items-center flex-1 relative">
                    
                    <?php if(!$loop->last): ?>
                        
                        <div class="absolute top-5 left-1/2 w-full h-[2.5px] z-0 <?php echo e($lineColor); ?>"></div>
                    <?php else: ?>
                        
                        
                        <?php if($log->tahap_persetujuan !== 'Selesai' && !$isFailFinal): ?>
                            <div class="absolute top-5 left-1/2 w-full h-[2.5px] z-0 <?php echo e($isCurr ? 'bg-orange-500' : 'bg-gray-200'); ?>"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    
                    <div class="relative flex items-center justify-center z-10">
                        <?php if($isCurr): ?>
                            <span class="absolute inline-flex h-9 w-9 rounded-full bg-orange-400 opacity-20 animate-ping"></span>
                        <?php endif; ?>

                        <div class="w-10 h-10 rounded-full <?php echo e($isDone ? 'bg-emerald-500 border-emerald-100' : ($isFail ? 'bg-red-500 border-red-100' : 'bg-orange-500 border-orange-100')); ?> border-[5px] flex items-center justify-center shadow-sm transition-all duration-300">
                            <?php if($isDone): ?>
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <?php elseif($isFail): ?>
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <?php else: ?>
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="mt-4 text-center px-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tahap <?php echo e($index + 1); ?></p>
                        <p class="text-[11px] font-semibold text-gray-900 mt-1 leading-tight"><?php echo e($log->tahap_persetujuan == 'Pengajuan Awal' ? 'Pengajuan' : $log->tahap_persetujuan); ?></p>
                        <p class="text-[9px] font-bold <?php echo e($isDone ? 'text-emerald-600' : ($isFail ? 'text-red-600' : 'text-orange-600')); ?> mt-1 italic uppercase"><?php echo e($logStatus); ?></p>

                        
                        <?php if($log->$timeCol): ?>
                            <p class="text-[8px] text-gray-400 mt-1"><?php echo e(\Carbon\Carbon::parse($log->$timeCol)->format('d/m H:i')); ?> WIB</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if(count($historiLog) == 1 && $statusTerakhir !== 'ditolak'): ?>
                <div class="flex flex-col items-center flex-1 relative">
                    
                    <div class="absolute top-5 left-1/2 w-full h-[2.5px] z-0 bg-gray-100"></div>
                    
                    <div class="relative flex items-center justify-center z-10">
                        
                        <span class="absolute inline-flex h-9 w-9 rounded-full bg-orange-400 opacity-20 animate-ping"></span>
                        
                        <div class="w-10 h-10 rounded-full bg-orange-500 border-[5px] border-orange-100 flex items-center justify-center shadow-sm transition-all duration-300">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                        </div>
                    </div>
                    <div class="mt-4 text-center px-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tahap 2</p>
                        <p class="text-[11px] font-semibold text-gray-900 mt-1 leading-tight"><?php echo e($tahapTeks); ?></p>
                        
                        <p class="text-[9px] font-bold text-orange-600 mt-1 italic uppercase">DIPROSES</p>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if(!$isHRODone && !$isFailFinal && $lastLog->tahap_persetujuan !== 'Selesai'): ?>
                <div class="flex flex-col items-center flex-1 relative">
                    <div class="w-10 h-10 rounded-full bg-gray-100 border-[5px] border-white flex items-center justify-center z-10 shadow-sm transition-all duration-300">
                        <div class="w-2 h-2 bg-white rounded-full"></div>
                    </div>
                    <div class="mt-4 text-center opacity-40">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Akhir</p>
                        <p class="text-[11px] font-semibold text-gray-400 mt-1 leading-tight">Selesai</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Container: Kita tambah tingginya ke h-[580px] agar pas untuk textarea 5 baris + 2 tombol di kanan -->
<div class="max-w-full flex flex-col h-[450px] overflow-hidden bg-white border border-slate-200 shadow-xl">
    <div class="grid grid-cols-1 lg:grid-cols-3 h-full divide-x divide-slate-100">

        <!-- Kolom Kiri: Informasi (Clean White/Gray Theme) -->
        <div class="lg:col-span-2 flex flex-col min-h-0 overflow-y-auto p-4 bg-slate-50/30">
            <!-- Header -->
            <div class="mb-4 flex justify-between items-start">
                <div>
                    <span class="text-xl font-bold text-slate-800"><?php echo e($data->nama); ?></span>
                    <p class="text-xs text-slate-500 font-mono tracking-wider"><?php echo e($data->nomor_urut_pegawai); ?> | <?php echo e($data->nama_divisi); ?></p>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter <?php echo e($sumber == 'cuti' ? 'bg-purple-100 text-purple-600 border border-purple-200' : 'bg-cyan-100 text-cyan-600 border border-cyan-200'); ?>">
                    <?php echo e($sumber); ?>

                </span>
            </div>

            <!-- Grid Detail -->
            <div class="grid grid-cols-2 gap-8 mb-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jabatan</label>
                    <p class="text-sm text-slate-700 font-medium"><?php echo e($data->jabatan); ?></p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jenis Pengajuan</label>
                    <p class="text-sm text-indigo-600 font-medium italic">
                        <?php if($sumber == 'cuti'): ?>
                            
                            <?php echo e($data->jenis_cuti_nama ?? $data->jenis_cuti); ?>

                        <?php elseif($sumber == 'lembur'): ?>
                            Lembur Kerja
                        <?php else: ?>
                            <?php echo e($data->jenis_pengajuan ?? ucfirst($sumber)); ?>

                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Content Card dengan Perbaikan Aksen Garis -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative">

                <!-- Perbaikan Aksen Garis: Menambahkan margin top/bottom dan rounded agar lebih 'soft' -->
                <div class="absolute left-0 top-4 bottom-4 w-1.5 bg-indigo-500 rounded-r-full shadow-[2px_0_8px_rgba(99,102,241,0.4)]"></div>

                <?php if($sumber == 'cuti'): ?>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pl-4"> <!-- Tambah padding left agar tidak menempel garis -->

                        <!-- Blok Tanggal -->
                        <div class="flex items-center space-x-4">
                            <!-- Mulai -->
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <svg xmlns="http://www.w3.org" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mulai Cuti</span>
                                    <span class="text-sm font-bold text-slate-800"><?php echo e(\Carbon\Carbon::parse($data->tanggal_mulai)->locale('id')->translatedFormat('l, d M Y')); ?></span>
                                </div>
                            </div>

                            <div class="text-slate-300">
                                <svg xmlns="http://www.w3.org" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </div>

                            <!-- Kembali -->
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                                    <svg xmlns="http://www.w3.org" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Selesai Cuti</span>
                                    <span class="text-sm font-bold text-emerald-600"><?php echo e(\Carbon\Carbon::parse($data->tanggal_selesai)->locale('id')->translatedFormat('l, d M Y')); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Durasi (Badge) -->
                        <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100 shadow-sm">
                            <span class="text-xs font-bold text-indigo-700">
                                <?php echo e(\Carbon\Carbon::parse($data->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($data->tanggal_selesai)) + 1); ?> Hari Cuti
                            </span>
                        </div>
                    </div>

                    <hr class="my-3 border-slate-100 ml-3">

                    <!-- Blok Alasan -->
                    <div class="ml-4 bg-slate-50/50 p-3 rounded-2xl border border-slate-100">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Alasan Pengajuan</label>
                        <p class="text-sm text-slate-600 leading-relaxed italic">
                            "<?php echo e($data->keterangan ?? '-'); ?>"
                        </p>
                    </div>
                <?php else: ?>
                    <!-- Blok Deskripsi & Detail Waktu Lembur -->
                    <div class="ml-4 bg-blue-50/50 p-6 rounded-2xl border border-blue-100 space-y-5">
                        <!-- 1. Uraian Tugas -->
                        <div>
                            <label class="block text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-2">Deskripsi Pengajuan Lembur</label>
                            <p class="text-sm text-slate-700 leading-relaxed italic">
                                "<?php echo e($data->uraian_tugas ?? 'Tidak ada deskripsi'); ?>"
                            </p>
                        </div>

                        <!-- 2. Grid Detail Waktu (Sesuai Gambar Lu) -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-blue-100/50">
                            <!-- Tanggal -->
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Lembur</span>
                                <span class="text-sm font-semibold text-slate-700 mt-1">
                                    <?php echo e(\Carbon\Carbon::parse($data->tanggal_lembur)->translatedFormat('d F Y') ?? '-'); ?>

                                </span>
                            </div>

                            <!-- Jam Mulai -->
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Jam Mulai</span>
                                <span class="text-sm font-semibold text-emerald-600 mt-1">
                                    <?php echo e($data->jam_mulai ? date('H:i', strtotime($data->jam_mulai)) : '-'); ?> WIB
                                </span>
                            </div>

                            <!-- Jam Selesai -->
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Jam Selesai</span>
                                <span class="text-sm font-semibold text-rose-600 mt-1">
                                    <?php echo e($data->jam_selesai ? date('H:i', strtotime($data->jam_selesai)) : '-'); ?> WIB
                                </span>
                            </div>

                            <!-- Total Jam -->
                            <div class="flex flex-col bg-blue-600/5 p-2 rounded-xl border border-blue-200/50">
                                <span class="text-[9px] font-bold text-blue-500 uppercase tracking-wider">Total Lembur</span>
                                <span class="text-sm font-bold text-blue-700 mt-1">
                                    <?php echo e($data->total_jam_lembur ?? '0 jam 0 menit'); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- KOLOM KANAN: Action (Scroll Mandiri) -->
        <div class="lg:col-span-1 flex flex-col bg-slate-50 overflow-hidden border-l border-slate-100 h-full">

            <!-- 1. TITLE KEPUTUSAN -->
            <div class="p-5 bg-white border-b border-slate-200 flex-none shadow-sm">
                <h3 class="text-[12px] font-bold text-indigo-600 uppercase tracking-[0.2em]">
                    
                    Keputusan <?php echo e(str_replace('Verifikasi ', '', $tahapTeks)); ?>

                </h3>
            </div>

            <!-- 2. AREA FORM (Scroll Mandiri) -->
            <div class="flex-1 overflow-y-auto custom-scroll-container p-3 pb-5">
                
                <form id="formApproval" action="<?php echo e(route('hro.updateStatus', ['sumber' => $sumber, 'id_log' => $id_log])); ?>" method="POST" class="flex flex-col">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    
                    <?php if($sumber === 'lembur'): ?>
                        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest">Verifikasi Waktu Lembur</label>
                                <button type="button" id="btnEditTime" onclick="toggleEditTime()"
                                    class="text-[11px] font-extrabold text-indigo-600 hover:text-indigo-800 uppercase tracking-widest bg-indigo-50 px-3 py-1.5 rounded-lg transition-all active:scale-95">
                                    Ubah
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <span class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">Jam Mulai</span>
                                    <input type="time" name="jam_mulai" id="input_jam_mulai"
                                        value="<?php echo e(isset($data->jam_mulai) ? date('H:i', strtotime($data->jam_mulai)) : '00:00'); ?>"
                                        disabled
                                        class="w-full p-3 text-sm border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none bg-slate-50 font-medium text-slate-500 disabled:opacity-70 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">Jam Selesai</span>
                                    <input type="time" name="jam_selesai" id="input_jam_selesai"
                                        value="<?php echo e(isset($data->jam_selesai) ? date('H:i', strtotime($data->jam_selesai)) : '00:00'); ?>"
                                        disabled
                                        class="w-full p-3 text-sm border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none bg-slate-50 font-medium text-slate-500 disabled:opacity-70 transition-all">
                                </div>
                            </div>

                            <div class="pt-2">
                                <span class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">Total Jam Lembur</span>
                                <input type="text" name="total_jam_lembur" id="input_total_jam"
                                    value="<?php echo e($data->total_jam_lembur ?? '0 jam 0 menit'); ?>" readonly
                                    class="w-full mt-2 p-4 text-sm bg-slate-50 border border-slate-100 rounded-xl font-bold text-slate-400 outline-none transition-all">
                            </div>
                        </div>

                    
                    <?php elseif($sumber === 'cuti'): ?>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest">Catatan Penyetuju HRO</label>
                            <textarea name="catatan" rows="5"
                                class="w-full p-5 text-sm bg-white border border-slate-200 rounded-3xl text-slate-700 focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition resize-none placeholder-slate-400 shadow-sm"
                                placeholder="Berikan alasan atau catatan verifikasi..."
                                <?php if($data->status !== 'diproses'): echo 'disabled'; endif; ?>><?php echo e($data->komentar ?? ''); ?></textarea>
                        </div>
                    <?php endif; ?>

                    <input type="hidden" name="status" id="status_input">

                    <!-- 3. ACTION BUTTONS -->
                    <div class="grid grid-cols-1 gap-3 pt-4">
                        
                        <button type="button" onclick="handleApproval('disetujui')"
                            <?php if($data->status !== 'diproses'): echo 'disabled'; endif; ?>
                            class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-200 disabled:text-slate-400 text-white text-xs font-semibold rounded-xl transition-all shadow-lg shadow-indigo-200 uppercase tracking-widest">
                            Setujui Pengajuan
                        </button>

                        <button type="button" onclick="handleApproval('ditolak')"
                            <?php if($data->status !== 'diproses'): echo 'disabled'; endif; ?>
                            class="w-full py-4 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 disabled:border-slate-100 disabled:text-slate-300 text-xs font-semibold rounded-xl transition-all shadow-sm uppercase tracking-widest">
                            Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    const jamMulai = document.getElementById('input_jam_mulai');
    const jamSelesai = document.getElementById('input_jam_selesai');
    const totalJam = document.getElementById('input_total_jam');
    const btnEdit = document.getElementById('btnEditTime');

    // 1. Logika Toggle Edit
    function toggleEditTime() {
        const isLocked = jamMulai.disabled;

        if (isLocked) {
            // UNLOCK (Buka Gembok)
            jamMulai.disabled = false;
            jamSelesai.disabled = false;
            // Ubah gaya visual saat aktif
            [jamMulai, jamSelesai].forEach(el => {
                el.classList.remove('bg-slate-50', 'text-slate-500');
                el.classList.add('bg-white', 'text-slate-700');
            });
            totalJam.classList.add('text-indigo-600', 'bg-indigo-50/50', 'border-indigo-100');
            btnEdit.innerText = 'Batal';
            btnEdit.classList.replace('bg-indigo-50', 'bg-rose-50');
            btnEdit.classList.replace('text-indigo-600', 'text-rose-600');
        } else {
            // LOCK (Kunci Lagi & Reset)
            jamMulai.disabled = true;
            jamSelesai.disabled = true;
            [jamMulai, jamSelesai].forEach(el => {
                el.classList.add('bg-slate-50', 'text-slate-500');
                el.classList.remove('bg-white', 'text-slate-700');
            });
            totalJam.classList.remove('text-indigo-600', 'bg-indigo-50/50', 'border-indigo-100');
            btnEdit.innerText = 'Ubah';
            btnEdit.classList.replace('bg-rose-50', 'bg-indigo-50');
            btnEdit.classList.replace('text-rose-600', 'text-indigo-600');
        }
    }

    // 2. Logika Hitung (Sama seperti sebelumnya)
    function calculateLembur() {
        if (!jamMulai.value || !jamSelesai.value) return;
        const [hStart, mStart] = jamMulai.value.split(':').map(Number);
        const [hEnd, mEnd] = jamSelesai.value.split(':').map(Number);
        let totalMinutesStart = (hStart * 60) + mStart;
        let totalMinutesEnd = (hEnd * 60) + mEnd;
        if (totalMinutesEnd < totalMinutesStart) totalMinutesEnd += 24 * 60;
        const diffMinutes = totalMinutesEnd - totalMinutesStart;
        const hours = Math.floor(diffMinutes / 60);
        const minutes = diffMinutes % 60;
        totalJam.value = hours + " jam " + minutes + " menit";
    }

    if(jamMulai && jamSelesai) {
        jamMulai.addEventListener('input', calculateLembur);
        jamSelesai.addEventListener('input', calculateLembur);
        calculateLembur();
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app-hro', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/hro/detail_approval.blade.php ENDPATH**/ ?>