@extends(auth()->user()->layout_file)

@section('content')

    {{-- Div utama tanpa padding internal, konten di dalamnya yang mengatur jarak --}}
    <!-- Form Tunggal untuk Semua Tab -->
    <!-- PENTING: enctype="multipart/form-data" tetap harus ada di sini -->
    <form action="{{ route('lembur.updateLembur') }}" method="POST" id="leaveFormLembur">
        @csrf
        @method('PUT')

    <div class="flex-1 overflow-y-auto h-[calc(100vh-120px)] space-y-2 custom-scroll-container">
        <div class="bg-white rounded-lg shadow-lg max-w-full mx-auto">
            <div style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.1)), url('{{ asset('images/vecteezylight.jpg') }}')" class="bg-cover bg-bottom p-2 rounded-t-lg relative">
                    <img src="{{ asset('images/overtime.png') }}" alt="Overtime" class="absolute right-0 top-0 h-40">
                    {{-- Opsional: Tambahkan overlay gelap agar teks lebih mudah dibaca --}}
                    {{-- <div class="absolute rounded-t-lg"></div> --}}
                    {{-- Image and Name section --}}
                    <div class="flex items-center mt-2 ml-2 mb-2">
                        {{-- Container untuk Foto Profil atau Placeholder --}}
                        <div class="h-28 w-28 rounded-full overflow-hidden flex items-center justify-center">
                            @if(Auth::user()->detailPribadi && Auth::user()->detailPribadi->photo_selfie)
                                {{-- Menampilkan foto yang sudah ada dengan cache busting --}}
                                <img src="{{ asset('storage/' . Auth::user()->detailPribadi->photo_selfie) }}?v={{ time() }}"
                                    class="h-32 w-32 rounded-full object-cover border border-gray-200 group-hover:border-green-500 transition-all duration-300"
                                    alt="Foto Selfie Pegawai">
                            @else
                                {{-- Placeholder jika foto belum diunggah (menggunakan ikon dari query awal Anda) --}}
                                <div class="h-28 w-28 rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-200 group-hover:border-yellow-500 transition-all duration-300">
                                    <x-heroicon-x-person-profile class="h-20 w-20 text-gray-400 group-hover:text-yellow-500" />
                                </div>
                            @endif
                        </div>

                    <div class="ml-5">
                        <h1 class="text-gray-800 text-1xl font-bold">
                            {{ Auth::user()->name ?? 'User' }} -
                            <a class="text-gray-600 font-semibold text-sm">
                                {{ Auth::user()->pegawai->nomor_urut_pegawai ?? 'Nomor Urut Pegawai tidak ditemukan' }}
                            </a>
                        </h1>
                        <p class="text-gray-600 font-semibold text-sm">{{ $pekerjaanData->jabatan ?? 'Jabatan Tidak Ditemukan' }}</p>
                        <p class="text-gray-600 font-semibold text-sm">
                            {{ ($pekerjaanData->pangkat ?? '') . ' - ' . ($pekerjaanData->grade ?? '') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-2 p-4 shadow-sm">
                <span class="text-md font-semibold text-blue-700 mb-4"># Section : Pengajuan Lembur</span>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <!-- Nama Pegawai -->
                    <div>
                        <label for="nama_pegawai" class="block text-sm font-medium text-gray-700 mb-1">Nama Pegawai</label>
                        <input type="text" id="nama_pegawai" value="{{ auth()->user()->name ?? 'NamaPegawaiDefault' }}" readonly class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <!-- NUP Pegawai -->
                    <div>
                        <label for="nomor_urut_pegawai" class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut Pegawai</label>
                        <input type="text" id="nomor_urut_pegawai" name="nomor_urut_pegawai" value="{{ auth()->user()->nomor_urut_pegawai }}" readonly class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <!-- Jabatan saat ini -->
                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <input type="text" id="jabatan" value="{{ $pekerjaanData->jabatan ?? '' }}" readonly class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <!-- Unit Kerja -->
                    <div>
                        <label for="unit_kerja" class="block text-sm font-medium text-gray-700 mb-1">Pangkat</label>
                        <input type="text" id="unit_kerja" value="{{ $pekerjaanData->pangkat ?? '' }}" readonly class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm sm:text-sm">
                    </div>
                </div>

                {{-- Awal Data Pengajuan Lembur --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <!-- Tanggal Lembur -->
                    <div>
                        <label for="tanggal_lembur" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lembur</label>
                        <div class="relative">
                            <input type="date" id="tanggal_lembur" name="tanggal_lembur" min="{{ now()->format('Y-m-d') }}" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" placeholder="MM/DD/YYYY">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- Calendar Icon (Heroicon) -->
                                <svg class="h-5 w-5 text-gray-400" xmlns="www.w3.org" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Mulai Lembur -->
                    <div>
                        <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai Lembur</label>
                        <div class="relative">
                            <input type="time" id="jam_mulai" name="jam_mulai" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" value="00:00">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- Clock Icon (Heroicon) -->
                                <svg class="h-5 w-5 text-gray-400" xmlns="www.w3.org" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Selesai Lembur -->
                    <div>
                        <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai Lembur</label>
                        <div class="relative">
                            <input type="time" id="jam_selesai" name="jam_selesai" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" value="00:00">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- Clock Icon (Heroicon) -->
                            <svg class="h-5 w-5 text-gray-400" xmlns="www.w3.org" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Jam Lembur -->
                    <div>
                        <label for="total_jam" class="block text-sm font-medium text-gray-700 mb-1">Total Jam Lembur</label>
                        <!-- Pastikan type="text" -->
                        <input type="text" id="total_jam" name="total_jam_lembur" value="" readonly class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm focus:outline-none sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="uraian_tugas" class="block text-sm font-medium text-gray-700 mb-1 mt-4">Uraian Tugas/Kegiatan</label>
                    <textarea id="uraian_tugas" name="uraian_tugas" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" placeholder="Jelaskan uraian tugas/kegiatan selama lembur.."></textarea>
                </div>

                <!-- Buttons Section -->
                <div class="mt-4 flex justify-end space-x-4">
                    <button type="button" id="openModalButtonLembur" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded rounded-lg text-sm font-semibold">
                        Buat Pengajuan Lembur
                    </button>
                </div>
            </div>

            <!-- === POPUP MODAL REVIEW === -->
            <div id="leaveModallembur" class="fixed inset-0 bg-black-50 flex items-center justify-center hidden z-50 backdrop-blur-sm">
                <!-- Konten Modal (Ubah max-w-lg jadi max-w-4xl agar lebih lebar) -->
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl mx-auto">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center border-b pb-3">
                        <h2 class="text-sm font-semibold">Surat Pengajuan Lembur</h2>
                    </div>

                    <!-- Modal Body dengan Container Scroll -->
                    <div class="mt-4">
                        <div class="custom-scroll-container p-4" style="max-height: 70vh; overflow-y: auto;">
                            @include('partials.lembur_letter_content')
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end mt-6 pt-4 border-t">
                        <button type="button" id="cancelButton" class="px-4 py-2 mr-3 text-sm text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                        <button id="submitButton" class="px-4 py-2 text-white bg-blue-600 text-sm rounded-lg hover:bg-blue-700">Ya, Ajukan Lembur</button>
                    </div>
                </div>
            </div>

            <!-- Modal Loading Overlay -->
            <div id="loadingModalLembur" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm">
                <div class="bg-gray-50 p-8 rounded-xl shadow-2xl flex flex-col items-center">
                    <!-- Heroicon Paper Airplane dengan Animasi Pulse -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-blue-600 animate-fly">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>

                    <p class="mt-4 text-gray-800 text-md font-semibold">Sedang Mengirim Data Pengajuan...</p>
                </div>
            </div>

            <!-- Modal Sukses (Sesuai Gambar) -->
            {{-- <div id="successModalLembur" class="{{ Session::has('success') ? 'show-on-load' : '' }} fixed inset-0 bg-black/50 items-center justify-center hidden z-50 backdrop-blur-sm">
                <!-- Tambahkan class 'relative' pada div putih di bawah ini -->
                <div class="relative bg-gray-50 p-8 rounded-xl shadow-2xl w-full max-w-lg mx-4">
                    <!-- Tombol X Close di Pojok Kanan Atas -->
                    <button id="closeSuccessModalButton" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="text-center">
                        <!-- Icon Sukses -->
                        <svg class="mx-auto h-16 w-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h2 class="mt-4 text-2xl font-bold text-gray-900">Pengajuan Lembur berhasil dibuat!!!</h2>
                        <p class="mt-2 text-sm text-gray-500">Permintaan Lembur Anda telah tercatat dan menunggu persetujuan atasan. Silahkan cek notifikasi secara berkala terkait persetujuan lembur anda</p>
                    </div>
                    <!-- Wadah Tombol -->
                    <div class="mt-6 flex justify-center space-x-4">
                        <!-- Tombol Lihat Pengajuan -->
                        <a href="{{ route($parentRouteName, ['type' => 'lembur']) }}"
                            class="px-6 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                            Lihat Pengajuan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Modal Error -->
            <!-- Perhatikan perubahan pada class dan isi pesan p -->
            <div id="errorModalLembur" class="{{ (Session::has('error') || $errors->any()) ? 'show-on-load' : '' }} fixed inset-0 bg-black/50 items-center justify-center hidden z-50 backdrop-blur-sm">
                <div class="bg-gray-50 p-8 rounded-xl shadow-2xl w-full max-w-sm mx-4">
                    <div class="text-center">
                        <svg class="mx-auto h-16 w-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h2 class="mt-4 text-xl font-bold text-gray-900">Gagal Mengajukan Lembur</h2>
                        <p id="errorMessage" class="mt-2 text-sm text-gray-500">
                            @if($errors->any())
                                {{ $errors->first() }}
                            @else
                                {{ Session::get('error') ?? 'Pesan error akan muncul di sini.' }}
                            @endif
                        </p>
                    </div>
                    <div class="mt-6 flex justify-center">
                        <button id="closeErrorModalButton" type="button" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</form>

@push('scripts')
    @vite(['resources/js/overtime.js', 'resources/js/reviewlembur.js'])
@endpush

@endsection
