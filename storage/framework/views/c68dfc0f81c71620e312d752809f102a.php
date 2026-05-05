 

<?php $__env->startSection('content'); ?>
    
    <div class="bg-white rounded-lg shadow-lg max-w-full mx-auto">
        
        <div class="bg-linear-to-r from-blue-700 to-indigo-200 bg-cover bg-bottom p-6 rounded-t-lg relative">
            
            <div class="absolute rounded-t-lg"></div>
            
            <h2 class="text-lg font-bold text-white tracking-tight">
                <?php echo e(__('Ubah Kata Sandi')); ?>

            </h2>
            <p class="text-blue-100 text-sm mt-1">
                Pastikan akun Anda tetap aman dengan memperbarui kata sandi secara berkala.
            </p>
        </div>

        <div class="p-4">
            
            <?php if(session('status')): ?>
                <div class="mb-6 flex items-center p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg text-green-700 animate-pulse" role="alert">
                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium text-sm"><?php echo e(session('status')); ?></span>
                </div>
            <?php endif; ?>

            
            <form method="POST" action="<?php echo e(route('password.change')); ?>" class="space-y-6">
                <?php echo csrf_field(); ?>

                
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-8">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 italic">Informasi Akun (Hanya Baca)</h3>

                    <!-- Menggunakan grid tunggal dengan 3 kolom pada layar menengah ke atas -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Field 1: Nomor Urut Pegawai (span 1 dari 5 kolom) -->
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut Pegawai</label>
                            <input type="text" value="<?php echo e($user->nomor_urut_pegawai); ?>"
                                class="w-full px-4 py-2 bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-lg cursor-not-allowed focus:outline-none" readonly>
                        </div>

                        <!-- Field 2: Nama Lengkap (span 3 dari 5 kolom) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" value="<?php echo e($user->name); ?>"
                                class="w-full px-4 py-2 bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-lg cursor-not-allowed focus:outline-none" readonly>
                        </div>

                        <!-- Field 3: Alamat Email (span 1 dari 5 kolom) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                            <input type="email" value="<?php echo e($user->email); ?>"
                                class="w-full px-4 py-2 bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-lg cursor-not-allowed focus:outline-none" readonly>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class=" bg-white text-gray-500 font-medium tracking-tight">Input Password Baru</span>
                    </div>
                </div>

                
                <div class="space-y-5">
                    <!-- Pastikan Anda memiliki kontainer grid utama di atas kode ini seperti yang kita buat sebelumnya -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        
                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2 italic">Password Saat Ini</label>
                            <div class="relative">
                                <input type="password" id="current_password" name="current_password" required
                                    class="w-full px-4 py-2 rounded-lg text-sm border <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400 pr-10"
                                    placeholder="••••••••">

                                <!-- Tombol Toggle Password (Menggunakan struktur baru Anda) -->
                                <button type="button" id="togglePassword-current" onclick="togglePasswordVisibility('current_password')" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800">
                                    <!-- Ikon Mata Terbuka -->
                                    <div id="icon-open-current" class="h-5 w-5 text-current">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-eye'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-full w-full']); ?>
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
                                    <!-- Ikon Mata Tertutup (disembunyikan secara default) -->
                                    <div id="icon-closed-current" class="h-5 w-5 text-current hidden">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-eye-slash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-full w-full']); ?>
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
                                </button>

                            </div>
                            <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-xs text-red-500 font-medium tracking-wide"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div>
                            <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2 italic">Password Baru</label>
                            <div class="relative">
                                <input type="password" id="new_password" name="new_password" required
                                    class="w-full px-4 py-2 rounded-lg text-sm border <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php else: ?> border-gray-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400 pr-10"
                                    placeholder="Minimal 8 karakter">

                                <!-- Tombol Toggle Password (Menggunakan struktur baru Anda) -->
                                <button type="button" id="togglePassword-new" onclick="togglePasswordVisibility('new_password')" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800">
                                    <!-- Ikon Mata Terbuka -->
                                    <div id="icon-open-new" class="h-5 w-5 text-current">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-eye'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-full w-full']); ?>
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
                                    <!-- Ikon Mata Tertutup (disembunyikan secara default) -->
                                    <div id="icon-closed-new" class="h-5 w-5 text-current hidden">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-eye-slash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-full w-full']); ?>
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
                                </button>
                            </div>
                            <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-xs text-red-500 font-medium tracking-wide"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2 italic">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                                    class="w-full px-4 py-2 rounded-lg text-sm border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400 pr-10"
                                    placeholder="Ulangi password baru">

                                <!-- Tombol Toggle Password (Menggunakan struktur baru Anda) -->
                                <button type="button" id="togglePassword-confirmation" onclick="togglePasswordVisibility('new_password_confirmation')" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800">
                                    <!-- Ikon Mata Terbuka -->
                                    <div id="icon-open-confirmation" class="h-5 w-5 text-current">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-eye'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-full w-full']); ?>
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
                                    <!-- Ikon Mata Tertutup (disembunyikan secara default) -->
                                    <div id="icon-closed-confirmation" class="h-5 w-5 text-current hidden">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-eye-slash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-full w-full']); ?>
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
                                </button>

                            </div>
                        </div>
                    </div>

                    <div class="mt-1 flex justify-between items-center">
                        <!-- Catatan Baru Ditambahkan di Sini -->
                        <p class="text-sm text-red-400 italic">
                            <span class="text-red-600 ml-1">Gunakan kombinasi huruf besar, kecil, angka, dan simbol untuk **keamanan** terbaik.</span>
                        </p>
                    </div>

                    <!-- Bagian pembungkus tombol yang diubah -->
                    <div class="pt-2 flex flex-col items-center justify-center">
                        <!-- Tombol dengan lebar yang dibatasi (misal: w-1/2 atau w-auto dengan px-12) -->
                        <button type="submit"
                            class="w-full md:w-max min-w-[200px] px-10 py-3 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition-all duration-200 hover:shadow-lg active:scale-95">
                            <?php echo e(__('Simpan Perubahan')); ?>

                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<script>
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        let suffix;

        // Menentukan suffix berdasarkan ID input secara spesifik
        if (inputId === 'current_password') {
            suffix = 'current';
        } else if (inputId === 'new_password') {
            suffix = 'new';
        } else if (inputId === 'new_password_confirmation') {
            suffix = 'confirmation';
        } else {
            // Log error jika ID tidak sesuai
            console.error("ID input tidak dikenali: " + inputId);
            return;
        }

        const iconOpen = document.getElementById('icon-open-' + suffix);
        const iconClosed = document.getElementById('icon-closed-' + suffix);

        if (input.type === "password") {
            input.type = "text";
            if (iconOpen && iconClosed) {
                iconOpen.classList.add('hidden');
                iconClosed.classList.remove('hidden');
            }
        } else {
            input.type = "password";
            if (iconOpen && iconClosed) {
                iconOpen.classList.remove('hidden');
                iconClosed.classList.add('hidden');
            }
        }
    }
</script>




<?php echo $__env->make('layouts.app-pegawai', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/pegawai/change-password.blade.php ENDPATH**/ ?>