@extends('layouts.app-pegawai') {{-- Assume you have a master layout that provides some outer padding --}}

@section('content')

{{-- <form action="{{ route('pegawai.updatePensiun') }}" method="POST" id="leaveFormPensiun" enctype="multipart/form-data">
@csrf
@method('PUT') --}}

    <div class="bg-white rounded-lg shadow-lg max-w-full mx-auto">
        {{-- Header Profile Section (menggunakan p-8 untuk padding internal) --}}
        <div style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.1)), url('{{ asset('images/vecteezylight.jpg') }}')" class="bg-cover bg-bottom p-2 rounded-t-lg relative">
            <img src="{{ asset('images/retired.png') }}" alt="Overtime" class="absolute right-0 top-0 h-48">
            {{-- Opsional: Tambahkan overlay gelap agar teks lebih mudah dibaca --}}
            <div class="absolute rounded-t-lg"></div>
            {{-- Image and Name section --}}
            <div class="flex items-center mt-3 ml-4 mb-3">
                {{-- Container untuk Foto Profil atau Placeholder --}}
                <div class="h-32 w-32 rounded-full overflow-hidden flex items-center justify-center">
                    @if(Auth::user()->detailPribadi && Auth::user()->detailPribadi->photo_selfie)
                        {{-- Menampilkan foto profil jika sudah diunggah --}}
                        <img src="{{ asset('storage/' . Auth::user()->detailPribadi->photo_selfie) }}?v={{ time() }}"
                            class="h-full w-full object-cover"
                            alt="Foto Profil Pegawai">
                    @else
                        {{-- Placeholder ikon jika belum ada foto diunggah --}}
                        <x-heroicon-x-person-profile class="h-full w-full text-gray-400 group-hover:text-yellow-500" />
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

        <div class="mb-4 p-4 shadow-sm">
            <h3 class="text-lg font-semibold text-blue-700 mb-4"># Section 1: Persyaratan Dokumen Pengajuan Pensiun</h3>
            <p class="text-gray-600 mb-6 text-sm">
                Sebelum melakukan pengajuan pensiun, pastikan Anda telah menyiapkan dan mengunggah seluruh dokumen yang tercantum di bawah ini. Dokumen yang tidak lengkap dapat menyebabkan proses pengajuan tertunda. Daftar Dokumen yang perlu disiapkan adalah,
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Item 1 -->
                <div class="flex items-center space-x-4">
                    <!-- Icon placeholder: Use your actual icon component here -->
                    <div class="bg-blue-600 p-3 rounded-lg flex items-center justify-center">
                        <!-- Replace with actual SVG icon if you have one, e.g., an icon of a document -->
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-gray-800 text-sm font-semibold">Surat Permohonan Pensiun sebagai Pegawai Tetap</p>
                </div>

                <!-- Item 2 -->
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-600 p-3 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-gray-800 text-sm font-semibold">Salinan Surat Keputusan Kenaikan Gaji Pokok Berkala Terakhir</p>
                </div>

                <!-- Item 3 -->
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-600 p-3 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-gray-800 text-sm font-semibold">Salinan Surat Keputusan Pengangkatan Pertama Sebagai Pegawai tetap</p>
                </div>

                <!-- Item 4 -->
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-600 p-3 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-gray-800 text-sm font-semibold">Salinan Surat Keputusan Kenaikan Pangkat Terakhir</p>
                </div>

                <!-- Item 5 (aligned left by default in grid) -->
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-600 p-3 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-gray-800 text-sm font-semibold">Daftar Penilaian Kinerja Terakhir</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Data Pekerjaan -->
    <!-- Container Utama yang Diinginkan User (Single White Background) -->
    <div class="bg-white rounded-lg shadow-lg max-w-full mx-auto">
        <div class="mb-4 p-4 shadow-sm">
            <h3 class="text-lg font-semibold mb-4 text-blue-700"># Section 2: Data Pegawai</h3>
            {{-- Sisa formulir HTML Anda ada di bawah sini --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex-1">
                    <label for="nama_pegawai" class="block text-sm font-medium text-gray-700">Nama Pegawai</label>
                    <input name="nama_pegawai" id="nama_pegawai" value="{{ auth()->user()->name }}" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Nama Pegawai" readonly />
                </div>
                <div class="flex-1">
                    <label for="nomor_urut_pegawai" class="block text-sm font-medium text-gray-700">Nomor Urut Pegawai</label>
                    <input name="nomor_urut_pegawai" id="nomor_urut_pegawai" value="{{ auth()->user()->nomor_urut_pegawai }}" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Nomor Urut Pegawai" readonly />
                </div>
                <div class="flex-1">
                    <label for="unit_kerja" class="block text-sm font-medium text-gray-700">Unit Kerja (Divisi)</label>
                    {{-- Mengakses nama_divisi melalui relasi yang sudah di-load --}}
                    <input
                        name="unit_kerja"
                        id="unit_kerja"
                        value="{{ $pekerjaanData->divisi?->nama_divisi ?? 'Data tidak ditemukan' }}"
                        class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed"
                        placeholder="Unit Kerja"
                        readonly
                    />
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label for="status_pegawai" class="block text-sm font-medium text-gray-700">Status Pegawai</label>
                        <input name="status_pegawai" id="status_pegawai" value="{{ $pekerjaanData->status_pegawai ?? '' }}" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Status Pegawai" readonly />
                    </div>
                    <div class="flex-1">
                        <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan Terakhir</label>
                        <input name="jabatan" id="jabatan" value="{{ $pekerjaanData->jabatan ?? '' }}" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Jabatan" readonly />
                    </div>
                </div>
                <div class="flex gap-2">
                    <div class="flex-2">
                        <label for="pangkat" class="block text-sm font-medium text-gray-700">Pangkat</label>
                        <input name="pangkat" id="pangkat" value="{{ $pekerjaanData->pangkat ?? '' }}" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Pangkat" readonly />
                    </div>
                    <div>
                        <label for="grade" class="block text-sm font-medium text-gray-700">Grade</label>
                        <input name="grade" id="grade" value="{{ $pekerjaanData->grade ?? '' }}" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Grade" readonly />
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-5">
                    <div class="flex-1">
                        <label for="tmt_pegawai" class="block text-sm font-medium text-gray-700">TMT Pegawai</label>
                        <div class="relative mt-1">
                            {{-- GANTI BARIS INI --}}
                            <input name="tmt_pegawai" id="tmt_pegawai" value="{{ $tmt_pegawai_formatted ?? '' }}" type="text"
                            {{-- SAMPAI SINI --}}
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
                    <div class="flex-2">
                        <label for="masa_kerja" class="block text-sm font-medium text-gray-700">Masa Kerja</label>
                        <input name="masa_kerja" id="masa_kerja" value="{{ $pekerjaanData->masa_kerja ?? '' }}" class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 mt-1 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:bg-gray-50 read-only:cursor-not-allowed" placeholder="Masa Kerja" readonly />
                    </div>
                    <div>
                        <label for="tmt_pensiun" class="block text-sm font-medium text-gray-700">TMT Pensiun</label>
                        <!-- Wrapper relatif untuk menempatkan ikon -->
                        <div class="relative mt-1">
                            {{-- GANTI BARIS INI --}}
                            <input name="tmt_pensiun" id="tmt_pensiun" value="{{ $tmt_pensiun_otomatis ?? '' }}" type="text"
                                class="w-full bg-gray-50 placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 pr-10 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow read-only:cursor-not-allowed"
                                placeholder="TMT Pensiun" readonly />
                            {{-- SAMPAI SINI --}}
                            <!-- Ikon absolut di kanan dalam input -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="www.w3.org" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 18h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Detail Pengajuan Pensiun -->
    <div class="bg-white rounded-lg shadow-lg max-w-full mx-auto">
        <div class="mb-4 p-4 shadow-sm">
            <h3 class="text-lg font-semibold mb-4 text-blue-700"># Section 3: Detail Pengajuan Pensiun</h3>
            <div class="mb-6">
                <div class="relative">
                    <select id="jenis_pengajuan" name="jenis_pengajuan" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm appearance-none">
                        <option disabled selected>Pilih Jenis Pengajuan Pensiun</option>
                        <!-- Tambahkan opsi lain di sini jika diperlukan -->
                        <option value="Pensiun Normal">Pensiun Normal</option>
                        <option value="Pensiun Dini">Pensiun Dini</option>
                    </select>
                    <!-- Custom chevron icon for select (Tailwind default behavior) -->
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="h-4 w-4" xmlns="www.w3.org" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <hr class="border-b border-gray-100 mt-4 mb-4">
            <!-- START: Modifikasi Upload Dokumen Section -->
            <div class="mb-4">
                <label class="block text-xs text-gray-700 font-bold mb-4">
                    Upload Dokumen Persyaratan (Semua dokumen wajib diunggah dalam format PDF/JPG/PNG Max 5MB)
                </label>

                <div class="space-y-4">
                    <!-- Dokumen 1: Surat Permohonan Pensiun -->
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label for="dok_surat_permohonan" class="col-span-1 text-sm font-medium text-gray-700">
                            1. Surat Permohonan Pensiun
                        </label>
                        <div class="col-span-2">
                            <!-- Input File (Hidden, gunakan label untuk trigger klik) -->
                            <input type="file" id="dok_surat_permohonan" name="documents[Surat_Permohonan]" accept=".pdf,image/jpeg,image/png" required class="hidden"
                                onchange="handleFileChange(event, 'status_surat_permohonan', 'view_surat_permohonan')">

                            <!-- Area Tampilan Status dan Tombol Aksi -->
                            <div class="flex items-center justify-between p-2 border border-gray-300 rounded-lg shadow-sm">
                                <span id="status_surat_permohonan" class="text-sm text-gray-500 truncate">Belum ada file dipilih</span>
                                <div class="flex space-x-2 ml-4">
                                    <label for="dok_surat_permohonan" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded shadow-md transition duration-150">
                                        Pilih File
                                    </label>
                                    <!-- Tombol Lihat (disabled by default) -->
                                    <button type="button" id="view_surat_permohonan" onclick="viewDocument('dok_surat_permohonan')"
                                            class="bg-gray-400 text-white text-xs font-semibold py-1 px-3 rounded shadow-md cursor-not-allowed" disabled>
                                        Lihat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen 2: Salinan SK Kenaikan Gaji Pokok Berkala Terakhir -->
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label for="dok_sk_kgb" class="col-span-1 text-sm font-medium text-gray-700">
                            2. Salinan SK Kenaikan Gaji Pokok Berkala Terakhir
                        </label>
                        <div class="col-span-2">
                            <input type="file" id="dok_sk_kgb" name="documents[SK_Gaji_Pokok_Berkala_Terakhir]" accept=".pdf,image/jpeg,image/png" required class="hidden"
                                onchange="handleFileChange(event, 'status_kenaikangapok', 'view_kenaikangapok')"/>

                            <!-- Area Tampilan Status dan Tombol Aksi -->
                            <div class="flex items-center justify-between p-2 border border-gray-300 rounded-lg shadow-sm">
                                <span id="status_kenaikangapok" class="text-sm text-gray-500 truncate">Belum ada file dipilih</span>
                                <div class="flex space-x-2 ml-4">
                                    <label for="dok_sk_kgb" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded shadow-md transition duration-150">
                                        Pilih File
                                    </label>
                                    <!-- Tombol Lihat (disabled by default) -->
                                    <button type="button" id="view_kenaikangapok" onclick="viewDocument('dok_sk_kgb')"
                                            class="bg-gray-400 text-white text-xs font-semibold py-1 px-3 rounded shadow-md cursor-not-allowed" disabled>
                                        Lihat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen 3: Salinan SK Pengangkatan Pertama -->
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label for="dok_sk_angkat_pertama" class="col-span-1 text-sm font-medium text-gray-700">
                            3. Salinan SK Pengangkatan Pertama
                        </label>
                        <div class="col-span-2">
                            <input type="file" id="dok_sk_angkat_pertama" name="documents[SK_Pengangkatan_Pertama_Pegawai_Tetap]" accept=".pdf,image/jpeg,image/png" required class="hidden"
                                onchange="handleFileChange(event, 'status_skpengangkatanpertama', 'view_skpengangkatanpertama')"/>

                            <!-- Area Tampilan Status dan Tombol Aksi -->
                            <div class="flex items-center justify-between p-2 border border-gray-300 rounded-lg shadow-sm">
                                <span id="status_skpengangkatanpertama" class="text-sm text-gray-500 truncate">Belum ada file dipilih</span>
                                <div class="flex space-x-2 ml-4">
                                    <label for="dok_sk_angkat_pertama" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded shadow-md transition duration-150">
                                        Pilih File
                                    </label>
                                    <!-- Tombol Lihat (disabled by default) -->
                                    <button type="button" id="view_skpengangkatanpertama" onclick="viewDocument('dok_sk_angkat_pertama')"
                                            class="bg-gray-400 text-white text-xs font-semibold py-1 px-3 rounded shadow-md cursor-not-allowed" disabled>
                                        Lihat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen 4: Salinan SK Kenaikan Pangkat Terakhir -->
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label for="dok_sk_pangkat_terakhir" class="col-span-1 text-sm font-medium text-gray-700">
                            4. Salinan SK Kenaikan Pangkat Terakhir
                        </label>
                        <div class="col-span-2">
                            <input type="file" id="dok_sk_pangkat_terakhir" name="documents[SK_Kenaikan_Pangkat_Terakhir]" accept=".pdf,image/jpeg,image/png" required class="hidden"
                                onchange="handleFileChange(event, 'status_skpangkatterakhir', 'view_skpangkatterakhir')"/>

                            <!-- Area Tampilan Status dan Tombol Aksi -->
                            <div class="flex items-center justify-between p-2 border border-gray-300 rounded-lg shadow-sm">
                                <span id="status_skpangkatterakhir" class="text-sm text-gray-500 truncate">Belum ada file dipilih</span>
                                <div class="flex space-x-2 ml-4">
                                    <label for="dok_sk_pangkat_terakhir" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded shadow-md transition duration-150">
                                        Pilih File
                                    </label>
                                    <!-- Tombol Lihat (disabled by default) -->
                                    <button type="button" id="view_skpangkatterakhir" onclick="viewDocument('dok_sk_pangkat_terakhir')"
                                            class="bg-gray-400 text-white text-xs font-semibold py-1 px-3 rounded shadow-md cursor-not-allowed" disabled>
                                        Lihat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen 5: Daftar Penilaian Kinerja Terakhir -->
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label for="dok_penilaian_kinerja" class="col-span-1 text-sm font-medium text-gray-700">
                            5. Daftar Penilaian Kinerja Terakhir
                        </label>
                        <div class="col-span-2">
                            <input type="file" id="dok_penilaian_kinerja" name="documents[Daftar_Penilaian_Kerja_Terakhir]" accept=".pdf,image/jpeg,image/png" required class="hidden"
                                onchange="handleFileChange(event, 'status_penilaian_kinerja', 'view_penilaian_kinerja')"/>

                            <!-- Area Tampilan Status dan Tombol Aksi -->
                            <div class="flex items-center justify-between p-2 border border-gray-300 rounded-lg shadow-sm">
                                <span id="status_penilaian_kinerja" class="text-sm text-gray-500 truncate">Belum ada file dipilih</span>
                                <div class="flex space-x-2 ml-4">
                                    <label for="dok_penilaian_kinerja" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded shadow-md transition duration-150">
                                        Pilih File
                                    </label>
                                    <!-- Tombol Lihat (disabled by default) -->
                                    <button type="button" id="view_penilaian_kinerja" onclick="viewDocument('dok_penilaian_kinerja')"
                                            class="bg-gray-400 text-white text-xs font-semibold py-1 px-3 rounded shadow-md cursor-not-allowed" disabled>
                                        Lihat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Modifikasi Upload Dokumen Section -->

            <!-- Catatan/Note Section -->
            <p class="text-xs text-red-600 mt-4">
                <span class="font-semibold">Catatan:</span> Pastikan dokumen persyaratan yang anda upload dapat terlihat dengan jelas.
            </p>
            <div id="document-viewer-modal"
                style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                class="flex items-center justify-center">
                <div class="relative bg-white rounded-xl shadow-2xl w-11/12 max-w-4xl h-5/6 flex flex-col overflow-hidden">
                    <div class="p-4 bg-white border-b border-gray-200 flex justify-between items-center">
                        <strong id="pdfNameTitle" class="text-sm text-gray-800">Pratinjau Dokumen</strong>
                        <button type="button" id="viewer-close-button" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-4 rounded-lg transition duration-150 ease-in-out shadow-md">
                            Tutup X
                        </button>
                    </div>
                    <div class="flex-grow bg-gray-800 p-1">
                        <iframe id="document-viewer-iframe" src="" frameborder="0" class="w-full h-full"></iframe>
                    </div>
                </div>
            </div>
            <hr class="border-b border-gray-100 mt-4">
            <div class="mt-4 flex justify-end space-x-4">
                <button type="button" id="openModalButtonPensiun" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded rounded-lg text-sm font-semibold">
                    Buat Pengajuan Pensiun
                </button>
            </div>
        </div>

        <!-- === POPUP MODAL REVIEW === -->
        <div id="leaveModalpensiun" class="fixed inset-0 bg-black-50 flex items-center justify-center hidden z-50 backdrop-blur-sm">
            <!-- Konten Modal (Background putih, tidak terpengaruh blur) -->
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg mx-auto">
                <!-- Modal Header -->
                <div class="flex justify-between items-center border-b pb-3">
                    <h2 class="text-sm font-semibold">Data Pengajuan Pensiun</h2>
                </div>
                <!-- Modal Body (Area Review Data) -->
                <div class="mt-4">
                    <div class="space-y-4">
                        <!-- Section 1: Review Header Info -->
                        <div class="flex items-center p-3 bg-blue-50 rounded-md">
                            <span class="text-blue-600 mr-3">📄</span>
                            <!-- Kontainer untuk nama dan NUP dalam satu baris, dan tanggal di baris berikutnya -->
                            <div>
                                <!-- Gabungkan nama pegawai dan NUP dalam satu <p> tag -->
                                <p class="font-small text-gray-800">
                                    <!-- Target JS: review_nama_pegawai dan review_nup -->
                                    <span id="review_nama_pegawai">[Nama Pegawai]</span> -
                                    <span id="review_nup">[Nomor Urut Pegawai]</span>
                                </p>
                                <p class="text-sm text-gray-500">Tanggal Pengajuan: {{ now()->format('d M Y') }}</p>
                            </div>
                        </div>
                        <!-- Section 2: Data Cuti Details Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Jenis Pensiun</p>
                                <!-- Target JS: review_tanggal_mulai -->
                                <p class="mt-1 font-semibold text-sm" id="review_jenis_pengajuan">[Jenis Pengajuan]</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Unit Kerja</p>
                                <!-- Target JS: review_tanggal_mulai -->
                                <p class="mt-1 font-semibold text-sm" id="review_unit_kerja">[Unit Kerja]</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status Pegawai</p>
                                <!-- Target JS: review_tanggal_selesai -->
                                <p class="mt-1 font-semibold text-sm" id="review_status_pegawai">[Status Pegawai]</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Jabatan Terakhir</p>
                                <!-- Target JS: review_jumlah_cuti -->
                                <p class="mt-1 font-semibold text-sm" id="review_jabatan">[Jabatan Terakhir]</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Pangkat</p>
                                <!-- Target JS: review_jatah_periode_hari -->
                                <p class="mt-1 font-semibold text-sm" id="review_pangkat">[Pangkat]</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Grade</p>
                                <!-- Target JS: review_jatah_periode_hari -->
                                <p class="mt-1 font-semibold text-sm" id="review_grade">[Grade]</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">TMT Pegawai</p>
                                <!-- Target JS: review_jatah_periode_hari -->
                                <p class="mt-1 font-semibold text-sm" id="review_tmt_pegawai">[TMT Pegawai]</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Masa Kerja</p>
                                <!-- Target JS: review_jatah_periode_hari -->
                                <p class="mt-1 font-semibold text-sm" id="review_masa_kerja">[Masa Kerja]</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">TMT Pensiun</p>
                                <!-- Target JS: review_jatah_periode_hari -->
                                <p class="mt-1 font-semibold text-sm" id="review_tmt_pensiun">[TMT Pensiun]</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer (Buttons) -->
                <div class="flex justify-end mt-6 pt-4 border-t">
                    <button type="button" id="cancelButton" class="px-4 py-2 mr-3 text-sm text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                    <!-- TOMBOL KONFIRMASI SUBMIT FORM -->
                    <button type="button" id="submitButton" class="px-4 py-2 text-white bg-blue-600 text-sm rounded-lg hover:bg-blue-700">Ya, Ajukan Pensiun</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Loading Overlay -->
    <div id="loadingModalpensiun" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-gray-50 p-8 rounded-xl shadow-2xl flex flex-col items-center">
            <!-- Heroicon Paper Airplane dengan Animasi Pulse -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-blue-600 animate-fly">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
            </svg>

            <p class="mt-4 text-gray-800 text-md font-semibold">Sedang Mengirim Data Pengajuan...</p>
        </div>
    </div>

    <!-- Modal Sukses (Sesuai Gambar) -->
    <div id="successModalPensiun" class="{{ Session::has('success') ? 'show-on-load' : '' }} fixed inset-0 bg-black/50 items-center justify-center hidden z-50 backdrop-blur-sm">
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
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Pengajuan Pensiun berhasil dibuat!!!</h2>
                <p class="mt-2 text-sm text-gray-500">Permintaan Pensiun Anda telah tercatat dan menunggu persetujuan atasan. Silahkan cek notifikasi secara berkala terkait Pengajuan Pensiun anda</p>
            </div>
            <!-- Wadah Tombol -->
            <div class="mt-6 flex justify-center space-x-4">
                <!-- Tombol Lihat Pengajuan -->
                {{-- <a href="{{ route('pegawai.formDataPengajuan', ['type' => 'pensiun']) }}" class="px-6 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                    Lihat Pengajuan
                </a> --}}
            </div>
        </div>
    </div>

    <!-- Modal Error -->
    <!-- Perhatikan perubahan pada class dan isi pesan p -->
    <div id="errorModalPensiun" class="{{ (Session::has('error') || $errors->any()) ? 'show-on-load' : '' }} fixed inset-0 bg-black/50 items-center justify-center hidden z-50 backdrop-blur-sm">
        <div class="bg-gray-50 p-8 rounded-xl shadow-2xl w-full max-w-sm mx-4">
            <div class="text-center">
                <svg class="mx-auto h-16 w-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="www.w3.org">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="mt-4 text-xl font-bold text-gray-900">Gagal Mengajukan Pensiun</h2>
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
    </div>

    <!-- Modal Notifikasi Validasi File -->
    <div id="fileValidationErrorModal" class="fixed inset-0 bg-black/50 items-center justify-center hidden z-[100] backdrop-blur-sm">
        <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-sm mx-4 transform transition-all">
            <div class="text-center">
                <!-- Icon Warning -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 mb-4">
                    <svg class="h-10 w-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Peringatan Upload</h2>
                <p id="fileValidationMessage" class="mt-3 text-sm text-gray-500 leading-relaxed">
                    Pesan validasi akan muncul di sini.
                </p>
            </div>
            <div class="mt-6">
                <button id="closeFileValidationError" type="button" class="w-full px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition-colors shadow-lg">
                    Mengerti
                </button>
            </div>
        </div>
    </div>

{{-- </form> --}}

@push('scripts')
    @vite(['resources/js/app.js', 'resources/js/pensiun.js'])
@endpush

<script>
    // Fungsi untuk memperbarui status file yang dipilih dan mengaktifkan tombol Lihat
    function handleFileChange(event, statusElementId, viewButtonId) {
        const input = event.target;
        const statusElement = document.getElementById(statusElementId);
        const viewButton = document.getElementById(viewButtonId);

        if (input.files && input.files[0]) {
            statusElement.textContent = input.files[0].name;
            viewButton.disabled = false;
            viewButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            viewButton.classList.add('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');
        } else {
            statusElement.textContent = 'Belum ada file dipilih';
            viewButton.disabled = true;
            viewButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            viewButton.classList.remove('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');
        }
    }

    // Fungsi untuk menampilkan dokumen di modal viewer (menggunakan FileReader API)
    function viewDocument(inputId) {
        const input = document.getElementById(inputId);
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const modal = document.getElementById('document-viewer-modal');
                const iframe = document.getElementById('document-viewer-iframe');
                const title = document.getElementById('pdfNameTitle');

                title.textContent = file.name;
                iframe.src = e.target.result; // Data URL untuk pratinjau
                modal.style.display = 'flex';
            };
            reader.readAsDataURL(file); // Membaca file sebagai Data URL
        }
    }

    // Fungsi untuk menutup modal viewer (dari kode blade Anda sebelumnya)
    document.getElementById('viewer-close-button').onclick = function() {
        document.getElementById('document-viewer-modal').style.display = 'none';
        document.getElementById('document-viewer-iframe').src = '';
    }
</script>

@endsection
