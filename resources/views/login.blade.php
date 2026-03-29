<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepegawaian Bank Kota Bogor</title>
    <link href="fonts.googleapis.com" rel="stylesheet">
    @vite('resources/css/app.css')
    {{-- Tambahkan gaya sederhana ini di head untuk fungsionalitas drag non-Tailwind --}}

</head>

<body class="flex flex-col min-h-screen bg-moving">
    <div class="absolute top-4 left-4 md:top-8 md:left-8"></div>
    @if(session('status'))
        <div id="success-popup" class="fixed inset-0 bg-black/75 flex items-center justify-center z-50">
            <div class="bg-green-900 p-6 rounded-lg shadow-xl max-w-sm w-full m-4 text-white">
                <div class="flex items-start">
                    {{-- Ikon ceklis SVG (gunakan Heroicons atau ikon library lain) --}}
                    <svg class="h-6 w-6 text-green-400 mr-4" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-grow">
                        <h4 class="text-lg font-semibold">Registrasi Berhasil!</h4>
                        <p class="mt-1 text-sm text-green-100">{{ session('status') }}</p>
                        {{-- Tombol "Lihat selengkapnya" --}}
                        <div class="mt-4">
                            <button onclick="closeSuccessPopup()" class="px-4 py-2 bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-green-900">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="flex-grow flex items-center justify-center p-4">
        {{-- Popup Notifikasi Error Modern (Ditambahkan z-50) --}}
        <div id="error-popup" class="fixed inset-0 bg-black/75 flex items-center justify-center hidden z-50">
            <div class="bg-red-900 p-6 rounded-lg shadow-xl max-w-sm w-full m-4 text-white">
                <div class="flex items-start">
                    <svg class="h-6 w-6 text-red-400 mr-4" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-grow">
                        <h4 class="text-lg font-semibold">Terjadi Kesalahan!</h4>
                        <p id="error-message" class="mt-1 text-sm text-red-100">Pesan error akan muncul di sini.</p>
                        <div class="mt-4">
                            <button onclick="closeErrorPopup()" class="px-4 py-2 bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-red-900">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Akhir Popup Notifikasi -->
        @if($errors->has('verification') || $errors->has('is_verified'))
        <div id="laravel-error-container" class="hidden">
            @if($errors->has('verification'))
            {{ $errors->first('verification') }}
            @else
            {{ $errors->first('is_verified') }}
            @endif
        </div>
        @endif
        <div class="p-8 rounded-2xl shadow-lg bg-gray-50 shadow-gray-300 max-w-sm w-full">
            <!-- Elemen perantara yang dibaca oleh JS (PENTING!) -->
            @if($errors->has('verification') || $errors->has('is_verified'))
                <div id="laravel-error-container" class="hidden">
                    @if($errors->has('verification'))
                        {{ $errors->first('verification') }}
                    @else
                        {{ $errors->first('is_verified') }}
                    @endif
                </div>
            @endif
            <form method="POST" action="{{ url('/login') }}" id="loginForm">
                @csrf
                <!-- BAGIAN BARU UNTUK CAPTION -->
                <img src="{{ asset('images/logobkb.png') }}" alt="Logo Bank Kota Bogor" class="h-10 mb-6 w-auto mx-auto">
                <div class="mb-6 text-center">
                    <p class="text-md font-stylish font-semibold text-gray-800">Sistem Informasi Manajemen Kepegawaian</p>
                    <p class="text-sm font-stylish font-semibold text-gray-800">Bank Kota Bogor</p>
                </div>
                <!-- AKHIR BAGIAN BARU UNTUK CAPTION -->
                <div class="mb-5">
                    <label for="username" class="block text-gray-600 text-[10px] font-bold uppercase tracking-widest mb-2 ml-1">Email</label>
                    <input type="text" id="username" name="username" class="w-full px-4 py-3 bg-white/50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all shadow-sm placeholder:text-gray-400 text-sm" placeholder="Masukkan email Anda" required>
                </div>
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label for="password" class="text-gray-600 text-[10px] font-bold uppercase tracking-widest">Password</label>
                        <a href="{{ url('/forgot-password') }}" class="text-[11px] font-bold text-blue-700 hover:underline">Lupa Password?</a>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 bg-white/50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all shadow-sm placeholder:text-gray-400 text-sm" placeholder="••••••••" required>
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-blue-700 transition-colors">
                            <div id="icon-open" class="h-5 w-5 text-current">
                                <x-heroicon-o-eye class="h-full w-full" />
                            </div>
                            <div id="icon-closed" class="h-5 w-5 text-current hidden">
                                <x-heroicon-o-eye-slash class="h-full w-full" />
                            </div>
                        </button>
                    </div>
                </div>
                <div class="mb-6">
                    <div class="w-full bg-gray-100 rounded-full p-1 relative h-11 flex items-center shadow-inner overflow-hidden cursor-pointer" id="sliderContainer">
                        <!-- Progress Fill -->
                        <div class="absolute inset-y-1 left-1 bg-blue-600/10 rounded-full transition-all duration-75" id="sliderFill" style="width: 0%;"></div>

                        <!-- Handle Slider (Ukuran dikecilkan) -->
                        <div class="relative z-20 w-9 h-9 bg-white rounded-full shadow-md flex items-center justify-center text-blue-600 border border-gray-50 cursor-grab active:cursor-grabbing transition-shadow hover:shadow-lg" id="sliderHandle">
                            <svg xmlns="http://www.w3.org" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </div>

                        <!-- Teks Petunjuk -->
                        <span class="absolute inset-0 flex items-center justify-center text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] pointer-events-none select-none" id="sliderText">
                            Geser untuk Masuk
                        </span>
                    </div>
                    <input type="hidden" name="is_verified" id="isVerified" value="0">
                </div>
                <!-- AKHIR KODE SLIDER -->
                <button type="submit" id="loginButton" class="hidden w-full bg-blue-600 hover:bg-blue-700 text-white h-11 px-6 rounded-full transition-all duration-300 shadow-lg shadow-blue-200 flex items-center justify-between group">
                    <!-- Spacer kiri biar teks bener-bener di tengah (opsional) -->
                    <div class="w-6"></div>

                    <!-- Teks Login (Ramping & Bold) -->
                    <span class="text-[9px] font-extrabold uppercase tracking-[0.25em] ml-2">Masuk ke Sistem</span>

                    <!-- Ikon Panah (Ukuran kecil & pas di lingkaran) -->
                    <div class="w-7 h-7 bg-white/20 rounded-full flex items-center justify-center transition-transform group-hover:translate-x-1">
                        <svg xmlns="http://www.w3.org" class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>

                </form>
                <p class="mt-6 text-center text-sm text-gray-500">
                    Belum punya akun?
                    <a href="{{ url('/register') }}" class="text-blue-600 hover:underline font-bold">
                        Daftar Sekarang
                    </a>
                </p>
            </p>
        </div>
    </div>
    <footer class="p-8 text-center text-white/40 text-[11px] tracking-widest font-medium uppercase">
        &copy; <span id="currentYear"></span> Bank Kota Bogor. All Rights Reserved.
    </footer>
    {{-- Tambahkan script ini --}}
    <script>
        function closeSuccessPopup() {
            const popup = document.getElementById('success-popup');
            if (popup) {
                popup.style.display = 'none';
            }
        }

        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
    {{-- Memuat JavaScript Slider menggunakan Vite --}}
    @vite('resources/js/login-slider.js')
</body>
</html>
