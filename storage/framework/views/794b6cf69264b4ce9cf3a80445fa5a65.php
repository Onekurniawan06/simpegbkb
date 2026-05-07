<?php $__env->startSection('content'); ?>
    <div class="w-full">
    <div class="bg-white rounded-l-md shadow-md border border-gray-200 overflow-hidden">

        <!-- Header: Solid Midnight Blue (Tetap) -->
        <div class="px-8 py-6 text-white shrink-0 shadow-sm" 
     style="background: linear-gradient(to right, #f97316, #fdba74);">
        <h2 class="text-xl font-bold tracking-tight" style="color: white !important;"><?php echo e(__('Ubah Kata Sandi')); ?></h2>
        <p class="text-orange-50 text-sm mt-1 opacity-90">Sistem keamanan akun Anda adalah prioritas utama kami.</p>
    </div>

        <div class="p-5">
            <!-- Alert Status -->
            <?php if(session('status')): ?>
                <div class="mb-8 flex items-center p-4 bg-emerald-50 border border-emerald-100 rounded-lg text-emerald-700 shadow-sm" role="alert">
                    <svg class="h-5 w-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium text-sm"><?php echo e(session('status')); ?></span>
                </div>
            <?php endif; ?>

            
            <form method="POST" action="<?php echo e(route('password.change')); ?>">
                <?php echo csrf_field(); ?>

                <!-- Section Profil -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 mb-8">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 italic">Informasi Akun (Hanya Baca)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Field 1: Nomor Urut Pegawai (span 1 dari 5 kolom) -->
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut Pegawai</label>
                            <input type="text" value="<?php echo e($user->nomor_urut_pegawai); ?>" class="w-full px-4 py-2 bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-lg cursor-not-allowed focus:outline-none" readonly>
                        </div>
                        <!-- Field 2: Nama Lengkap (span 3 dari 5 kolom) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" value="<?php echo e($user->name); ?>" class="w-full px-4 py-2 bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-lg cursor-not-allowed focus:outline-none" readonly>
                        </div>
                        <!-- Field 3: Alamat Email (span 1 dari 5 kolom) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                            <input type="email" value="<?php echo e($user->email); ?>" class="w-full px-4 py-2 bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-lg cursor-not-allowed focus:outline-none" readonly>
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

                <!-- Section Input Password -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label for="current_password" class="block text-sm font-bold text-gray-700">Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm outline-none shadow-sm"
                            placeholder="••••••••">
                        <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-[11px] text-red-500 font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="space-y-2">
                        <label for="new_password" class="block text-sm font-bold text-gray-700">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm outline-none shadow-sm"
                            placeholder="Min. 8 Karakter">
                        <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-[11px] text-red-500 font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="space-y-2">
                        <label for="new_password_confirmation" class="block text-sm font-bold text-gray-700">Konfirmasi Password</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm outline-none shadow-sm"
                            placeholder="Ulangi password">
                    </div>
                </div>

                <!-- Footer Area -->
                <div class="pt-6 border-t border-gray-50 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-start gap-2 text-gray-400">
                        <svg class="w-4 h-4 mt-0.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-[11px] italic leading-relaxed">Gunakan kombinasi huruf besar, kecil, angka, dan simbol untuk keamanan maksimal.</p>
                    </div>
                    <button type="submit" class="w-full md:w-auto px-12 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-lg shadow-blue-100 transition-all active:scale-95">
                        <?php echo e(__('Simpan Perubahan')); ?>

                    </button>
                </div>
            </form>
        </div>
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




<?php echo $__env->make($layoutFile, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/change-password.blade.php ENDPATH**/ ?>