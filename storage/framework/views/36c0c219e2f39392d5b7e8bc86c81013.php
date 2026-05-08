<?php $__env->startSection('content'); ?>



<div x-data="{
    search: '',
    status: '',
    filterRow(nama, nup, statusRow) {
        const keyword = this.search.toLowerCase();
        const matchSearch = nama.toLowerCase().includes(keyword) || nup.toString().includes(keyword);
        const matchStatus = this.status === '' || statusRow === this.status;
        return matchSearch && matchStatus;
    }
}" class="h-full w-full bg-[#f8fafc] rounded-l-md shadow-2xl flex flex-col overflow-hidden ">

    <?php
        $statusConfigs = [
            'Pegawai Tetap' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'dot' => 'bg-orange-500'],
            'Pegawai Kontrak' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-400'],
            'Pegawai Harian Lepas' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'dot' => 'bg-purple-500'],
            'Pegawai Bulanan' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
            'Pegawai Alih Daya' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500']
        ];
    ?>

    <!-- HEADER & STATISTIK -->
    <div class="p-6 bg-gradient-to-br from-[#001A4E] via-[#002b7a] to-[#001A4E] flex-none relative overflow-hidden">
        <!-- Efek Dekoratif (Diperbesar kembali untuk suasana premium) -->
        <div class="absolute -top-10 -left-10 w-64 h-64 bg-blue-500/10 rounded-full blur-[80px]"></div>
        <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-[80px]"></div>

        <div class="relative z-10">
            <!-- Baris Atas: Lebih Lega -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 bg-blue-400 rounded-full shadow-[0_0_15px_rgba(96,165,250,0.6)]"></div>
                    <h2 class="text-3xl font-black text-white tracking-tighter">
                        <?php echo e($pegawaiDivisi->total()); ?> <span class="text-blue-100 font-light text-xl ml-2">Pegawai</span>
                    </h2>
                </div>

                <!-- SEARCH & FILTER BOX -->
                <form action="<?php echo e(route('pegawai.data')); ?>" method="GET" class="flex items-center gap-3">
                    <?php
                        $userRoute = auth()->user()->rolesMapping->route_name ?? '';
                        $isManagerDivisi = str_contains($userRoute, 'manager');
                    ?>

                    <?php if(!$isManagerDivisi): ?>
                        <div class="relative group">
                            <select name="divisi" onchange="this.form.submit()"
                                class="block w-full md:w-48 pl-4 pr-10 py-2.5 bg-white/10 border border-white/20 rounded-2xl text-xs text-white backdrop-blur-md focus:ring-4 focus:ring-blue-500/30 outline-none cursor-pointer transition-all appearance-none shadow-xl">
                                <option value="" class="text-slate-800">Semua Divisi</option>
                                <?php $__currentLoopData = $allDivisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($d->id_divisi); ?>" <?php echo e(request('divisi') == $d->id_divisi ? 'selected' : ''); ?> class="text-slate-800">
                                        <?php echo e($d->nama_divisi); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-blue-300">
                                <i class="fa fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Search Box (Tetap muncul untuk semua orang) -->
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa fa-search text-blue-200 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari Pegawai..."
                            class="block w-full md:w-64 pl-11 pr-4 py-2.5 bg-white/10 border border-white/20 rounded-2xl text-sm text-white placeholder:text-blue-100/40 backdrop-blur-md focus:ring-4 focus:ring-blue-500/30 outline-none transition-all shadow-xl">
                    </div>

                    <!-- Tombol Reset -->
                    <?php if(request('search') || request('divisi')): ?>
                        <a href="<?php echo e(route('pegawai.data')); ?>" class="px-4 py-2.5 bg-red-500 text-white rounded-2xl text-xs font-black hover:bg-red-600 transition-all flex items-center gap-2 shadow-lg uppercase tracking-wider">
                            RESET
                        </a>
                    <?php endif; ?>
                </form>

            </div>

            <!-- STATS CARDS -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <?php $__currentLoopData = $statusConfigs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $style): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white/10 border border-white/10 backdrop-blur-xl p-4 rounded-2xl hover:bg-white/20 transition-all duration-300 group shadow-lg">
                    <div class="flex items-center gap-2 mb-2.5">
                        <span class="w-2 h-2 rounded-full <?php echo e($style['dot']); ?> shadow-[0_0_10px_currentColor]"></span>
                        <span class="text-[10px] font-black text-blue-100 uppercase tracking-widest leading-none"><?php echo e($label); ?></span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-white tracking-tight"><?php echo e($stats[$label] ?? 0); ?></span>
                        <span class="text-[10px] font-bold text-blue-300 uppercase">Orang</span>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-full h-1 bg-white/10 rounded-full mt-3 overflow-hidden">
                        <div class="h-full <?php echo e($style['dot']); ?> opacity-70" style="width: <?php echo e($pegawaiDivisi->total() > 0 ? (($stats[$label] ?? 0) / $pegawaiDivisi->total()) * 100 : 0); ?>%"></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- AREA DATA TABEL DENGAN CUSTOM SCROLL -->
    <div class="custom-scroll-area flex flex-col overflow-hidden">
        <div class="flex-1 overflow-y-auto custom-scroll-container">
            <!-- TABEL DATA -->
            <table class="w-full text-left border-separate border-spacing-0 table-fixed">
                <thead class="sticky top-0 z-30">
                    <tr class="bg-slate-50/90 backdrop-blur-md">
                        <th class="w-[18%] py-3 px-6 text-blue-900 text-[10px] uppercase font-black tracking-widest border-b border-slate-200">Nomor Urut Pegawai</th>
                        <th class="w-[22%] py-3 px-6 text-blue-900 text-[10px] uppercase font-black tracking-widest border-b border-slate-200">Nama Pegawai</th>
                        <th class="w-[30%] py-3 px-6 text-blue-900 text-[10px] uppercase font-black tracking-widest border-b border-slate-200">Jabatan & Golongan</th>
                        <th class="w-[18%] py-3 px-6 text-blue-900 text-[10px] uppercase font-black tracking-widest border-b border-slate-200 text-center">Status</th>
                        <th class="w-[15%] py-3 px-6 text-center text-blue-900 text-[10px] uppercase font-black tracking-widest border-b border-slate-200">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__currentLoopData = $pegawaiDivisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-blue-50/50 transition-all duration-200 group"> 
                        <td class="py-3 px-6">
                            <span class="font-mono text-[13px] font-black <?php echo e($p->level_id == 3 ? 'text-blue-700' : 'text-slate-500'); ?> tracking-tight">
                                #<?php echo e($p->nomor_urut_pegawai); ?>

                            </span>
                        </td>
                        <td class="py-3 px-6">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-700 text-[13px] tracking-tight group-hover:text-blue-700 uppercase leading-tight"><?php echo e($p->nama); ?></span>
                                <?php if($p->level_id == 3): ?>
                                    <span class="mt-1 w-fit px-1.5 py-0.5 bg-red-50 text-red-600 text-[8px] font-black uppercase rounded border border-red-100">Top Management</span>
                                <?php else: ?>
                                    <span class="mt-1 text-[10px] text-slate-400 font-medium italic leading-none"><?php echo e($p->nama_divisi); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="py-3 px-6">
                            <div class="text-[12px] text-slate-600 font-bold leading-snug line-clamp-1" title="<?php echo e($p->jabatan); ?>"><?php echo e($p->jabatan); ?></div>
                            <div class="flex items-center gap-2 mt-1">
                                <?php if($p->grade): ?>
                                    <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded border border-blue-100">Grade <?php echo e($p->grade); ?></span>
                                    <span class="text-[10px] text-slate-400 font-medium italic opacity-80"><?php echo e($p->pangkat); ?></span>
                                <?php else: ?>
                                    <span class="text-[10px] text-slate-300 font-medium italic">Struktur Direksi</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <?php $currStyle = $statusStyles[$p->status_pegawai] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-400', 'border' => 'border-slate-100']; ?>
                            <span class="<?php echo e($currStyle['bg']); ?> <?php echo e($currStyle['text']); ?> text-[10px] px-3 py-1.5 rounded-full font-black uppercase border <?php echo e($currStyle['border']); ?> shadow-sm">
                                <?php echo e($p->status_pegawai ?? 'Aktif'); ?>

                            </span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <a href="<?php echo e(route('pegawai.detail', $p->nomor_urut_pegawai)); ?>" class="inline-flex items-center gap-1.5 bg-white text-slate-600 border border-slate-200 px-3 py-1.5 rounded-lg text-[10px] font-bold hover:bg-[#001A4E] hover:text-white transition-all shadow-sm">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <!-- EMPTY STATE -->
            <div x-cloak x-show="(search !== '' || status !== '') && $el.parentElement.querySelectorAll('tbody tr[x-show]:not([style*=\'display: none\'])').length === 0" class="py-24 text-center">
                <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <i class="fa fa-user-slash text-2xl text-slate-200"></i>
                </div>
                <h3 class="text-slate-800 font-bold">Data Tidak Ditemukan</h3>
                <p class="text-slate-400 text-xs mt-1">Coba sesuaikan kata kunci atau filter status</p>
            </div>
        </div>
    </div>

    <!-- FOOTER PAGINATION -->
    <div class="p-4 px-8 border-t border-slate-100 bg-white flex-none">
        <div class="flex justify-between items-center">
            <!-- GANTI DI BAGIAN INI -->
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                Menampilkan <?php echo e($pegawaiDivisi->firstItem()); ?> - <?php echo e($pegawaiDivisi->lastItem()); ?> dari <?php echo e($pegawaiDivisi->total()); ?> Pegawai
            </span>

            <div class="pagination-custom no-info">
                <?php echo e($pegawaiDivisi->links()); ?>

            </div>
        </div>
    </div>



<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/pegawai/datapegawai.blade.php ENDPATH**/ ?>