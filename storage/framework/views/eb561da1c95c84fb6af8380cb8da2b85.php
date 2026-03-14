<?php
    // Logika untuk menentukan file layout mana yang dipakai
    // Jika role_name mengandung 'manajer' atau 'kepala', pakai layout manager
    $layoutFile = (str_contains(strtolower($layout), 'manajer') || str_contains(strtolower($layout), 'kepala'))
                ? 'layouts.app-manager'
                : 'layouts.app-pegawai';
?>



<?php $__env->startSection('content'); ?>
    
    <div class="bg-white rounded-tl-md shadow-lg max-w-full">
        <div class="flex-1 overflow-y-auto h-[calc(100vh-120px)] space-y-4 custom-scroll-container shadow-lg">
            <?php if(session('warning')): ?>
                <div class="mb-4 flex items-center p-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800" role="alert">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-s-exclamation-triangle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'shrink-0 inline w-4 h-4 mr-3']); ?>
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
                    <span class="sr-only">Warning</span>
                    <div>
                        <span class="font-medium">Perhatian!</span> <?php echo e(session('warning')); ?>

                    </div>
                </div>
            <?php endif; ?>

            
            <div style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.1)), url('<?php echo e(asset('images/vecteezylight.jpg')); ?>')" class="bg-cover bg-bottom p-2 rounded-t-lg relative">
                <img src="<?php echo e(asset('images/dataprofile.png')); ?>" alt="Overtime" class="absolute right-0 top-0 h-44">
                
                
                
                <div class="flex items-center mt-1 ml-2">
                    <!-- Kontainer Foto Profil Interaktif dan Form Upload -->
                    <div class="relative group">
                        
                        <form action="<?php echo e(route('profile.update-photo')); ?>" method="POST" enctype="multipart/form-data" id="photoForm">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>

                            <label for="photo_selfie_input" class="cursor-pointer relative block">
                                
                                <?php
                                    // Ambil data detail_pribadi secara manual di layout agar muncul di semua halaman
                                    $detailPribadi = \DB::table('detail_pribadi')
                                                ->where('nomor_urut_pegawai', Auth::user()?->nomor_urut_pegawai)
                                                ->first();
                                ?>
                                <?php if($detailPribadi && $detailPribadi->photo_selfie): ?>
                                    <img src="<?php echo e(asset('storage/' . $detailPribadi->photo_selfie)); ?>?v=<?php echo e(time()); ?>"
                                        class="h-32 w-32 rounded-full object-cover border border-gray-200 group-hover:border-green-500 transition-all duration-300" alt="Foto Selfie Pegawai">
                                <?php else: ?>
                                    
                                    <div class="h-32 w-32 rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-200 group-hover:border-yellow-500 transition-all duration-300">
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

                                
                                <div class="absolute inset-0 h-32 w-32 rounded-full bg-gray-600 bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-50 transition-opacity duration-300">
                                    
                                    <svg xmlns="www.w3.org" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>

                                    
                                    <span class="text-white text-xs font-semibold mt-2">
                                        Upload Photo
                                    </span>
                                </div>
                            </label>

                            
                            <input type="file" name="photo_selfie" id="photo_selfie_input" class="hidden" onchange="document.getElementById('photoForm').submit();" accept="image/*">
                        </form>
                    </div>

                    
                    <?php $__errorArgs = ['photo_selfie'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1 ml-5"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    
                    <div class="ml-5">
                        <h1 class="text-gray-800 text-1xl font-bold">
                            <?php echo e(Auth::user()->name ?? 'User'); ?> -
                            <span class="text-gray-600 font-semibold text-sm">
                                <?php echo e(Auth::user()->pegawai->nomor_urut_pegawai ?? 'Nomor Urut Pegawai tidak ditemukan'); ?>

                            </span>
                        </h1>
                        
                        <p class="text-gray-600 font-semibold text-sm"><?php echo e($pekerjaanData->jabatan ?? 'Jabatan Tidak Ditemukan'); ?></p>
                        
                        <p class="text-gray-600 font-semibold text-sm">
                            <?php echo e(($pekerjaanData->pangkat ?? '') . ' - ' . ($pekerjaanData->grade ?? '')); ?>

                        </p>
                    </div>
                </div>
            </div>

            
            <div x-data="{ activeTab: 'pribadi' }"  class="mb-4 pl-4 shadow-lg">
                <span class="text-md font-semibold text-blue-700"># Section 1: Detail Pribadi</span>

                <!-- Container internal untuk padding dan layout tab -->
                <!-- Saya tambahkan padding p-6 di sini agar konten tidak menempel di tepi container utama -->
                <!-- Navigasi Tab -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button
                        @click="activeTab = 'pribadi'"
                        :class="activeTab === 'pribadi' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition-all duration-200">
                        Data Pribadi
                    </button>
                    <button
                        @click="activeTab = 'dokumen'"
                        :class="activeTab === 'dokumen' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition-all duration-200">
                        Upload Dokumen
                    </button>
                </div>

                
                <?php if(session('success')): ?>
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 animate-pulse rounded-lg" role="alert">
                        <p class="font-bold">Sukses!</p>
                        <p><?php echo e(session('success')); ?></p>
                    </div>
                <?php endif; ?>

                
                <?php if(session('error')): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Gagal!</p>
                        <p><?php echo e(session('error')); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Form Tunggal untuk Semua Tab -->
                <!-- PENTING: enctype="multipart/form-data" tetap harus ada di sini -->
                <form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                    <!-- KONTEN TAB 1: DATA PRIBADI -->
                    <div x-show="activeTab === 'pribadi'" class="space-y-6">
                        <!-- Data Pribadi Fields (Kode dari user sebelumnya) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <input type="hidden" name="nomor_urut_pegawai" id="nomor_urut_pegawai" value="<?php echo e(auth()->user()->nomor_urut_pegawai); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50" placeholder="Nomor Urut Pegawai" readonly/>
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap Pegawai</label>
                                <input type="text"
                                    name="nama"
                                    id="nama"
                                    
                                    value="<?php echo e(old('nama', auth()->user()->name)); ?>"
                                    
                                    readonly
                                    class="w-full bg-gray-100 cursor-not-allowed placeholder:text-slate-400 text-slate-600 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none shadow-sm <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Nama Pegawai" />

                                
                                <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label for="tempat_lahir" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                                    
                                    <input type="text" name="tempat_lahir" id="tempat_lahir"
                                    
                                    value="<?php echo e(old('tempat_lahir', Auth::user()->detailPribadi->tempat_lahir ?? '')); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 <?php $__errorArgs = ['tempat_lahir'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Nama Pegawai"
                                    
                                    <?php echo e(isset($formType) && $formType !== 'edit' && $formType !== 'new' ? 'readonly' : ''); ?> />
                                </div>

                                <div class="flex-1">
                                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                    <!-- Wrapper div untuk menampung input dan icon, diberi 'relative' -->
                                    <div class="relative mt-1">
                                            <input type="text" name="tanggal_lahir" id="tanggal_lahir"
                                                value="<?php echo e(old('tanggal_lahir', optional(Auth::user()->detailPribadi)->tanggal_lahir?->format('Y-m-d') ?? '')); ?>"
                                                placeholder="Pilih Tanggal Lahir"
                                                class="w-full bg-white placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 pr-10 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow cursor-pointer flatpickr-input" />
                                        
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" xmlns="www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 18h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label for="agama" class="block text-sm font-medium text-gray-700">Agama</label>
                                    <select name="agama" id="agama" class="w-full bg-white text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow">
                                        <option value="">-- Pilih Agama --</option>
                                        
                                        <?php
                                            $agamas = ['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Khonghucu', 'Lainnya'];
                                        ?>
                                        <?php $__currentLoopData = $agamas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($agama); ?>" <?php echo e((old('agama', Auth::user()->detailPribadi->agama ?? '') == $agama) ? 'selected' : ''); ?>>
                                                <?php echo e($agama); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                
                                <div class="flex-1">
                                    <span class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</span>
                                    <div class="flex items-center space-x-6">
                                        
                                        <label for="gender_male" class="flex items-center cursor-pointer">
                                            <input type="radio" id="gender_male" name="jenis_kelamin" value="Laki-laki" class="form-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                            
                                            <?php if(old('jenis_kelamin', Auth::user()->detailPribadi?->jenis_kelamin) === 'Laki-laki'): echo 'checked'; endif; ?>>
                                            <span class="ml-2 text-sm text-gray-700">Laki-laki</span>
                                        </label>
                                        
                                        <label for="gender_female" class="flex items-center cursor-pointer">
                                            <input type="radio" id="gender_female" name="jenis_kelamin" value="Perempuan" class="form-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                            <?php if(old('jenis_kelamin', Auth::user()->detailPribadi?->jenis_kelamin) === 'Perempuan'): echo 'checked'; endif; ?>>
                                            <span class="ml-2 text-sm text-gray-700">Perempuan</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat Lengkap Sesuai KTP</label>
                                <textarea name="alamat" id="alamat" rows="2" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50" placeholder="Alamat"><?php echo e(old('alamat', Auth::user()->detailPribadi->alamat ?? '')); ?></textarea>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="text" name="email" id="email"
                                    
                                    value="<?php echo e(old('email', Auth::user()->detailPribadi->email ?? '')); ?>"
                                    
                                    readonly
                                    class="w-full bg-gray-100 cursor-not-allowed placeholder:text-slate-400 text-slate-600 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none shadow-sm <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Email Pegawai" />
                            </div>

                            <div>
                                <label for="no_telpon" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input type="text" name="no_telpon" id="no_telpon"
                                    
                                    value="<?php echo e(old('no_telpon', Auth::user()->detailPribadi->no_telpon ?? '')); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 <?php $__errorArgs = ['no_telpon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Nomor Telepon Pegawai"
                                    
                                    <?php echo e(isset($formType) && $formType !== 'edit' && $formType !== 'new' ? 'readonly' : ''); ?> />
                            </div>

                            <div class="flex-1">
                                <label for="pendidikan_terakhir" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="w-full bg-white text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow" >
                                    <option value="">Pilih Pendidikan Terakhir</option>
                                    <?php
                                        // Gunakan Key sebagai nilai yang masuk ke database (sesuaikan dengan isi DB Anda)
                                        // Gunakan Value sebagai teks yang tampil di dropdown
                                        $list_pendidikan = [
                                            'SD' => 'Sekolah Dasar (SD)',
                                            'SMP' => 'Sekolah Menengah Pertama (SMP)',
                                            'SMA' => 'Sekolah Menengah Atas/ Kejuruan (SMA/SMK)',
                                            'D3' => 'Diploma III (D3)',
                                            'S1' => 'Sarjana (S1)',
                                            'S2' => 'Magister (S2)',
                                            'S3' => 'Doktor (S3)'
                                        ];
                                    ?>
                                    <?php $__currentLoopData = $list_pendidikan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"
                                            <?php echo e((old('pendidikan_terakhir', $detailPribadi->pendidikan_terakhir ?? '') == $key) ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label for="jurusan" class="block text-sm font-medium text-gray-700">Jurusan</label>
                                <input name="jurusan" value="<?php echo e(old('jurusan', Auth::user()->detailPribadi->jurusan ?? '')); ?>" id="jurusan" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50" placeholder="Jurusan" />
                            </div>

                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label for="nama_ibu" class="block text-sm font-medium text-gray-700">Nama Orang Tua (Ibu)</label>
                                    <input name="nama_ibu" id="nama_ibu" value="<?php echo e(old('nama_ibu', Auth::user()->detailPribadi->nama_ibu ?? '')); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50" placeholder="Nama Ibu Kandung" />
                                </div>
                                <div class="flex-1">
                                    <label for="nama_ayah" class="block text-sm font-medium text-gray-700">Nama Orang Tua (Ayah) </label>
                                    <input name="nama_ayah" id="nama_ayah" value="<?php echo e(old('nama_ayah', Auth::user()->detailPribadi->nama_ayah ?? '')); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50" placeholder="Nama Ayah Kandung" />
                                </div>
                            </div>

                            <div>
                                <label for="status_perkawinan" class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                                
                                <select name="status_perkawinan" id="status_perkawinan" class="w-full bg-white text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow">
                                    
                                    <option value="">-- Pilih Status --</option>
                                    <?php
                                        $status_perkawinan = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
                                    ?>
                                    <?php $__currentLoopData = $status_perkawinan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($status); ?>" <?php echo e((old('status_perkawinan', Auth::user()->detailPribadi->status_perkawinan ?? '') == $status) ? 'selected' : ''); ?>>
                                            <?php echo e($status); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- KONTEN TAB 2: UPLOAD IMAGE -->
                    <div x-show="activeTab === 'dokumen'" class="space-y-6 mb-4 p-4" x-cloak>
                        
                        <div class="documents-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; margin-bottom: 30px;">
                            <!-- 1. DOKUMEN KTP -->
                            <div class="upload-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">Dokumen KTP</label>
                                <p style="font-size: 11px; color: #6b7280; margin-bottom: 12px;">Format: JPG, JPEG, PNG. Maks: <span style="color: #ef4444; font-weight: 600;">10MB</span></p>

                                <div id="ktp-preview-container">
                                    <?php if(isset($detailPribadi?->dokumen_ktp)): ?>
                                        <div class="image-preview-wrapper" style="position: relative; width: fit-content; margin-bottom: 15px;">
                                            <img id="ktp-preview-img" src="<?php echo e(url('storage/uploads/ktp/' . $detailPribadi->dokumen_ktp)); ?>" alt="KTP" class="img-zoomable" onclick="openImageZoom(this.src)" style="max-width: 100%; height: 180px; border-radius: 12px; border: 2px solid #e5e7eb; cursor: zoom-in; object-fit: cover;">
                                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1); pointer-events: none;">📄 Tersimpan</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-preview" style="width: 100%; height: 180px; background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;"><span style="color: #9ca3af;">Belum diunggah</span></div>
                                    <?php endif; ?>
                                </div>
                                <div id="ktp-filename-wrapper" style="display: none; margin-bottom: 10px; font-size: 13px; color: #2563eb; font-weight: 500;"><i class="fas fa-file-image"></i> <span id="ktp-filename-text"></span></div>
                                <label for="dokumen_ktp" style="display: inline-block; padding: 10px 20px; background-color: #2563eb; color: white; border-radius: 8px; cursor: pointer; font-size: 14px;"><i class="fas fa-upload"></i> Pilih KTP</label>
                                <input type="file" id="dokumen_ktp" name="dokumen_ktp" onchange="handleFilePreviewCustom(this, 'ktp')" style="display: none;" accept="image/jpeg,image/png,image/webp">
                            </div>

                            <!-- 2. DOKUMEN KK -->
                            <div class="upload-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">Dokumen Kartu Keluarga</label>
                                <p style="font-size: 11px; color: #6b7280; margin-bottom: 12px;">Format: JPG, JPEG, PNG. Maks: <span style="color: #ef4444; font-weight: 600;">10MB</span></p>

                                <div id="kk-preview-container">
                                    <?php if(isset($detailPribadi?->dokumen_kk)): ?>
                                        <div class="image-preview-wrapper" style="position: relative; width: fit-content; margin-bottom: 15px;">
                                            <img id="kk-preview-img" src="<?php echo e(url('storage/uploads/kk/' . $detailPribadi->dokumen_kk)); ?>" alt="KK" class="img-zoomable" onclick="openImageZoom(this.src)" style="max-width: 100%; height: 180px; border-radius: 12px; border: 2px solid #e5e7eb; cursor: zoom-in; object-fit: cover;">
                                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1); pointer-events: none;">📄 Tersimpan</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-preview" style="width: 100%; height: 180px; background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;"><span style="color: #9ca3af;">Belum diunggah</span></div>
                                    <?php endif; ?>
                                </div>
                                <div id="kk-filename-wrapper" style="display: none; margin-bottom: 10px; font-size: 13px; color: #4f46e5; font-weight: 500;"><i class="fas fa-file-image"></i> <span id="kk-filename-text"></span></div>
                                <label for="dokumen_kk" style="display: inline-block; padding: 10px 20px; background-color: #4f46e5; color: white; border-radius: 8px; cursor: pointer; font-size: 14px;"><i class="fas fa-upload"></i> Pilih KK</label>
                                <input type="file" id="dokumen_kk" name="dokumen_kk" onchange="handleFilePreviewCustom(this, 'kk')" style="display: none;" accept="image/jpeg,image/png,image/webp">
                            </div>

                            <!-- 3. DOKUMEN NPWP -->
                            <div class="upload-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">Dokumen NPWP</label>
                                <p style="font-size: 11px; color: #6b7280; margin-bottom: 12px;">Format: JPG, JPEG, PNG. Maks: <span style="color: #ef4444; font-weight: 600;">10MB</span></p>

                                <div id="npwp-preview-container">
                                    <?php if(isset($detailPribadi?->dokumen_npwp)): ?>
                                        <div class="image-preview-wrapper" style="position: relative; width: fit-content; margin-bottom: 15px;">
                                            <img id="npwp-preview-img" src="<?php echo e(url('storage/uploads/npwp/' . $detailPribadi->dokumen_npwp)); ?>" alt="NPWP" class="img-zoomable" onclick="openImageZoom(this.src)" style="max-width: 100%; height: 180px; border-radius: 12px; border: 2px solid #e5e7eb; cursor: zoom-in; object-fit: cover;">
                                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1); pointer-events: none;">📄 Tersimpan</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-preview" style="width: 100%; height: 180px; background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;"><span style="color: #9ca3af;">Belum diunggah</span></div>
                                    <?php endif; ?>
                                </div>
                                <div id="npwp-filename-wrapper" style="display: none; margin-bottom: 10px; font-size: 13px; color: #0891b2; font-weight: 500;"><i class="fas fa-file-image"></i> <span id="npwp-filename-text"></span></div>
                                <label for="dokumen_npwp" style="display: inline-block; padding: 10px 20px; background-color: #0891b2; color: white; border-radius: 8px; cursor: pointer; font-size: 14px;"><i class="fas fa-upload"></i> Pilih NPWP</label>
                                <input type="file" id="dokumen_npwp" name="dokumen_npwp" onchange="handleFilePreviewCustom(this, 'npwp')" style="display: none;" accept="image/jpeg,image/png,image/webp">
                            </div>

                            <!-- DOKUMEN BUKU NIKAH (Diperbaiki) -->
                            <div class="upload-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">Dokumen Buku Nikah (Opsional)</label>
                                <p style="font-size: 11px; color: #6b7280; margin-bottom: 12px;">Format: JPG, JPEG, PNG. Maks: <span style="color: #ef4444; font-weight: 600;">10MB</span></p>

                                <div id="buku_nikah-preview-container">
                                    <?php if(isset($detailPribadi?->dokumen_buku_nikah)): ?>
                                        <div class="image-preview-wrapper" style="position: relative; width: fit-content; margin-bottom: 15px;">
                                            <!-- Pastikan ada tag <img> yang memuat gambar dari storage -->
                                            <img id="buku_nikah-preview-img"
                                                src="<?php echo e(url('storage/uploads/buku_nikah/' . $detailPribadi->dokumen_buku_nikah)); ?>"
                                                alt="Buku Nikah Tersimpan"
                                                class="img-zoomable"
                                                onclick="openImageZoom(this.src)"
                                                style="max-width: 100%; height: 180px; border-radius: 12px; border: 2px solid #e5e7eb; cursor: zoom-in; object-fit: cover;">

                                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1); pointer-events: none;">📄 Tersimpan</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-preview" style="width: 100%; height: 180px; background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                                            <span style="color: #9ca3af;">Belum diunggah</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div id="buku_nikah-filename-wrapper" style="display: none; margin-bottom: 10px; font-size: 13px; color: #059669; font-weight: 500;"><i class="fas fa-file-image"></i> <span id="buku_nikah-filename-text"></span></div>
                                <label for="dokumen_buku_nikah" style="display: inline-block; padding: 10px 20px; background-color: #059669; color: white; border-radius: 8px; cursor: pointer; font-size: 14px;"><i class="fas fa-upload"></i> Pilih Buku Nikah</label>
                                <input type="file" id="dokumen_buku_nikah" name="dokumen_buku_nikah" onchange="handleFilePreviewCustom(this, 'buku_nikah')" style="display: none;" accept="image/jpeg,image/png,image/webp">
                            </div>

                            <!-- DOKUMEN AKTA CERAI (Diperbaiki) -->
                            <div class="upload-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">Dokumen Akta Cerai (Opsional)</label>
                                <p style="font-size: 11px; color: #6b7280; margin-bottom: 12px;">Format: JPG, JPEG, PNG. Maks: <span style="color: #ef4444; font-weight: 600;">10MB</span></p>

                                <div id="akta_cerai-preview-container">
                                    <?php if(isset($detailPribadi?->dokumen_akta_cerai)): ?>
                                        <div class="image-preview-wrapper" style="position: relative; width: fit-content; margin-bottom: 15px;">
                                            <!-- Pastikan ada tag <img> yang memuat gambar dari storage -->
                                            <img id="akta_cerai-preview-img"
                                                src="<?php echo e(url('storage/uploads/akta_cerai/' . $detailPribadi->dokumen_akta_cerai)); ?>"
                                                alt="Akta Cerai Tersimpan"
                                                class="img-zoomable"
                                                onclick="openImageZoom(this.src)"
                                                style="max-width: 100%; height: 180px; border-radius: 12px; border: 2px solid #e5e7eb; cursor: zoom-in; object-fit: cover;">

                                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1); pointer-events: none;">📄 Tersimpan</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-preview" style="width: 100%; height: 180px; background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                                            <span style="color: #9ca3af;">Belum diunggah</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div id="akta_cerai-filename-wrapper" style="display: none; margin-bottom: 10px; font-size: 13px; color: #dc2626; font-weight: 500;"><i class="fas fa-file-image"></i> <span id="akta_cerai-filename-text"></span></div>
                                <label for="dokumen_akta_cerai" style="display: inline-block; padding: 10px 20px; background-color: #dc2626; color: white; border-radius: 8px; cursor: pointer; font-size: 14px;"><i class="fas fa-upload"></i> Pilih Akta Cerai</label>
                                <input type="file" id="dokumen_akta_cerai" name="dokumen_akta_cerai" onchange="handleFilePreviewCustom(this, 'akta_cerai')" style="display: none;" accept="image/jpeg,image/png,image/webp">
                            </div>
                        </div>
                    </div>
                    <!-- Tombol Simpan di bagian dalam form -->
                    <div class="mt-4 flex justify-end items-center">
                        <!-- Button Simpan -->
                        <button type="submit" class="inline-flex justify-center mb-4 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Simpan Data Pribadi
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 2: Data Anak -->
            <!-- Container Utama yang Diinginkan User (Single White Background) -->
            <div class="bg-white shadow-lg max-w-full">
                <div class="pl-4 shadow-sm">
                    <span class="text-md font-semibold mb-4 text-blue-700"># Section 2: Data Keluarga</span>
                    <form action="<?php echo e(route('profile.update-keluarga')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                            
                            <!-- Bagian Input Data Istri -->
                            <input type="hidden" name="nomor_urut_pegawai" id="nomor_urut_pegawai" value="<?php echo e(auth()->user()->nomor_urut_pegawai); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50" placeholder="Nomor Urut Pegawai" readonly/>
                            <div class="flex gap-2">
                                

                                <div class="flex-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Suami/Istri</label>
                                    
                                    <div id="wife-container" class="space-y-3">
                                        
                                        <?php if($istris->count() > 0): ?>
                                            <?php $__currentLoopData = $istris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $istri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex items-center gap-3 wife-input-group">
                                                
                                                <input type="text" name="nama_istri[]" value="<?php echo e($istri->nama); ?>" placeholder="Nama Istri" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow" />
                                                <button type="button" onclick="removeDynamicField(this, 'wife-container', 'wife-input-group', 'Istri')" class="p-2 text-red-600 hover:text-red-800 transition duration-150 ease-in-out">
                                                <!-- Ikon SVG tempat sampah (trash can) -->
                                                
                                                </button>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <!-- Tampilkan satu input kosong jika belum ada data -->
                                            <div class="flex items-center gap-3 wife-input-group">
                                                <input type="text" name="nama_istri[]" placeholder="Nama Istri" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow" />
                                                <button type="button" onclick="removeDynamicField(this, 'wife-container', 'wife-input-group', 'Istri')" class="p-2 text-red-600 hover:text-red-800 transition duration-150 ease-in-out hidden">
                                                
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    

                                    
                                    
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <label for="jumlah_anak" class="block text-sm font-medium text-gray-700">Jumlah Anak</label>
                                    <input type="text" value="<?php echo e($anaks->count()); ?>" name="jumlah_anak" id="jumlah_anak" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Jumlah Anak dari database" readonly />
                                </div>

                                <div class="flex-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Anak-anak</label>

                                    
                                    <div id="children-container" class="space-y-3">
                                        
                                        <?php if($anaks->count() > 0): ?>
                                            <?php $__currentLoopData = $anaks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex items-center gap-3 child-input-group">
                                                
                                                <input type="text" name="nama_anak[]" value="<?php echo e($anak->nama); ?>" placeholder="Nama Anak" class="w-full bg-transparent text-sm border border-slate-200 rounded-md px-3 py-2" />
                                                <button type="button" onclick="removeDynamicField(this, 'children-container', 'child-input-group', 'Anak')" class="p-2 text-red-600 hover:text-red-800 transition duration-150 ease-in-out">
                                                <!-- Ikon SVG tempat sampah (trash can) -->
                                                <svg xmlns="www.w3.org" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                </button>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <!-- Tampilkan satu input kosong jika belum ada data -->
                                            <div class="flex items-center gap-3 child-input-group">
                                                <input type="text" name="nama_anak[]" placeholder="Nama Anak Pertama" class="w-full bg-transparent text-sm border border-slate-200 rounded-md px-3 py-2" />
                                                
                                                <button type="button" onclick="removeDynamicField(this, 'children-container', 'child-input-group', 'Anak')" class="p-2 text-red-600 hover:text-red-800 transition duration-150 ease-in-out hidden" title="Hapus anak ini">
                                                    <!-- Ikon SVG tempat sampah (trash can) -->
                                                    <svg xmlns="www.w3.org" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    

                                    
                                    <button type="button" onclick="addDynamicField('children-container', 'child-input-group', 'Anak')" class="mt-3 flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                        <!-- Ikon Plus (+) -->
                                        <svg xmlns="www.w3.org" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        Tambah Anak
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Tombol Simpan di bagian dalam form -->
                        <div class="mt-4 flex justify-end items-center">
                            <button type="submit" class="inline-flex justify-center mb-4 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Simpan Data Keluarga
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Section 3: Data Pekerjaan -->
            <!-- Container Utama yang Diinginkan User (Single White Background) -->
            <div class="bg-white shadow-lg max-w-full">
                <div class="pl-4 shadow-sm">
                    <span class="text-md font-semibold mb-4 text-blue-700"># Section 3: Status Kepegawaian</span>
                    <form action="<?php echo e(route('profile.update-pekerjaan')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                            
                            <!-- Bagian Input Data Istri -->
                            <input type="hidden" name="nomor_urut_pegawai" id="nomor_urut_pegawai" value="<?php echo e(auth()->user()->nomor_urut_pegawai); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50" placeholder="Nomor Urut Pegawai" readonly/>
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <label for="status_pegawai" class="block text-sm font-medium text-gray-700">Status PEgawai<span class="text-red-600 ml-1">*</span></label>
                                    <input name="status_pegawai" id="status_pegawai" value="<?php echo e($pekerjaanData->status_pegawai ?? ''); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Status Pegawai" readonly />
                                </div>
                                <div class="flex-1">
                                    <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan<span class="text-red-600 ml-1">*</span></label>
                                    <input name="jabatan" id="jabatan" value="<?php echo e($pekerjaanData->jabatan ?? ''); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Jabatan" readonly />
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <div class="flex-2">
                                    <label for="pangkat" class="block text-sm font-medium text-gray-700">Pangkat<span class="text-red-600 ml-1">*</span></label>
                                    <input name="pangkat" id="pangkat" value="<?php echo e($pekerjaanData->pangkat ?? ''); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Pangkat" readonly />
                                </div>
                                <div>
                                    <label for="grade" class="block text-sm font-medium text-gray-700">Grade<span class="text-red-600 ml-1">*</span></label>
                                    <input name="grade" id="grade" value="<?php echo e($pekerjaanData->grade ?? ''); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Grade" readonly />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="tmt_pegawai" class="block text-sm font-medium text-gray-700">TMT Pegawai<span class="text-red-600 ml-1">*</span></label>
                                    <!-- Wrapper relatif untuk menempatkan ikon -->
                                    <div class="relative mt-1">
                                        <input name="tmt_pegawai" id="tmt_pegawai" value="<?php echo e($pekerjaanData->tmt_pegawai ?? ''); ?>" type="text"
                                            class="w-full bg-gray-50 placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 pr-10 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:cursor-not-allowed"
                                            placeholder="TMT Pegawai" readonly />
                                        <!-- Ikon absolut di kanan dalam input -->
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 18h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="masa_kerja" class="block text-sm font-medium text-gray-700">Masa Kerja<span class="text-red-600 ml-1">*</span></label>
                                    <input name="masa_kerja" id="masa_kerja" value="<?php echo e($pekerjaanData->masa_kerja ?? ''); ?>" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Masa Kerja" readonly />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div> <!-- Ditambahkan margin top untuk pemisahan -->
                                    <label for="periode_kenaikan_gapok" class="block text-sm font-medium text-gray-700">Periode Kenaikan Gaji Pokok<span class="text-red-600 ml-1">*</span></label>
                                    <!-- Wrapper relatif untuk menempatkan ikon -->
                                    <div class="relative mt-1">
                                        <input name="periode_kenaikan_gapok" id="periode_kenaikan_gapok" value="<?php echo e($pekerjaanData->periode_kenaikan_gapok ?? ''); ?>" type="text"
                                            class="w-full bg-gray-50 placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 pr-10 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:cursor-not-allowed"
                                            placeholder="Periode Kenaikan Gaji Pokok" readonly />
                                        <!-- Ikon absolut di kanan dalam input -->
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 18h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div> <!-- Ditambahkan margin top untuk pemisahan -->
                                    <label for="periode_kenaikan_grade" class="block text-sm font-medium text-gray-700">Periode Kenaikan Grade<span class="text-red-600 ml-1">*</span></label>
                                    <!-- Wrapper relatif untuk menempatkan ikon -->
                                    <div class="relative mt-1">
                                        <input name="periode_kenaikan_grade" id="periode_kenaikan_grade" value="<?php echo e($pekerjaanData->periode_kenaikan_grade ?? ''); ?>" type="text"
                                            class="w-full bg-gray-50 placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 pr-10 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:cursor-not-allowed"
                                            placeholder="Periode Kenaikan Grade" readonly />
                                        <!-- Ikon absolut di kanan dalam input -->
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 18h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="golongan_pajak" class="block text-sm font-medium text-gray-700">Golongan Pajak<span class="text-red-600 ml-1">*</span></label>
                                
                                <input name="golongan_pajak"
                                    id="golongan_pajak"
                                    value="<?php echo e(old('golongan_pajak', $pekerjaanData?->golongan_pajak)); ?>"
                                    class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50"
                                    placeholder="Golongan Pajak" readonly />
                                    
                                    <?php $__errorArgs = ['golongan_pajak'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label for="no_rekening" class="block text-sm font-medium text-gray-700">No. Rekening<span class="text-red-600 ml-1">*</span></label>
                                
                                <input name="no_rekening"
                                    id="no_rekening"
                                    value="<?php echo e(old('no_rekening', $pekerjaanData?->no_rekening)); ?>"
                                    class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50"
                                    placeholder="Nomor Rekening" readonly />
                                    
                                    <?php $__errorArgs = ['no_rekening'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                        </div>
                        <!-- Bagian Footer Formulir yang Dimodifikasi -->
                        <div class="mt-2 flex justify-between items-center">
                            <!-- Catatan Baru Ditambahkan di Sini -->
                            <p class="text-sm text-red-400 italic">
                                Yang bertanda<span class="text-red-600 ml-1">*</span> diisi oleh Divisi HRO
                            </p>
                            <!-- Button Simpan -->
                            
                            <div class="h-10"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Section 4: Reward & Punishment -->


<?php $__env->startPush('scripts'); ?>
    <script>
        // Fungsi umum untuk menangani perubahan file dan menampilkan pratinjau gambar
        function setupImageUploadPreview(inputId, defaultViewId, previewViewId, previewImageId, filenameId) {
            const inputElement = document.getElementById(inputId);
            if (!inputElement) return;

            const defaultView = document.getElementById(defaultViewId);
            const previewView = document.getElementById(previewViewId);
            const previewImage = document.getElementById(previewImageId);
            const filenameText = document.getElementById(filenameId);

            // --- PERBAIKAN 1: Tambahkan Listener Klik pada Gambar yang Sudah Ada ---
            if (previewImage) {
                previewImage.style.cursor = 'zoom-in';
                previewImage.onclick = function() {
                    if (this.src && this.src !== window.location.href) {
                        openImageZoom(this.src);
                    }
                };
            }

            inputElement.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (previewImage && filenameText && defaultView && previewView) {
                                previewImage.src = e.target.result;
                                filenameText.textContent = file.name;
                                defaultView.style.display = 'none';
                                previewView.style.display = 'block';

                                // --- PERBAIKAN 2: Pastikan gambar baru juga bisa di-zoom ---
                                previewImage.style.cursor = 'zoom-in';
                            }
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // Logika untuk file non-gambar (PDF dll)
                        if (defaultView && previewView) {
                            defaultView.style.display = 'block';
                            previewView.style.display = 'none';
                        }
                    }
                }
            });
        }

        /**
         * Fungsi Universal untuk Preview KTP & KK
         */
        function handleFilePreview(input, type) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (!file.type.startsWith('image/')) {
                    alert('Silakan pilih file gambar!');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Tentukan target container berdasarkan tipe (ktp/kk)
                    const container = document.getElementById(`${type}-preview-container`);
                    const themeColor = (type === 'ktp') ? '#2563eb' : '#4f46e5';

                    container.innerHTML = `
                        <div class="image-preview-wrapper" style="position: relative; width: fit-content; margin-bottom: 15px;">
                            <img id="${type}-preview-img"
                                src="${e.target.result}"
                                alt="Preview Baru"
                                class="img-zoomable"
                                onclick="openImageZoom(this.src)"
                                style="max-width: 100%; height: 180px; border-radius: 12px; border: 2px solid ${themeColor}; cursor: zoom-in; object-fit: cover; animation: fadeIn 0.5s;">
                            <div style="position: absolute; top: 10px; right: 10px; background: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                ✨ Baru
                            </div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        }

        /**
         * Fungsi Tambahan untuk Validasi Size & Display Judul File
         */
        function handleFilePreviewCustom(input, type) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = 10 * 1024 * 1024; // 10MB

                // 1. Validasi Ukuran 10MB
                if (file.size > maxSize) {
                    alert(`Gagal: File "${file.name}" terlalu besar! Maksimal ukuran adalah 10MB.`);
                    input.value = ''; // Reset input agar tidak terunggah saat submit
                    document.getElementById(`${type}-filename-wrapper`).style.display = 'none';
                    return;
                }

                // 2. Tampilkan Nama File (Judul Gambar)
                const filenameWrapper = document.getElementById(`${type}-filename-wrapper`);
                const filenameText = document.getElementById(`${type}-filename-text`);
                if (filenameWrapper && filenameText) {
                    filenameText.textContent = file.name;
                    filenameWrapper.style.display = 'block';
                }

                // 3. Panggil Fungsi Preview Asli Anda (handleFilePreview)
                handleFilePreview(input, type);
            }
        }

        /**
         * Mesin Zoom Global (Modal)
         */
        function openImageZoom(imageSrc) {
            let modal = document.getElementById('global-zoom-overlay');

            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'global-zoom-overlay';
                modal.style.cssText = `
                    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                    background: rgba(0,0,0,0.92); display: flex; align-items: center;
                    justify-content: center; z-index: 1000000; cursor: zoom-out;
                    backdrop-filter: blur(5px); transition: all 0.3s;
                `;

                const zoomImg = document.createElement('img');
                zoomImg.id = 'global-zoom-img';
                zoomImg.style.cssText = 'max-width: 90%; max-height: 90%; border-radius: 5px; box-shadow: 0 0 40px rgba(0,0,0,0.5);';

                modal.appendChild(zoomImg);
                document.body.appendChild(modal);

                modal.onclick = function() {
                    this.style.display = 'none';
                    document.body.style.overflow = 'auto';
                };
            }

            const fullImg = document.getElementById('global-zoom-img');
            fullImg.src = imageSrc;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Panggil fungsi untuk setiap dokumen Anda
        // setupImageUploadPreview('dokumen_ktp', 'ktp-default-view', 'ktp-preview-view', 'ktp-preview-image', 'ktp-preview-filename');
        // setupImageUploadPreview('dokumen_kk', 'kk-default-view', 'kk-preview-view', 'kk-preview-image', 'kk-preview-filename');
        // setupImageUploadPreview('dokumen_npwp', 'npwp-default-view', 'npwp-preview-view', 'npwp-preview-image', 'npwp-preview-filename');
        // setupImageUploadPreview('dokumen_buku_nikah', 'buku-nikah-default-view', 'buku-nikah-preview-view', 'buku-nikah-preview-image', 'buku-nikah-preview-filename');
        // setupImageUploadPreview('dokumen_akta_cerai', 'akta-cerai-default-view', 'akta-cerai-preview-view', 'akta-cerai-preview-image', 'akta-cerai-preview-filename');

    </script>
<?php $__env->stopPush(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- INISIALISASI UNTUK TANGGAL LAHIR (Kode Anda sebelumnya) ---
        const inputElement = document.getElementById('tanggal_lahir');
        const defaultDateValue = inputElement ? inputElement.value : '';

        if (inputElement) {
            flatpickr(inputElement, {
                dateFormat: "Y-m-d", // Format internal
                altFormat: "d-m-Y",  // Format tampilan
                altInput: true,
                allowInput: true,
                defaultDate: defaultDateValue
            });
        }

        // --- INISIALISASI UNTUK INPUT REWARD (Kode baru) ---
        // Menargetkan semua elemen dengan kelas 'flatpickr-input' yang digunakan untuk reward
        const rewardInputs = document.querySelectorAll('#reward-container .flatpickr-input');

        rewardInputs.forEach(input => {
            // Baca nilai default dari atribut 'value' yang sudah diisi oleh Blade/Old Input
            const defaultValue = input.value;

            flatpickr(input, {
                enableTime: true,
                dateFormat: "Y-m-d", // Format internal DB (ISO 8601)
                altInput: true,          // Aktifkan input alternatif
                altFormat: "d-m-Y",  // Format tampilan yang diinginkan user
                allowInput: true,
                defaultDate: defaultValue // Gunakan nilai dari DB/Old input
            });
        });

    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($layoutFile, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/profile.blade.php ENDPATH**/ ?>