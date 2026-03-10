<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SIMPEG BKB</title>
    @vite('resources/css/app.css')
</head>

<body class="flex flex-col min-h-screen bg-moving">

    <div class="absolute top-4 left-4 md:top-8 md:left-8">
        <img src="{{ asset('images/logobkb.png') }}" alt="Logo Bank Kota Bogor" class="h-11 w-auto">
    </div>

        <div class="flex-grow flex items-center justify-center p-4">

            {{-- START: Success Message Modal (Laravel Session Status) --}}
            @if (session('status'))
                <div id="successModal" class="fixed inset-0 bg-black/75 backdrop-blur-xs flex items-center justify-center z-50 p-4">
                    <div class="bg-blue-900 border-l-4 border-blue-500 text-white p-4 rounded-lg shadow-2xl max-w-sm w-full mx-auto" role="alert">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-blue-400 mr-4" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="font-bold">Reset Password Berhasil Terkirim!</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 ml-10 text-sm">
                            <p>Silahkan buka email Anda dan klik "Reset Password" yang sudah kami kirimkan.</p>
                        </div>
                        <div class="mt-4 ml-10">
                            <button type="button" onclick="window.location.href='{{ url('/') }}'" class="flex items-center bg-blue-700 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-sm shadow-md transition duration-200">
                                <svg class="h-4 w-4 mr-2" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            {{-- END: Success Message Modal --}}

            {{-- START: Error Message Modal (Laravel Validation Errors & Throttle Error) --}}

            {{-- Variabel bantuan untuk memeriksa apakah ada error email ATAU error sesi (throttle) --}}
            @php
                $hasError = $errors->has('email') || session('error');
                $errorMessage = $errors->first('email') ?: session('error');

                // Cek jika pesan error adalah pesan throttle default Laravel (masih Inggris)
                // Jika ya, kita ganti pesannya ke Bahasa Indonesia
                if (Str::contains($errorMessage, 'Please wait before retrying')) {
                    $errorMessage = 'Anda terlalu sering meminta reset password. Mohon tunggu beberapa saat sebelum mencoba lagi.';
                } elseif (Str::contains($errorMessage, 'We can\'t find a user with that email address')) {
                    $errorMessage = 'Email Anda sebagai User tidak ditemukan.';
                }
            @endphp

            @if ($hasError)
                <div id="errorModal" class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                    <div class="bg-red-900 border-l-4 border-red-500 text-white p-4 rounded-lg shadow-2xl max-w-sm w-full mx-auto" role="alert">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-red-400 mr-4" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="font-bold">Peringatan!</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 ml-10 text-sm">
                            {{-- Menampilkan pesan error spesifik yang sudah di-handle di atas --}}
                            <p>{{ $errorMessage }}</p>
                        </div>
                        <div class="mt-4 ml-10">
                            <button type="button" onclick="document.getElementById('errorModal').style.display='none'" class="flex items-center bg-red-700 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-sm shadow-md transition duration-200">
                                <svg class="h-4 w-4 mr-2" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            {{-- END: Error Message Modal --}}

            {{-- Form Lupa Password --}}
            <div class="p-8 rounded-lg shadow-lg bg-gray-50 shadow-gray-300 max-w-sm w-full">
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-6 text-center">
                        <h1 class="text-xl font-bold text-gray-800 mb-2">Lupa Password?</h1>
                        <p class="text-sm text-gray-600">Masukkan email Anda yang terdaftar, dan kami akan mengirimkan tautan untuk mengatur ulang password Anda.</p>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3 py-2 shadow-md border border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-200 @error('email') border-red-500 @enderror">
                    </div>
                    <div class="mb-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-sm shadow-md transition duration-200">
                            Kirim Tautan Reset Password
                        </button>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ url('/') }}" class="text-sm text-blue-600 hover:text-blue-500 hover:underline">
                            Kembali ke Halaman Login
                        </a>
                    </div>
                </form>
            </div>
        </div>

    <footer class="w-full p-2 shadow-md text-center">
        <a class="text-sm text-white">©2025 Bank Kota Bogor</a>
    </footer>

</body>
</html>
