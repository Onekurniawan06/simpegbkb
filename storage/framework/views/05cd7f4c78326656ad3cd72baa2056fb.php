<!-- resources/views/partials/modal-success.blade.php -->
<div id="successModal" class="<?php echo e(Session::has('success') ? 'flex' : 'hidden'); ?> fixed inset-0 bg-black/50 items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-gray-50 p-8 rounded-xl shadow-2xl w-full max-w-lg mx-4 relative">
        <button onclick="document.getElementById('successModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="text-center">
            <svg class="mx-auto h-16 w-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>

            <h2 class="mt-4 text-2xl font-bold text-gray-900">
                <?php echo e(Session::get('modal_title') ?? 'Berhasil!'); ?>

            </h2>

            <p class="mt-2 text-sm text-gray-500">
                <?php echo e(Session::get('success')); ?> <br>
                Silahkan tunggu notifikasi secara berkala.
            </p>
        </div>

        
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/partials/modal-success.blade.php ENDPATH**/ ?>