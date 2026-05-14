<?php $__env->startSection('content'); ?>
    <!-- PENTING: enctype="multipart/form-data" tetap harus ada di sini -->
    <form action="<?php echo e(route('cuti.updateCutiizin')); ?>" method="POST" enctype="multipart/form-data" id="leaveForm">
        <?php echo csrf_field(); ?>
        

        
        <?php if($errors->any()): ?>
            <div style="background-color: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <p style="font-weight: bold;">Terjadi Kesalahan Validasi:</p>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>- <?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grow flex items-center justify-center">
            <div id="error-popup" class="fixed inset-0 bg-black/75 flex items-center justify-center hidden z-50">
                <div class="bg-red-900 p-6 rounded-lg shadow-xl max-w-sm w-full m-4 text-white">
                    <div class="flex items-center mb-4">
                        <svg class="h-8 w-8 text-red-400 mr-4" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-6">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                        </svg>
                        <h4 class="text-lg font-semibold">Terjadi Kesalahan!</h4>
                    </div>
                    <div class="ml-12">
                        <p id="error-message" class="mt-1 text-sm text-red-100">Pesan error akan muncul di sini.</p>
                        <div class="mt-4">
                            <button id="btn-close-error" onclick="closePopup('error-popup')" class="px-4 py-2 bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-red-900">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popup Warning Kuning (Validasi Cuti) -->
            <div id="warning-popup" class="fixed inset-0 bg-black/75 flex items-center justify-center hidden z-50">
                <div class="bg-amber-900 p-6 rounded-lg shadow-xl max-w-sm w-full m-4 text-white">
                    <div class="flex items-center mb-4">
                        
                        <svg class="h-8 w-8 text-amber-400 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        <h4 class="text-lg font-semibold">Peringatan!</h4>
                    </div>
                    <div class="ml-12">
                        <p id="warning-message" class="mt-1 text-sm text-amber-100">Pesan peringatan akan muncul di sini.</p>
                        <div class="mt-4">
                            <!-- Hapus onclick dari sini -->
                            <button id="btn-close-warning" class="px-4 py-2 bg-amber-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-amber-900">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto h-[calc(100vh-120px)] space-y-2 custom-scroll-container">
            <div class="bg-white rounded-l-md shadow-lg max-w-full mx-auto">
                
                <div style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.1)), url('<?php echo e(asset('images/vecteezylight.jpg')); ?>')" class="bg-cover bg-bottom p-2 rounded-t-lg relative">
                    <img src="<?php echo e(asset('images/vacation.png')); ?>" alt="Overtime" class="absolute right-0 top-0 h-40">
                    
                    
                    
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
                            <p class="text-gray-600 font-semibold text-sm"> <?php echo e(($pekerjaanData->pangkat ?? '') . ' - ' . ($pekerjaanData->grade ?? '')); ?> </p>
                        </div>
                    </div>
                </div>

                
                <div class="p-4 shadow-sm">
                    <span class="text-md font-semibold text-blue-700 mb-4"># Section 1: Pengajuan Cuti dan Izin</span>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="hidden" name="nomor_urut_pegawai" id="nomor_urut_pegawai" value="<?php echo e(auth()->user()->nomor_urut_pegawai); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50" placeholder="Nomor Urut Pegawai" readonly/>
                        <input type="hidden" name="nama_pegawai" value="<?php echo e(auth()->user()->name ?? 'NamaPegawaiDefault'); ?>">

                        <div>
                            <label for="jenis_cuti" class="block text-sm font-medium text-gray-700 mt-2">Jenis Cuti dan Izin</label>
                            <select name="jenis_cuti" id="jenis_cuti" class="w-full bg-white text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow">
                                <option value="">-- Pilih Jenis Cuti dan Izin --</option>
                                <?php $__currentLoopData = $jenisCuti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cuti): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cuti->nama_cuti); ?>" <?php echo e((old('jenis_cuti') == $cuti->nama_cuti) ? 'selected' : ''); ?>>
                                        <?php echo e($cuti->nama_cuti); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div id="sub_jenis_cuti_container" style="display: none;">
                            <label for="sub_jenis_cuti" class="block text-sm font-medium text-gray-700 mt-2">Sub Jenis Cuti Penting</label>
                            <select name="sub_jenis_cuti" id="sub_jenis_cuti" class="w-full bg-white text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow">
                                <option value="">-- Pilih Sub Jenis Cuti --</option>
                                <?php $__currentLoopData = $subJenisCuti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCuti): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($subCuti->id); ?>" <?php echo e((old('sub_jenis_cuti') == $subCuti->id) ? 'selected' : ''); ?>>
                                        <?php echo e($subCuti->nama_sub_jenis); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <!-- Kolom Saldo Awal (Menampilkan jatah yang tersedia saat ini) -->
                            <div class="flex flex-col gap-1">
                                <label for="saldo_awal" class="block text-sm font-medium text-gray-700 mt-2">Saldo Cuti Saat Ini</label>
                                <input type="text" name="saldo_awal" id="saldo_awal" value="<?php echo e($sisaCutiTahunIni); ?>" readonly
                                    class="w-full bg-gray-100 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 shadow-sm cursor-not-allowed"
                                    placeholder="Otomatis">
                                
                                <input type="hidden" name="jatah_periode_hari" id="jatah_periode_hari" value="<?php echo e($sisaCutiTahunIni); ?>">
                            </div>

                            <div class="flex flex-col gap-1">
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mt-2">Tanggal Mulai Cuti</label>
                                <input id="tanggal_mulai" type="date" min="<?php echo e(now()->format('Y-m-d')); ?>" class="form-control <?php $__errorArgs = ['tanggal_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> w-full bg-white text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 focus:border-blue-500 outline-none shadow-sm" name="tanggal_mulai" required >
                                <?php $__errorArgs = ['tanggal_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="flex flex-col gap-1">
                                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mt-2">Tanggal Selesai Cuti</label>
                                <input id="tanggal_selesai" type="date" class="form-control <?php $__errorArgs = ['tanggal_selesai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> w-full bg-white text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 focus:border-blue-500 outline-none shadow-sm" name="tanggal_selesai" required>
                                <?php $__errorArgs = ['tanggal_selesai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <label for="jumlah_cuti" class="block text-sm font-medium text-gray-700 mt-2">Jumlah Cuti yang diambil (Hari)</label>
                                <input id="jumlah_cuti" type="number" readonly
                                    class="form-control w-full bg-gray-100 cursor-not-allowed text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 outline-none shadow-sm"
                                    name="jumlah_cuti" placeholder="Otomatis terisi">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label for="sisa_cuti" class="block text-sm font-medium text-gray-700 mt-2">Sisa Cuti Nanti (Hari)</label>
                                <input id="sisa_cuti" type="number" readonly
                                    class="form-control w-full bg-gray-100 cursor-not-allowed text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 outline-none shadow-sm"
                                    name="sisa_cuti" placeholder="Otomatis terisi">
                            </div>
                        </div>

                        <?php
                            $jenisCutiJson = $jenisCuti->toJson();
                            // Variabel sisaCutiTahunIni ini diambil dari Controller menggunakan fungsi hitungSaldoAwal
                        ?>
                    </div>

                    <div class="mt-4 flex justify-start items-center">
                        <button type="button" id="btn_refresh_form" class="btn btn-warning bg-blue-600 hover:bg-blue-700 text-sm font-semibold" style="padding: 10px 22px; color: white; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        Reset Pengajuan Cuti
                        </button>
                    </div>

                </div>
            </div>

            <div class="bg-white rounded-l-md shadow-lg max-w-full mx-auto">
                <div class="mb-4 p-4 shadow-sm" style="background-color: white; border-radius: 12px; border: 1px solid #e5e7eb;">
                    <span class="text-md font-semibold text-blue-700 mb-4"># Section 2: Keterangan Cuti/ Izin</span>
                    <div class="upload-group">
                        <label for="keterangan" class="block text-sm font-medium mb-2 text-gray-700 mt-2">Keterangan Cuti & izin:</label>
                        <textarea name="keterangan" id="keterangan" rows="3" placeholder="Masukkan alasan/keterangan cuti anda..." class="w-full bg-white text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 focus:border-blue-500 outline-none shadow-sm"></textarea>
                    </div>
                    <div class="flex justify-end items-center">
                        <button type="button" id="openModalButton" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded rounded-lg text-sm font-semibold">
                            Buat Pengajuan Cuti dan Izin
                        </button>
                    </div>
                </div>
            </div>

            <!-- === POPUP MODAL REVIEW === -->
            <div id="leaveModal" class="fixed inset-0 bg-black-50 flex items-center justify-center hidden z-50 backdrop-blur-sm">
                <!-- Konten Modal (Ubah max-w-lg jadi max-w-4xl agar lebih lebar) -->
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl mx-auto">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center border-b pb-3">
                        <h2 class="text-sm font-semibold">Surat Pengajuan Cuti/Izin</h2>
                    </div>

                    <!-- Modal Body dengan Container Scroll -->
                    <div class="mt-4">
                        <div class="custom-scroll-container p-4" style="max-height: 70vh; overflow-y: auto;">
                            <?php echo $__env->make('partials.letter_content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end mt-6 pt-4 border-t">
                        <button type="button" id="cancelButton" class="px-4 py-2 mr-3 text-sm text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                        <button id="submitButton" class="px-4 py-2 text-white bg-blue-600 text-sm rounded-lg hover:bg-blue-700">Ya, Ajukan Cuti</button>
                    </div>
                </div>
            </div>
            <!-- Modal Loading Overlay -->
            <div id="loadingModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm">
                <div class="bg-gray-50 p-8 rounded-xl shadow-2xl flex flex-col items-center">
                    <!-- Heroicon Paper Airplane dengan Animasi Pulse -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-blue-600 animate-fly">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>

                    <p class="mt-4 text-gray-800 text-md font-semibold">Sedang Mengirim Data Pengajuan...</p>
                </div>
            </div>
            <!-- Modal Sukses (Sesuai Gambar) -->
            
            <!-- Modal Error -->
            <div id="errorModal" class="<?php echo e(Session::has('error') ? 'show-on-load' : ''); ?> fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50 backdrop-blur-sm">
                <div class="bg-gray-50 p-8 rounded-xl shadow-2xl w-full max-w-sm mx-4">
                    <div class="text-center">
                        <!-- Icon SVG Anda -->
                        <svg class="mx-auto h-16 w-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h2 class="mt-4 text-xl font-bold text-gray-900">Gagal Mengajukan Cuti</h2>
                        <p id="errorMessage" class="mt-2 text-sm text-gray-500"><?php echo e(Session::get('error') ?? 'Pesan error akan muncul di sini.'); ?></p>
                    </div>
                    <div class="mt-6 flex justify-center">
                        <!-- Tombol yang sudah diupdate -->
                        <button id="closeErrorModalButton" type="button" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
    $jenisCutiJson = $jenisCuti->toJson();
    $jatahCutiTahunanMaksimal = $jenisCuti->where('nama_cuti', 'Cuti Tahunan')->first()->durasi_hari ?? 0;
?>
<div id="cuti-data-bridge"
    data-jatah-cuti-max="<?php echo e($jatahCutiTahunanMaksimal); ?>"
    data-all-jenis-cuti="<?php echo e($jenisCuti->toJson()); ?>"
    data-jenis-pengajuan="<?php echo e($jenisPengajuan); ?>"
    data-sisa-cuti-tahun-ini="<?php echo e($sisaCutiTahunIni); ?>"
    style="display: none;">
</div>

<?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/cuti-logic.js', 'resources/js/reviewcuti.js']); ?>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->user()->layout_file, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/cuti/cutiizin.blade.php ENDPATH**/ ?>