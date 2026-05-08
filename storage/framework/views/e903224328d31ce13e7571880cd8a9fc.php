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
}" class="h-full w-full bg-[#f8fafc] rounded-xl shadow-2xl flex flex-col overflow-hidden border border-gray-200">

    
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
    <div class="p-6 bg-white border-b border-gray-100 flex-none">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                    <?php echo e($pegawaiDivisi->total()); ?> <span class="text-slate-400 font-light text-xl">Pegawai</span>
                </h2>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                    <input x-model="search" type="text" placeholder="Cari Nama atau NUP..."
                        class="block w-full md:w-72 pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm transition-all focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-400 outline-none">
                </div>

                
                <select x-model="status" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-600 outline-none hover:bg-white focus:ring-4 focus:ring-blue-50 transition-all cursor-pointer">
                    <option value="">Semua Status</option>
                    <?php $__currentLoopData = $statusConfigs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($label); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <template x-if="search.length > 0 || status !== ''">
                    <button @click="search = ''; status = ''" class="px-3 py-2 text-xs font-bold text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                        Reset
                    </button>
                </template>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <?php $__currentLoopData = $statusConfigs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $style): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white p-4 rounded-2xl border <?php echo e($style['border']); ?> shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full <?php echo e($style['dot']); ?>"></span>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider leading-none"><?php echo e($label); ?></span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-slate-800"><?php echo e($stats[$label] ?? 0); ?></span>
                    <span class="text-[10px] text-slate-400 font-medium">Orang</span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- AREA DATA TABEL DENGAN CUSTOM SCROLL -->
    <div class="custom-scroll-area flex flex-col overflow-hidden">
        <div class="flex-1 overflow-y-auto custom-scroll-container">
            <table class="w-full text-left border-separate border-spacing-0">
                <thead class="sticky top-0 z-30">
                    <tr class="bg-white/95 backdrop-blur-md shadow-[0_1px_0_rgba(0,0,0,0.05)]">
                        <th class="py-4 px-6 text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-100">Nomor Urut Pegawai</th>
                        <th class="py-4 px-6 text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-100">Nama Pegawai</th>
                        <th class="py-4 px-6 text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-100">Jabatan & Grade</th>
                        <th class="py-4 px-6 text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-100">Status</th>
                        <th class="py-4 px-6 text-center text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-100">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    <?php $__currentLoopData = $pegawaiDivisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr x-show="filterRow('<?php echo e($p->nama); ?>', '<?php echo e($p->nomor_urut_pegawai); ?>', '<?php echo e($p->status_pegawai); ?>')"
                        x-transition.opacity.duration.250ms
                        class="hover:bg-blue-50/40 transition-colors group">

                        <td class="py-4 px-6">
                            <span class="font-mono text-xs text-slate-400 group-hover:text-blue-600 transition-colors">#<?php echo e($p->nomor_urut_pegawai); ?></span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-700 text-sm tracking-tight"><?php echo e($p->nama); ?></span>
                                <?php if(auth()->user()->level_id == 3): ?>
                                    <span class="text-[10px] text-slate-400 italic font-medium"><?php echo e($p->divisi); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="text-sm text-slate-600 font-medium"><?php echo e($p->jabatan); ?></div>
                            <div class="text-[10px] text-slate-400 font-medium"><?php echo e($p->pangkat); ?> • Grade <?php echo e($p->grade); ?></div>
                        </td>
                        <td class="py-4 px-6">
                            <?php $currStyle = $statusStyles[$p->status_pegawai] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200']; ?>
                            <span class="<?php echo e($currStyle['bg']); ?> <?php echo e($currStyle['text']); ?> text-[9px] px-2.5 py-1 rounded-md font-bold uppercase border <?php echo e($currStyle['border']); ?>">
                                <?php echo e($p->status_pegawai); ?>

                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <a href="<?php echo e(route('manager.pegawai.detail', $p->nomor_urut_pegawai)); ?>"
                               class="inline-flex items-center gap-2 bg-white text-slate-700 border border-slate-200 px-4 py-2 rounded-xl text-xs font-bold hover:bg-[#001A4E] hover:text-white hover:border-[#001A4E] transition-all shadow-sm active:scale-95">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <!-- EMPTY STATE -->
            <div x-cloak x-show="(search !== '' || status !== '') && $el.parentElement.querySelectorAll('tbody tr[x-show]:not([style*=\'display: none\'])').length === 0"
                 class="py-24 text-center">
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
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                Page <?php echo e($pegawaiDivisi->currentPage()); ?> of <?php echo e($pegawaiDivisi->lastPage()); ?>

            </span>
            <div class="pagination-custom no-info">
                <?php echo e($pegawaiDivisi->links()); ?>

            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/manager/pegawaidivisi.blade.php ENDPATH**/ ?>