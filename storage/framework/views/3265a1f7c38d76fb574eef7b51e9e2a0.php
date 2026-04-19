<?php $__env->startSection('content'); ?>

<form action="<?php echo e(route('pensiun.updatePensiun')); ?>" method="POST" id="leaveFormPensiun" enctype="multipart/form-data">
<?php echo csrf_field(); ?>
<?php echo method_field('PUT'); ?>

    <div class="flex-1 overflow-y-auto h-[calc(100vh-120px)] space-y-2 custom-scroll-container">
        <div class="bg-white rounded-lg shadow-lg max-w-full mx-auto">
            
            <div style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.1)), url('<?php echo e(asset('images/vecteezylight.jpg')); ?>')" class="bg-cover bg-bottom p-2 rounded-t-lg relative">
                    <img src="<?php echo e(asset('images/retired.png')); ?>" alt="Overtime" class="absolute right-0 top-0 h-40">
                    
                    
                    
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
                <span class="text-md font-semibold text-blue-700 mb-4"># Section 1: Persyaratan Dokumen Pengajuan Pensiun</span>
                <p class="text-gray-600 mb-4 mt-2 text-sm">
                    Sebelum melakukan pengajuan pensiun, pastikan Anda telah menyiapkan dan mengunggah seluruh dokumen yang tercantum di bawah ini. Dokumen yang tidak lengkap dapat menyebabkan proses pengajuan tertunda. Daftar Dokumen yang perlu disiapkan adalah,
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Item 1 -->
                    <div class="flex items-center space-x-4">
                        <!-- Icon placeholder: Use your actual icon component here -->
                        <div class="bg-blue-600 p-3 rounded-lg flex items-center justify-center">
                            <!-- Replace with actual SVG icon if you have one, e.g., an icon of a document -->
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-800 text-sm font-semibold">Surat Permohonan Pensiun sebagai Pegawai Tetap</p>
                    </div>

                    <!-- Item 2 -->
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-600 p-3 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-800 text-sm font-semibold">Salinan Surat Keputusan Kenaikan Gaji Pokok Berkala Terakhir</p>
                    </div>

                    <!-- Item 3 -->
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-600 p-3 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-800 text-sm font-semibold">Salinan Surat Keputusan Pengangkatan Pertama Sebagai Pegawai tetap</p>
                    </div>

                    <!-- Item 4 -->
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-600 p-3 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-800 text-sm font-semibold">Salinan Surat Keputusan Kenaikan Pangkat Terakhir</p>
                    </div>

                    <!-- Item 5 (aligned left by default in grid) -->
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-600 p-3 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-800 text-sm font-semibold">Daftar Penilaian Kinerja Terakhir</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Data Pekerjaan -->
        <!-- Container Utama yang Diinginkan User (Single White Background) -->
        <div class="bg-white rounded-lg shadow-lg max-w-full mx-auto">
            <div class="mb-2 p-4 shadow-sm">
                <span class="text-md font-semibold text-blue-700 mb-4"># Section 2: Data Pegawai</span>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                    <div class="flex-1">
                        <label for="nama_pegawai" class="block text-sm font-medium text-gray-700">Nama Pegawai</label>
                        <input name="nama_pegawai" id="nama_pegawai" value="<?php echo e(auth()->user()->name); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Nama Pegawai" readonly />
                    </div>
                    <div class="flex-1">
                        <label for="nomor_urut_pegawai" class="block text-sm font-medium text-gray-700">Nomor Urut Pegawai</label>
                        <input name="nomor_urut_pegawai" id="nomor_urut_pegawai" value="<?php echo e(auth()->user()->nomor_urut_pegawai); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Nomor Urut Pegawai" readonly />
                    </div>
                    <div class="flex-1">
                        <label for="unit_kerja" class="block text-sm font-medium text-gray-700">Unit Kerja (Divisi)</label>
                        <input name="unit_kerja" id="unit_kerja" value="<?php echo e($pekerjaanData->divisi?->nama_divisi ?? 'Data tidak ditemukan'); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Unit Kerja" readonly />
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label for="status_pegawai" class="block text-sm font-medium text-gray-700">Status Pegawai</label>
                            <input name="status_pegawai" id="status_pegawai" value="<?php echo e($pekerjaanData->status_pegawai ?? ''); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Status Pegawai" readonly />
                        </div>
                        <div class="flex-1">
                            <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan Terakhir</label>
                            <input name="jabatan" id="jabatan" value="<?php echo e($pekerjaanData->jabatan ?? ''); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Jabatan" readonly />
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-2">
                            <label for="pangkat" class="block text-sm font-medium text-gray-700">Pangkat</label>
                            <input name="pangkat" id="pangkat" value="<?php echo e($pekerjaanData->pangkat ?? ''); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Pangkat" readonly />
                        </div>
                        <div>
                            <label for="grade" class="block text-sm font-medium text-gray-700">Grade</label>
                            <input name="grade" id="grade" value="<?php echo e($pekerjaanData->grade ?? ''); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Grade" readonly />
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-5">
                        <div class="flex-1">
                            <label for="tmt_pegawai" class="block text-sm font-medium text-gray-700">TMT Pegawai</label>
                            <div class="relative mt-1">
                                <input name="tmt_pegawai" id="tmt_pegawai" value="<?php echo e($tmt_pegawai_formatted ?? ''); ?>" type="text" class="w-full bg-gray-50 placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 pr-10 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:cursor-not-allowed" placeholder="TMT Pegawai" readonly />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 18h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex-2">
                            <label for="masa_kerja" class="block text-sm font-medium text-gray-700">Masa Kerja</label>
                            <input name="masa_kerja" id="masa_kerja" value="<?php echo e($pekerjaanData->masa_kerja ?? ''); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Masa Kerja" readonly />
                        </div>
                        <div>
                            <label for="tmt_pensiun" class="block text-sm font-medium text-gray-700">TMT Pensiun</label>
                            <!-- Wrapper relatif untuk menempatkan ikon -->
                            <div class="relative mt-1">
                                <input name="tmt_pensiun" id="tmt_pensiun" value="<?php echo e($tmt_pensiun_otomatis ?? ''); ?>" type="text"
                                    class="w-full bg-gray-50 placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 pr-10 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:cursor-not-allowed"
                                    placeholder="TMT Pensiun" readonly />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 18h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Detail Pengajuan Pensiun -->
        <div class="bg-white rounded-l-md shadow-lg max-w-full">
            <div class="mb-4 p-4 shadow-sm">
                <span class="text-sm font-semibold text-blue-700 mb-4"># Section 3: Detail Pengajuan Pensiun</span>
                <div class="mb-6 mt-3">
                    <label for="jenis_pengajuan" class="block text-sm font-medium text-gray-700 mb-2">Pilih Jenis Pengajuan Pensiun</label>
                    <div class="relative">
                        <select id="jenis_pengajuan" name="jenis_pengajuan"
                            class="block w-full pl-3 pr-10 py-2 text-sm bg-white border-2 border-gray-200 text-slate-700 rounded-lg cursor-pointer transition duration-300 ease-in-out focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 shadow-sm appearance-none hover:border-gray-300">
                            <option value="" disabled selected>-- Klik untuk memilih jenis pensiun --</option>
                            <option value="Pensiun Normal">Pensiun Normal</option>
                            <option value="Pensiun Dini">Pensiun Dini</option>
                        </select>

                        <!-- Ikon Chevron yang lebih elegan -->
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-blue-600" xmlns="http://w3.org" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>


                <!-- START: Modifikasi Upload Dokumen Section -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-700 font-semibold mb-4">
                        Upload Dokumen Persyaratan (Semua dokumen wajib diunggah dalam format PDF/JPG/PNG Max 5MB)
                    </label>

                    <div class="space-y-4">
                        <!-- Dokumen 1: Surat Permohonan Pensiun -->
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label for="dok_surat_permohonan" class="col-span-1 text-sm font-medium text-gray-700">
                                1. Surat Permohonan Pensiun
                            </label>
                            <div class="col-span-2">
                                <!-- Input File (Hidden, gunakan label untuk trigger klik) -->
                                <input type="file" id="dok_surat_permohonan" name="documents[Surat_Permohonan]" accept=".pdf,image/jpeg,image/png" required class="hidden"
                                    onchange="handleFileChange(event, 'status_surat_permohonan', 'view_surat_permohonan')">

                                <!-- Area Tampilan Status dan Tombol Aksi -->
                                <div class="flex items-center justify-between p-2 border border-gray-300 rounded-lg shadow-sm">
                                    <span id="status_surat_permohonan" class="text-sm text-gray-500 truncate">Belum ada file dipilih</span>
                                    <div class="flex space-x-2 ml-4">
                                        <label for="dok_surat_permohonan" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded shadow-md transition duration-150">
                                            Pilih File
                                        </label>
                                        <!-- Tombol Lihat (disabled by default) -->
                                        <button type="button" id="view_surat_permohonan" onclick="viewDocument('dok_surat_permohonan')"
                                                class="bg-gray-400 text-white text-xs font-semibold py-1 px-3 rounded shadow-md cursor-not-allowed" disabled>
                                            Lihat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen 2: Salinan SK Kenaikan Gaji Pokok Berkala Terakhir -->
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label for="dok_sk_kgb" class="col-span-1 text-sm font-medium text-gray-700">
                                2. Salinan SK Kenaikan Gaji Pokok Berkala Terakhir
                            </label>
                            <div class="col-span-2">
                                <input type="file" id="dok_sk_kgb" name="documents[SK_Gaji_Pokok_Berkala_Terakhir]" accept=".pdf,image/jpeg,image/png" required class="hidden"
                                    onchange="handleFileChange(event, 'status_kenaikangapok', 'view_kenaikangapok')"/>

                                <!-- Area Tampilan Status dan Tombol Aksi -->
                                <div class="flex items-center justify-between p-2 border border-gray-300 rounded-lg shadow-sm">
                                    <span id="status_kenaikangapok" class="text-sm text-gray-500 truncate">Belum ada file dipilih</span>
                                    <div class="flex space-x-2 ml-4">
                                        <label for="dok_sk_kgb" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded shadow-md transition duration-150">
                                            Pilih File
                                        </label>
                                        <!-- Tombol Lihat (disabled by default) -->
                                        <button type="button" id="view_kenaikangapok" onclick="viewDocument('dok_sk_kgb')"
                                                class="bg-gray-400 text-white text-xs font-semibold py-1 px-3 rounded shadow-md cursor-not-allowed" disabled>
                                            Lihat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen 3: Salinan SK Pengangkatan Pertama -->
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label for="dok_sk_angkat_pertama" class="col-span-1 text-sm font-medium text-gray-700">
                                3. Salinan SK Pengangkatan Pertama
                            </label>
                            <div class="col-span-2">
                                <input type="file" id="dok_sk_angkat_pertama" name="documents[SK_Pengangkatan_Pertama_Pegawai_Tetap]" accept=".pdf,image/jpeg,image/png" required class="hidden"
                                    onchange="handleFileChange(event, 'status_skpengangkatanpertama', 'view_skpengangkatanpertama')"/>

                                <!-- Area Tampilan Status dan Tombol Aksi -->
                                <div class="flex items-center justify-between p-2 border border-gray-300 rounded-lg shadow-sm">
                                    <span id="status_skpengangkatanpertama" class="text-sm text-gray-500 truncate">Belum ada file dipilih</span>
                                    <div class="flex space-x-2 ml-4">
                                        <label for="dok_sk_angkat_pertama" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded shadow-md transition duration-150">
                                            Pilih File
                                        </label>
                                        <!-- Tombol Lihat (disabled by default) -->
                                        <button type="button" id="view_skpengangkatanpertama" onclick="viewDocument('dok_sk_angkat_pertama')"
                                                class="bg-gray-400 text-white text-xs font-semibold py-1 px-3 rounded shadow-md cursor-not-allowed" disabled>
                                            Lihat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen 4: Salinan SK Kenaikan Pangkat Terakhir -->
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label for="dok_sk_pangkat_terakhir" class="col-span-1 text-sm font-medium text-gray-700">
                                4. Salinan SK Kenaikan Pangkat Terakhir
                            </label>
                            <div class="col-span-2">
                                <input type="file" id="dok_sk_pangkat_terakhir" name="documents[SK_Kenaikan_Pangkat_Terakhir]" accept=".pdf,image/jpeg,image/png" required class="hidden"
                                    onchange="handleFileChange(event, 'status_skpangkatterakhir', 'view_skpangkatterakhir')"/>

                                <!-- Area Tampilan Status dan Tombol Aksi -->
                                <div class="flex items-center justify-between p-2 border border-gray-300 rounded-lg shadow-sm">
                                    <span id="status_skpangkatterakhir" class="text-sm text-gray-500 truncate">Belum ada file dipilih</span>
                                    <div class="flex space-x-2 ml-4">
                                        <label for="dok_sk_pangkat_terakhir" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded shadow-md transition duration-150">
                                            Pilih File
                                        </label>
                                        <!-- Tombol Lihat (disabled by default) -->
                                        <button type="button" id="view_skpangkatterakhir" onclick="viewDocument('dok_sk_pangkat_terakhir')"
                                                class="bg-gray-400 text-white text-xs font-semibold py-1 px-3 rounded shadow-md cursor-not-allowed" disabled>
                                            Lihat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen 5: Daftar Penilaian Kinerja Terakhir -->
                        <div class="grid grid-cols-3 gap-4 items-center">
                            <label for="dok_penilaian_kinerja" class="col-span-1 text-sm font-medium text-gray-700">
                                5. Daftar Penilaian Kinerja Terakhir
                            </label>
                            <div class="col-span-2">
                                <input type="file" id="dok_penilaian_kinerja" name="documents[Daftar_Penilaian_Kerja_Terakhir]" accept=".pdf,image/jpeg,image/png" required class="hidden"
                                    onchange="handleFileChange(event, 'status_penilaian_kinerja', 'view_penilaian_kinerja')"/>

                                <!-- Area Tampilan Status dan Tombol Aksi -->
                                <div class="flex items-center justify-between p-2 border border-gray-300 rounded-lg shadow-sm">
                                    <span id="status_penilaian_kinerja" class="text-sm text-gray-500 truncate">Belum ada file dipilih</span>
                                    <div class="flex space-x-2 ml-4">
                                        <label for="dok_penilaian_kinerja" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded shadow-md transition duration-150">
                                            Pilih File
                                        </label>
                                        <!-- Tombol Lihat (disabled by default) -->
                                        <button type="button" id="view_penilaian_kinerja" onclick="viewDocument('dok_penilaian_kinerja')"
                                                class="bg-gray-400 text-white text-xs font-semibold py-1 px-3 rounded shadow-md cursor-not-allowed" disabled>
                                            Lihat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Modifikasi Upload Dokumen Section -->

                <!-- Catatan/Note Section -->
                <p class="text-xs text-red-600 mt-4">
                    <span class="font-semibold">Catatan:</span> Pastikan dokumen persyaratan yang anda upload dapat terlihat dengan jelas.
                </p>
                <div id="document-viewer-modal"
                    style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                    class="flex items-center justify-center">
                    <div class="relative bg-white rounded-xl shadow-2xl w-11/12 max-w-4xl h-5/6 flex flex-col overflow-hidden">
                        <div class="p-4 bg-white border-b border-gray-200 flex justify-between items-center">
                            <strong id="pdfNameTitle" class="text-sm text-gray-800">Pratinjau Dokumen</strong>
                            <button type="button" id="viewer-close-button" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-4 rounded-lg transition duration-150 ease-in-out shadow-md">
                                Tutup X
                            </button>
                        </div>
                        <div class="flex-grow bg-gray-800 p-1">
                            <iframe id="document-viewer-iframe" src="" frameborder="0" class="w-full h-full"></iframe>
                        </div>
                    </div>
                </div>
                <hr class="border-b border-gray-100 mt-4">
                <div class="mt-4 flex justify-end space-x-4">
                    <button type="button" id="openModalButtonPensiun" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded rounded-lg text-sm font-semibold">
                        Buat Pengajuan Pensiun
                    </button>
                </div>
            </div>

            <!-- === POPUP MODAL REVIEW === -->
            <div id="leaveModalpensiun" class="fixed inset-0 bg-black-50 flex items-center justify-center hidden z-50 backdrop-blur-sm">
                <!-- Konten Modal (Ubah max-w-lg jadi max-w-4xl agar lebih lebar) -->
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl mx-auto">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center border-b pb-3">
                        <h2 class="text-sm font-semibold">Surat Pengajuan Pensiun</h2>
                    </div>

                    <!-- Modal Body dengan Container Scroll -->
                    <div class="mt-4">
                        <div class="custom-scroll-container p-4" style="max-height: 70vh; overflow-y: auto;">
                            <?php echo $__env->make('partials.pensiun_letter_content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end mt-6 pt-4 border-t">
                        <button type="button" id="cancelButton" class="px-4 py-2 mr-3 text-sm text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                        <button id="submitButton" class="px-4 py-2 text-white bg-blue-600 text-sm rounded-lg hover:bg-blue-700">Ya, Ajukan Pensiun</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Loading Overlay -->
        <div id="loadingModalpensiun" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm">
            <div class="bg-gray-50 p-8 rounded-xl shadow-2xl flex flex-col items-center">
                <!-- Heroicon Paper Airplane dengan Animasi Pulse -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-blue-600 animate-fly">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>

                <p class="mt-4 text-gray-800 text-md font-semibold">Sedang Mengirim Data Pengajuan...</p>
            </div>
        </div>

        <!-- Modal Sukses (Sesuai Gambar) -->
        

        <!-- Modal Notifikasi Validasi File -->
        <div id="fileValidationErrorModal" class="fixed inset-0 bg-black/50 items-center justify-center hidden z-[100] backdrop-blur-sm">
            <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-sm mx-4 transform transition-all">
                <div class="text-center">
                    <!-- Icon Warning -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 mb-4">
                        <svg class="h-10 w-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Peringatan Upload</h2>
                    <p id="fileValidationMessage" class="mt-3 text-sm text-gray-500 leading-relaxed">
                        Pesan validasi akan muncul di sini.
                    </p>
                </div>
                <div class="mt-6">
                    <button id="closeFileValidationError" type="button" class="w-full px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition-colors shadow-lg">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js', 'resources/js/pensiun.js']); ?>
<?php $__env->stopPush(); ?>

<script>
    // Fungsi untuk memperbarui status file yang dipilih dan mengaktifkan tombol Lihat
    function handleFileChange(event, statusElementId, viewButtonId) {
        const input = event.target;
        const statusElement = document.getElementById(statusElementId);
        const viewButton = document.getElementById(viewButtonId);

        if (input.files && input.files[0]) {
            statusElement.textContent = input.files[0].name;
            viewButton.disabled = false;
            viewButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            viewButton.classList.add('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');
        } else {
            statusElement.textContent = 'Belum ada file dipilih';
            viewButton.disabled = true;
            viewButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            viewButton.classList.remove('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');
        }
    }

    // Fungsi untuk menampilkan dokumen di modal viewer (menggunakan FileReader API)
    function viewDocument(inputId) {
        const input = document.getElementById(inputId);
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const modal = document.getElementById('document-viewer-modal');
                const iframe = document.getElementById('document-viewer-iframe');
                const title = document.getElementById('pdfNameTitle');

                title.textContent = file.name;
                iframe.src = e.target.result; // Data URL untuk pratinjau
                modal.style.display = 'flex';
            };
            reader.readAsDataURL(file); // Membaca file sebagai Data URL
        }
    }

    // Fungsi untuk menutup modal viewer (dari kode blade Anda sebelumnya)
    document.getElementById('viewer-close-button').onclick = function() {
        document.getElementById('document-viewer-modal').style.display = 'none';
        document.getElementById('document-viewer-iframe').src = '';
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->user()->layout_file, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/pensiun/pensiun.blade.php ENDPATH**/ ?>