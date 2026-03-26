<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi SIMPEG BKB</title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
</head>

<body class="flex flex-col min-h-screen bg-moving">

    <div class="flex flex-col items-center justify-center grow p-2">

        <div class="p-6 rounded-lg shadow-lg bg-gray-50 shadow-gray-300 max-w-xl w-full">
            <img src="<?php echo e(asset('images/logobkb.png')); ?>" alt="Logo Bank Kota Bogor" class="h-10 mb-4 w-auto mx-auto">
            <!-- Bagian Navigasi Tab: Menggunakan flexbox untuk membagi ruang secara merata -->
            <div class="flex mb-6 border-b border-gray-200">
                <button onclick="switchTab('pegawai-baru')" id="btn-baru" class="w-1/2 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-all duration-300 focus:outline-none">
                    Pegawai Baru
                </button>
                <button onclick="switchTab('pegawai-lama')" id="btn-lama" class="w-1/2 py-2 text-sm font-semibold text-gray-500 border-b-2 border-transparent hover:text-blue-600 transition-all duration-300 focus:outline-none">
                    Pegawai Lama
                </button>
            </div>

            

            <!-- Area Form Pendaftaran -->
            <form method="POST" action="<?php echo e(url('/register')); ?>">
                <?php echo csrf_field(); ?>

                <!-- Input Nama Lengkap: Field dasar yang wajib diisi oleh semua kategori pegawai -->
                <!-- Field Tambahan Khusus Pegawai Lama: Bagian ini akan muncul hanya jika tab Pegawai Lama aktif -->
                <div id="extra-field-lama" class="mb-4 hidden">
                    <label for="nomor_urut_pegawai" class="block text-gray-700 text-sm font-semibold mb-2">Nomor Urut Pegawai</label>
                    <input type="text" id="nomor_urut_pegawai" name="nomor_urut_pegawai"
                        onkeyup="verifikasiPegawai(this.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm"
                        placeholder="Masukkan Nomor Urut Anda">

                        <!-- Spinner Loading (Popup Kecil di dalam Input) -->
                        <div id="loading-spinner" class="absolute right-3 top-1.5 hidden">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    <p class="text-xs text-gray-500 mt-1 italic">*Khusus untuk verifikasi data pegawai lama.</p>
                </div>

                <div class="flex gap-2">
                    <div class="mb-4 flex-1">
                        <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" autocomplete="off"
                            class="w-full px-3 py-2 border border-gray-300 rounded-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm" required readonly>
                        <!-- Pesan feedback kecil jika pegawai ditemukan -->
                        <p id="msg-pegawai" class="text-xs mt-1 hidden"></p>
                    </div>

                    <!-- Input Alamat Email: Digunakan sebagai kredensial login utama -->
                    <div class="mb-4 flex-1">
                        <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Alamat Email</label>
                        <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm" required>
                    </div>
                </div>

                <!-- Input Otomatis: Jabatan & Divisi (Hasil Verifikasi Nomor Urut) -->
                <div id="extra-info-pegawai" class="flex gap-2 hidden">
                    <div class="mb-4 flex-1">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Jabatan</label>
                        <input type="text" id="jabatan_display" name="nama_jabatan"
                            class="w-full px-3 py-2 border border-gray-300 rounded-sm bg-gray-100 text-sm cursor-not-allowed" readonly>
                    </div>
                    <div class="mb-4 flex-1">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Divisi</label>
                        <input type="text" id="divisi_display" name="nama_divisi"
                            class="w-full px-3 py-2 border border-gray-300 rounded-sm bg-gray-100 text-sm cursor-not-allowed" readonly>
                    </div>
                </div>

                <div id="field-lahir" class="flex gap-2">
                    <div class="mb-4 flex-1">
                        <label for="tempat_lahir" class="block text-gray-700 text-sm font-semibold mb-2">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir" autocomplete="off"
                            class="w-full px-3 py-2 border border-gray-300 rounded-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm" required>
                    </div>

                    <div class="mb-4 flex-1">
                        <label for="tanggal_lahir" class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Lahir</label>
                        <!-- Menggunakan type date bawaan browser -->
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                            class="w-full px-3 py-2 border border-gray-300 rounded-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm" required>
                    </div>
                </div>

                <!-- Container Password menggunakan Flexbox -->
                <div class="flex gap-2">
                    <!-- Input Password Utama -->
                    <div class="mb-4 flex-1">
                        <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                onkeyup="cekKesesuaianPassword()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-200 pr-10 text-sm" required>
                            <button type="button" onclick="togglePasswordVisibility('password', 'icon-open-1', 'icon-closed-1')" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800">
                                <div id="icon-open-1" class="h-5 w-5 text-current">
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
                                <div id="icon-closed-1" class="h-5 w-5 text-current hidden">
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
                        <!-- Error Laravel untuk field password utama -->
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-[10px] text-red-600 mt-1 italic"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Input Konfirmasi Password -->
                    <div class="mb-4 flex-1">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-semibold mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                onkeyup="cekKesesuaianPassword()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-200 pr-10 text-sm" required>
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'icon-open-2', 'icon-closed-2')" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800">
                                <div id="icon-open-2" class="h-5 w-5 text-current">
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
                                <div id="icon-closed-2" class="h-5 w-5 text-current hidden">
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
                        <!-- Tempat Notifikasi Error Real-time (JavaScript) -->
                        <p id="msg-password-error" class="text-[10px] text-red-600 mt-1 hidden font-medium">
                            ✗ Password tidak cocok
                        </p>
                    </div>
                </div>

                <button type="submit" id="btn-submit-register" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-sm hover:bg-blue-700 transition duration-200 shadow-md flex items-center justify-center">
                    <!-- Spinner (Hidden by default) -->
                    <svg id="spinner-register" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="btn-text">Daftar</span>
                </button>
            </form>

            <!-- Footer Form: Memberikan navigasi balik bagi pengguna yang tidak sengaja berada di halaman daftar -->
            <p class="mt-4 text-center text-sm text-gray-600">
                Sudah punya akun? <a href="<?php echo e(url('/')); ?>" class="text-blue-800 hover:underline font-semibold">Login di sini</a>
            </p>
        </div>
    </div>

    <footer class="mt-auto text-center text-white text-sm p-2">
        ©2025 Bank Kota Bogor
    </footer>

    <?php echo app('Illuminate\Foundation\Vite')('resources/js/login-slider.js'); ?>

    <script>
        function closeSuccessPopup() {
            const popup = document.getElementById('success-popup');
            if (popup) {
                popup.classList.add('hidden');
            }
        }

        function closeErrorPopup() {
            const popup = document.getElementById('error-popup');
            if (popup) {
                popup.classList.add('hidden');
            }
        }
    </script>

    <script>
        /**
         * Fungsi switchTab bertanggung jawab untuk mengelola logika visual saat pengguna
         * berpindah antar kategori pendaftaran.
         */

        // Fungsi untuk memindahkan Tab
        function switchTab(type) {
            const btnBaru = document.getElementById('btn-baru');
            const btnLama = document.getElementById('btn-lama');
            // const title = document.getElementById('tab-title');
            const inputNomorUrut = document.getElementById('nomor_urut_pegawai_input');
            const nameInput = document.getElementById('name');
            const msg = document.getElementById('msg-pegawai');
            const submitBtn = document.querySelector('button[type="submit"]');

            const fieldLahir = document.getElementById('field-lahir');
            const inputTempat = document.getElementById('tempat_lahir');
            const inputTanggal = document.getElementById('tanggal_lahir');
            const extraField = document.getElementById('extra-field-lama');
            const extraInfo = document.getElementById('extra-info-pegawai');

            // Ambil input jabatan & divisi agar reset value jalan
            const jabInput = document.getElementById('jabatan_display');
            const divInput = document.getElementById('divisi_display');

            const currentYear = new Date().getFullYear();
            const maxDate = `${currentYear}-12-31`;

            const formElement = document.querySelector('form');

            setTimeout(() => {
                if (type === 'pegawai-baru') {
                    if (formElement) formElement.reset();

                    btnBaru.classList.add('text-blue-600', 'border-blue-600');
                    btnBaru.classList.remove('text-gray-500', 'border-transparent');
                    btnLama.classList.add('text-gray-500', 'border-transparent');
                    btnLama.classList.remove('text-blue-600', 'border-blue-600');

                    // title.innerText = 'Daftar Akun Baru (Pegawai Baru)';

                    if (fieldLahir) fieldLahir.classList.remove('hidden');
                    if (extraField) extraField.classList.add('hidden');
                    if (extraInfo) extraInfo.classList.add('hidden');

                    if (inputTempat) inputTempat.setAttribute('required', 'true');
                    if (inputTanggal) {
                        inputTanggal.setAttribute('required', 'true');
                        inputTanggal.setAttribute('max', maxDate);
                    }
                    if (inputNomorUrut) inputNomorUrut.removeAttribute('required');

                    if (nameInput) {
                        nameInput.readOnly = false;
                        nameInput.classList.remove('bg-gray-100');
                        nameInput.placeholder = "Masukkan Nama Lengkap";
                    }

                    if (msg) msg.classList.add('hidden');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }

                } else {
                    btnLama.classList.add('text-blue-600', 'border-blue-600');
                    btnLama.classList.remove('text-gray-500', 'border-transparent');
                    btnBaru.classList.add('text-gray-500', 'border-transparent');
                    btnBaru.classList.remove('text-blue-600', 'border-blue-600');

                    // title.innerText = 'Daftar Akun Baru (Pegawai Lama)';

                    if (fieldLahir) fieldLahir.classList.add('hidden');
                    if (extraField) extraField.classList.remove('hidden');
                    if (extraInfo) extraInfo.classList.remove('hidden');

                    if (inputTempat) inputTempat.removeAttribute('required');
                    if (inputTanggal) inputTanggal.removeAttribute('required');
                    if (inputNomorUrut) inputNomorUrut.setAttribute('required', 'true');

                    if (nameInput) {
                        nameInput.value = "";
                        nameInput.readOnly = true;
                        nameInput.classList.add('bg-gray-100');
                        nameInput.placeholder = "Nama akan muncul otomatis...";
                    }

                    if (jabInput) jabInput.value = "";
                    if (divInput) divInput.value = "";

                    if (msg) msg.classList.add('hidden');

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }
            }, 100);
        }

        const formPendaftaran = document.querySelector('form[action*="register"]');
        if (formPendaftaran) {
            formPendaftaran.addEventListener('submit', function (e) {
                const btn = document.getElementById('btn-submit-register');
                const spinner = document.getElementById('spinner-register');
                const btnText = document.getElementById('btn-text');

                // Munculkan spinner dan ubah teks tombol
                if (spinner) spinner.classList.remove('hidden');
                if (btnText) btnText.innerText = "Sedang Menyimpan...";

                // Matikan tombol agar tidak bisa diklik dua kali (mencegah data ganda)
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                }
            });
        }

        // Fungsi ini akan dipanggil oleh event listener saat halaman dimuat
        function forceClearNameInput() {
            const nameInput = document.getElementById('name');
            if (nameInput) {
                // Trik paksa: Ubah tipe input sebentar lalu kembalikan
                nameInput.type = 'password';
                nameInput.type = 'text';
                nameInput.value = ''; // Pastikan nilai kosong

                // Hapus juga placeholder yang mungkin muncul dari auto-fill
                nameInput.placeholder = '';
            }
        }

        // Pastikan fungsi ini dipanggil saat halaman dimuat DAN saat beralih tab
        document.addEventListener('DOMContentLoaded', (event) => {
            switchTab('pegawai-baru');
            forceClearNameInput(); // Panggil fungsi paksa saat load
        });

        // Fungsi AJAX untuk verifikasi ke database
        let timeout = null;

        function verifikasiPegawai(nomor) {
            const nameInput = document.getElementById('name');
            const msg = document.getElementById('msg-pegawai');
            const spinner = document.getElementById('loading-spinner');
            const submitBtn = document.querySelector('button[type="submit"]');

            // Bersihkan timeout sebelumnya jika pengguna masih mengetik
            clearTimeout(timeout);

            // Jika input dihapus atau terlalu pendek
            if (nomor.length < 1) {
                msg.classList.add('hidden');
                spinner.classList.add('hidden');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
                if (nameInput) nameInput.value = "";
                return;
            }

            // --- TAHAP 1: MULAI PENCARIAN ---
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            spinner.classList.remove('hidden');
            msg.classList.remove('hidden');
            msg.innerText = "Sedang memverifikasi nomor urut...";
            msg.className = "text-[10px] mt-1 text-blue-600 italic";

            // Debouncing: Tunggu 800ms setelah user berhenti mengetik
            timeout = setTimeout(function () {
                fetch(`/cek-pegawai/${nomor}`)
                    .then(response => {
                        // Validasi apakah respon dari server OK (status 200)
                        if (!response.ok) {
                            throw new Error(`Server Error: ${response.status}`);
                        }

                        // Pastikan yang dikirim server adalah JSON, bukan HTML (<!DOCTYPE...)
                        const contentType = response.headers.get("content-type");
                        if (!contentType || !contentType.includes("application/json")) {
                            throw new TypeError("Server tidak mengirim JSON. Cek route/URL backend Anda!");
                        }

                        return response.json();
                    })
                    .then(data => {
                        spinner.classList.add('hidden');
                        const jabInput = document.getElementById('jabatan_display');
                        const divInput = document.getElementById('divisi_display');

                        if (data.success) {
                            // 1. Isi Data Nama, Jabatan, dan Divisi
                            document.getElementById('name').value = data.nama;
                            if(jabInput) jabInput.value = data.jabatan;
                            if(divInput) divInput.value = data.divisi;

                            // 2. Feedback Visual
                            msg.innerText = "✓ Pegawai ditemukan: " + data.nama;
                            msg.className = "text-[10px] mt-1 text-green-600 font-medium";
                            msg.classList.remove('hidden');

                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            }
                        } else {
                            // 3. Kosongkan jika Gagal atau Sudah Terdaftar
                            document.getElementById('name').value = "";
                            if(jabInput) jabInput.value = "";
                            if(divInput) divInput.value = "";

                            msg.innerText = data.message;
                            msg.className = "text-[10px] mt-1 text-red-500 font-medium";
                            msg.classList.remove('hidden');
                        }
                    })


                    .catch(error => {
                        // Menangani error jaringan atau error format JSON (HTML error page)
                        spinner.classList.add('hidden');
                        msg.innerText = "Gagal memproses data dari server.";
                        msg.className = "text-[10px] mt-1 text-red-700";

                        console.error('Pesan Error Detail:', error.message);
                        console.log('Saran: Cek tab Network, klik request yang merah, lalu lihat tab "Response" untuk melihat error aslinya.');
                    });
            }, 800);
        }

        /**
         * Fungsi utilitas untuk mengganti visibilitas password antara tipe 'password' dan 'text'.
         */
        function togglePasswordVisibility(inputId, openIconId, closedIconId) {
            const passwordInput = document.getElementById(inputId);
            const openIcon = document.getElementById(openIconId);
            const closedIcon = document.getElementById(closedIconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                openIcon.classList.add('hidden');
                closedIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                openIcon.classList.remove('hidden');
                closedIcon.classList.add('hidden');
            }
        }

        function cekKesesuaianPassword() {
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');
            const errorMsg = document.getElementById('msg-password-error');
            const submitBtn = document.querySelector('button[type="submit"]');

            // Hanya lakukan pengecekan jika kolom konfirmasi sudah mulai diisi
            if (confirm.value.length > 0) {
                if (password.value !== confirm.value) {
                    // Tampilkan pesan error
                    errorMsg.classList.remove('hidden');

                    // Ubah border menjadi merah sebagai peringatan visual
                    confirm.classList.add('border-red-500', 'ring-red-100');
                    confirm.classList.remove('border-gray-300');

                    // Nonaktifkan tombol submit demi keamanan data
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    // Sembunyikan pesan error jika sudah cocok
                    errorMsg.classList.add('hidden');

                    // Kembalikan gaya border ke normal
                    confirm.classList.remove('border-red-500', 'ring-red-100');
                    confirm.classList.add('border-gray-300');

                    // Aktifkan kembali tombol submit
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }
            } else {
                // Jika kolom kosong, sembunyikan semua status error
                errorMsg.classList.add('hidden');
                confirm.classList.remove('border-red-500', 'ring-red-100');
            }
        }
    </script>
<?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/register.blade.php ENDPATH**/ ?>