@extends('layouts.app-pegawai') {{-- Assume you have a master layout that provides some outer padding --}}

@section('content')
    {{-- Div utama tanpa padding internal, konten di dalamnya yang mengatur jarak --}}
    <div class="bg-white rounded-lg shadow-lg max-w-full mx-auto">
        {{-- Header Profile Section (menggunakan p-8 untuk padding internal) --}}
        <div class="bg-linear-to-r from-blue-700 to-indigo-200 bg-cover bg-bottom p-6 rounded-t-lg relative">
            {{-- Opsional: Tambahkan overlay gelap agar teks lebih mudah dibaca --}}
            <div class="absolute rounded-t-lg"></div>
            {{-- Example Image Placeholder --}}
            <h2 class="text-lg font-bold text-white tracking-tight">
                {{ __('Ubah Kata Sandi') }}
            </h2>
            <p class="text-blue-100 text-sm mt-1">
                Pastikan akun Anda tetap aman dengan memperbarui kata sandi secara berkala.
            </p>
        </div>

        <div class="p-4">
            {{-- Menampilkan status berhasil jika ada --}}
            @if (session('status'))
                <div class="mb-6 flex items-center p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg text-green-700 animate-pulse" role="alert">
                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium text-sm">{{ session('status') }}</span>
                </div>
            @endif

            {{-- Formulir Utama --}}
            <form method="POST" action="{{ route('password.change') }}" class="space-y-6">
                @csrf

                {{-- Section: Informasi Profil (Read-only) --}}
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-8">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 italic">Informasi Akun (Hanya Baca)</h3>

                    <!-- Menggunakan grid tunggal dengan 3 kolom pada layar menengah ke atas -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Field 1: Nomor Urut Pegawai (span 1 dari 5 kolom) -->
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut Pegawai</label>
                            <input type="text" value="{{ $user->nomor_urut_pegawai }}"
                                class="w-full px-4 py-2 bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-lg cursor-not-allowed focus:outline-none" readonly>
                        </div>

                        <!-- Field 2: Nama Lengkap (span 3 dari 5 kolom) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" value="{{ $user->name }}"
                                class="w-full px-4 py-2 bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-lg cursor-not-allowed focus:outline-none" readonly>
                        </div>

                        <!-- Field 3: Alamat Email (span 1 dari 5 kolom) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                            <input type="email" value="{{ $user->email }}"
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

                {{-- Input Fields --}}
                <div class="space-y-5">
                    <!-- Pastikan Anda memiliki kontainer grid utama di atas kode ini seperti yang kita buat sebelumnya -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Input Password Lama --}}
                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2 italic">Password Saat Ini</label>
                            <div class="relative">
                                <input type="password" id="current_password" name="current_password" required
                                    class="w-full px-4 py-2 rounded-lg text-sm border @error('current_password') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400 pr-10"
                                    placeholder="••••••••">

                                <!-- Tombol Toggle Password (Menggunakan struktur baru Anda) -->
                                <button type="button" id="togglePassword-current" onclick="togglePasswordVisibility('current_password')" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800">
                                    <!-- Ikon Mata Terbuka -->
                                    <div id="icon-open-current" class="h-5 w-5 text-current">
                                        <x-heroicon-o-eye class="h-full w-full" />
                                    </div>
                                    <!-- Ikon Mata Tertutup (disembunyikan secara default) -->
                                    <div id="icon-closed-current" class="h-5 w-5 text-current hidden">
                                        <x-heroicon-o-eye-slash class="h-full w-full" />
                                    </div>
                                </button>

                            </div>
                            @error('current_password')
                                <p class="mt-1 text-xs text-red-500 font-medium tracking-wide">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Password Baru --}}
                        <div>
                            <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2 italic">Password Baru</label>
                            <div class="relative">
                                <input type="password" id="new_password" name="new_password" required
                                    class="w-full px-4 py-2 rounded-lg text-sm border @error('new_password') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400 pr-10"
                                    placeholder="Minimal 8 karakter">

                                <!-- Tombol Toggle Password (Menggunakan struktur baru Anda) -->
                                <button type="button" id="togglePassword-new" onclick="togglePasswordVisibility('new_password')" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800">
                                    <!-- Ikon Mata Terbuka -->
                                    <div id="icon-open-new" class="h-5 w-5 text-current">
                                        <x-heroicon-o-eye class="h-full w-full" />
                                    </div>
                                    <!-- Ikon Mata Tertutup (disembunyikan secara default) -->
                                    <div id="icon-closed-new" class="h-5 w-5 text-current hidden">
                                        <x-heroicon-o-eye-slash class="h-full w-full" />
                                    </div>
                                </button>
                            </div>
                            @error('new_password')
                                <p class="mt-1 text-xs text-red-500 font-medium tracking-wide">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password Baru --}}
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
                                        <x-heroicon-o-eye class="h-full w-full" />
                                    </div>
                                    <!-- Ikon Mata Tertutup (disembunyikan secara default) -->
                                    <div id="icon-closed-confirmation" class="h-5 w-5 text-current hidden">
                                        <x-heroicon-o-eye-slash class="h-full w-full" />
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
                            {{ __('Simpan Perubahan') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

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



