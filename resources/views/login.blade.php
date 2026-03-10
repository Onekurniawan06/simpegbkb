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
    <div class="absolute top-4 left-4 md:top-8 md:left-8">
        {{-- <img src="{{ asset('images/logobkb.png') }}" alt="Logo Bank Kota Bogor" class="h-11 w-auto"> --}}
    </div>
    {{-- Modal Notifikasi Sukses (Gaya seperti error di image, tapi warna hijau/teal) --}}
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
                    {{-- Ikon peringatan SVG (gunakan Heroicons atau ikon library lain) --}}
                    <svg class="h-6 w-6 text-red-400 mr-4" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-grow">
                        <h4 class="text-lg font-semibold">Terjadi Kesalahan!</h4>
                        <p id="error-message" class="mt-1 text-sm text-red-100">Pesan error akan muncul di sini.</p>
                        {{-- Tombol "Lihat selengkapnya" --}}
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

        <div class="p-8 rounded-lg shadow-lg bg-gray-50 shadow-gray-300 max-w-sm w-full">
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
                <img src="{{ asset('images/logobkb.png') }}" alt="Logo Bank Kota Bogor" class="h-10 mb-4 w-auto mx-auto">
                <div class="mb-4 text-center">
                    <p class="text-md font-stylish font-semibold text-gray-800">Sistem Informasi Manajemen Kepegawaian</p>
                    <p class="text-sm font-stylish font-semibold text-gray-800">Bank Kota Bogor</p>
                </div>
                <!-- AKHIR BAGIAN BARU UNTUK CAPTION -->
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input type="text" id="username" name="username" class="w-full px-3 py-1 shadow-md border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-200" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <!-- Tambahkan div pembungkus dengan 'relative' -->
                    <div class="relative">
                        <input type="password" id="password" name="password" class="w-full px-3 py-1 shadow-md border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-200" required>
                        <!-- Tombol toggle password visibility -->
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800">
                            <!-- Ikon Mata Terbuka -->
                            <div id="icon-open" class="h-5 w-5 text-current">
                                <x-heroicon-o-eye class="h-full w-full" />
                            </div>
                            <!-- Ikon Mata Tertutup (disembunyikan secara default) -->
                            <div id="icon-closed" class="h-5 w-5 text-current hidden">
                                <x-heroicon-o-eye-slash class="h-full w-full" />
                            </div>
                        </button>
                    </div>
                    <div class="text-left mt-2">
                        <a href="{{ url('/forgot-password') }}" class="text-xs text-blue-600 hover:text-blue-500 hover:underline">
                            Lupa Password?
                        </a>
                    </div>
                </div>
                <!-- KODE SLIDER MENGGUNAKAN TAILWIND MURNI -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Verifikasi Bukan Robot</label>
                    <div class="w-full bg-gray-200 rounded-lg p-1 relative cursor-pointer overflow-hidden h-10" id="sliderContainer">
                        <div class="absolute top-0 left-0 h-full bg-emerald-500 rounded-lg slider-fill-transition" id="sliderFill" style="width: 0%;"></div>
                        <div class="relative z-10 w-10 h-8 bg-emerald-500 rounded-md flex items-center justify-center text-white cursor-grab slider-handle-transition mt-0.5 ml-0.5" id="sliderHandle">
                            <svg xmlns="www.w3.org" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="flex items-center justify-center h-full w-full absolute top-0 left-0 text-sm text-gray-600 z-0" id="sliderText">
                            Geser untuk Login
                        </span>
                    </div>
                    <input type="hidden" name="is_verified" id="isVerified" value="0">
                </div>
                <!-- AKHIR KODE SLIDER -->
                <button type="submit" id="loginButton" class="hidden w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                    Login
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-gray-600">
                Belum punya akun?
                <a href="{{ url('/register') }}" class="text-blue-800 hover:underline font-semibold">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </div>
    <footer class="w-full p-2 shadow-md text-center">
        <a class="text-sm text-white">©2025 Bank Kota Bogor</a>
    </footer>
    {{-- Tambahkan script ini --}}
    <script>
        function closeSuccessPopup() {
            const popup = document.getElementById('success-popup');
            if (popup) {
                popup.style.display = 'none';
            }
        }
    </script>
    {{-- Memuat JavaScript Slider menggunakan Vite --}}
    @vite('resources/js/login-slider.js')
</body>
</html>
