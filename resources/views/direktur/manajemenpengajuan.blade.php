{{-- resources/views/dashboard.blade.php (Hanya HTML/Blade, tanpa JS inline) --}}
@extends('layouts.app-direktur')

{{-- Konten Utama Halaman Dashboard Admin --}}

@section('content')
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 text-xs font-bold rounded shadow-sm flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
@endif

<div class="rounded-l-md text-white relative group transition-all duration-300">
    <!-- Ornamen Lingkaran 1 (Kanan Atas) -->
    <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
    
    <!-- Ornamen Lingkaran 2 ( Kiri Bawah ) -->
    <div class="absolute -left-6 -bottom-6 w-20 h-20 bg-white/5 rounded-full group-hover:scale-110 transition-transform duration-500"></div>

    <!-- Padding diperkecil (p-3) agar ramping -->
    <div class="relative z-10 p-3">
        <!-- Bagian Atas -->
        <div class="flex justify-between items-center mb-2">
            <div class="flex items-center gap-3">
                <span class="text-3xl font-black text-white">{{ $totalMenunggu }}</span>
                <div class="flex flex-col">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-100/80 leading-none">Pengajuan Masuk</span>
                    <span class="text-[11px] font-bold text-white uppercase mt-0.5 leading-none">Menunggu Persetujuan</span>
                </div>
            </div>

            <!-- Ikon dengan Efek Kaca Lebih Kecil -->
            <div class="bg-white/15 p-1.5 rounded-lg backdrop-blur-md border border-white/20 shadow-inner">
                <svg xmlns="http://w3.org" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- List Badge Dinamis (Dibuat Lebih Tipis) -->
        <div class="flex flex-wrap gap-2 pt-2 border-t border-white/10">
            @if(isset($detailMenunggu))
                @foreach($detailMenunggu as $detail)
                    @if($detail['jumlah'] > 0)
                    <div class="bg-white/10 backdrop-blur-sm border border-white/15 px-2.5 py-1 rounded-lg flex items-center gap-1.5 hover:bg-white/20 transition-colors cursor-default">
                        <span class="text-[9px] font-bold text-amber-50 uppercase leading-none">{{ $detail['label'] }}</span>
                        <div class="flex items-baseline gap-0.5 leading-none">
                            <span class="text-xs font-extrabold text-white">{{ $detail['jumlah'] }}</span>
                            <span class="text-[8px] font-medium text-amber-100">Data</span>
                        </div>
                    </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>


{{-- section data approval pegawai --}}
<div class="bg-gray-100 rounded-tl-md shadow-lg max-w-full mt-2 h-screen flex flex-col overflow-hidden">
    <div class="p-3 shadow-sm flex flex-col h-full">
        <span class="text-sm font-semibold text-blue-700">#Section Data Pengajuan Pegawai</span>
        <hr class="border-b border-gray-200 mt-2">

        <form action="{{ url()->current() }}" method="GET"> {{-- Otomatis mengikuti route direktur yang aktif --}}
            <div class="bg-teal-50 p-4 rounded-t-lg border border-gray-300 border-b-0 mt-2">
                <div class="flex flex-wrap items-end gap-3">
                    <!-- Cari Pegawai -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Cari Pegawai</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Nomor Urut Pegawai..." class="w-full px-3 py-2 border border-gray-300 rounded text-xs outline-none focus:ring-1 focus:ring-blue-500">
                    </div>

                    <!-- Filter Tanggal -->
                    <div class="w-40">
                        <label class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-2 py-2 border border-gray-300 rounded text-xs outline-none bg-white">
                    </div>
                    <div class="w-40">
                        <label class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-2 py-2 border border-gray-300 rounded text-xs outline-none bg-white">
                    </div>

                    <!-- Filter Jenis Pengajuan (Sudah Ditambahkan Pensiun & Kenaikan) -->
                    <div class="w-48"> {{-- Lebar sedikit ditambah agar teks tidak terpotong --}}
                        <label class="text-[11px] font-bold text-gray-500 uppercase block mb-1">Jenis Pengajuan</label>
                        <select name="jenis" class="w-full px-3 py-2 border border-gray-300 rounded text-xs outline-none bg-white">
                            <option value="">Semua Jenis</option>
                            <option value="Lembur" {{ request('jenis') == 'Lembur' ? 'selected' : '' }}>Lembur</option>
                            <option value="Cuti" {{ request('jenis') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="Pensiun" {{ request('jenis') == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                            <option value="Kenaikan" {{ request('jenis') == 'Kenaikan' ? 'selected' : '' }}>Kenaikan Pangkat/Gaji</option>
                        </select>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white p-2.5 rounded shadow-md transition-all active:scale-95" title="Tampilkan">
                            <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button>

                        {{-- Reset Filter --}}
                        <a href="{{ url()->current() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 p-2.5 rounded shadow-sm transition-all active:scale-95" title="Reset">
                            <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- 2. WADAH TABEL (Otomatis mengisi sisa ruang ke bawah) -->
        <div class="bg-white shadow-md rounded-b-lg border border-gray-300 flex flex-col flex-1 min-h-0 overflow-hidden mb-0">
            <div class="flex-1 overflow-y-auto overflow-x-auto custom-scroll-container min-h-0">
                <table class="w-full border-separate border-spacing-0 table-fixed border-t-0 border-l-0">
                    <thead class="sticky top-0 z-20 bg-gray-100 shadow-sm">
                        <tr class="text-gray-700 text-[11px] font-bold uppercase tracking-wider">
                            <th class="w-[15%] px-2 py-2 border-b border-r border-gray-300 text-center">Tanggal Pengajuan</th>
                            <th class="w-[15%] px-2 py-2 border-b border-r border-gray-300 text-center">Nomor Urut Pegawai</th>
                            <th class="w-[20%] px-2 py-2 border-b border-r border-gray-300 text-center">Nama Pegawai</th>
                            <th class="w-[15%] px-2 py-2 border-b border-r border-gray-300 text-center">Jenis Pengajuan</th>
                            <th class="w-[15%] px-2 py-2 border-b border-r border-gray-300 text-center">Status Pengajuan</th>
                            <th class="w-[15%] px-2 py-2 border-b border-gray-300 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataPengajuan as $item)
                            <tr class="bg-white hover:bg-blue-50/50 transition-colors duration-150 h-px">
                                <td class="px-2 py-1.5 border-b border-r border-gray-200 text-center text-[11px] font-medium text-gray-600">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                </td>
                                <td class="px-2 py-1.5 border-b border-r border-gray-200 text-center text-[11px] font-semibold text-gray-700">
                                    {{ $item->nup }}
                                </td>
                                <td class="px-3 py-1.5 border-b border-r border-gray-200 text-center text-[11px] font-bold text-gray-800 uppercase leading-tight truncate">
                                    {{ $item->nama }}
                                </td>
                                <td class="px-2 py-1.5 border-b border-r border-gray-200 text-center text-[11px] font-bold text-blue-600">
                                    {{ $item->jenis }}
                                </td>
                                <td class="px-2 py-1.5 border-b border-r border-gray-200 text-center">
                                    @php
                                    $statusClasses = [
                                        'diproses' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'disetujui' => 'bg-green-100 text-green-800 border-green-200',
                                        'ditolak' => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                    $statusLabel = $item->status;
                                    $class = $statusClasses[$statusLabel] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                                    @endphp
                                    <span class="{{ $class }} px-2 py-0.5 rounded border text-[9px] font-extrabold uppercase tracking-tighter inline-block min-w-[65px] shadow-sm">
                                        {{ $statusLabel == 'diproses' ? 'DIPROSES' : ($statusLabel == 'disetujui' ? 'SETUJU' : 'DITOLAK') }}
                                    </span>
                                </td>
                                <td class="px-2 py-1.5 border-b border-gray-200 text-center">
                                    <div class="flex flex-row justify-center items-center gap-1.5 whitespace-nowrap">

                                        <!-- SESUAIKAN ROUTE DI SINI -->
                                        <a href="{{ route('direktur.detailApproval', ['sumber' => $item->sumber, 'id_log' => $item->id_transaksi]) }}"
                                        class="bg-[#001f3f] hover:bg-black text-white py-1 px-2 rounded text-[8px] font-bold tracking-tighter transition shadow-sm">
                                        DETAIL
                                        </a>

                                        @if($item->status == 'disetujui')
                                            <a href="#" class="bg-green-600 hover:bg-green-700 text-white py-1 px-2 rounded text-[9px] font-bold tracking-tighter transition shadow-sm">
                                                DOWNLOAD
                                            </a>
                                        @else
                                            <button class="bg-[#aab2bd] text-white py-1 px-2 rounded text-[9px] font-bold tracking-tighter opacity-70 cursor-not-allowed">
                                                DOWNLOAD
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-10 border-b border-gray-200 text-center text-[11px] text-gray-400 italic bg-gray-50 border-r">Belum ada data pengajuan.</td></tr>
                        @endforelse

                        <!-- BARIS SPACER -->
                        <tr class="h-full">
                            <td class="border-r border-gray-200"></td>
                            <td class="border-r border-gray-200"></td>
                            <td class="border-r border-gray-200"></td>
                            <td class="border-r border-gray-200"></td>
                            <td class="border-r border-gray-200"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 3. NAVIGASI PAGINATION -->
            <div class="px-4 py-2 bg-gray-50 border-t border-gray-300 flex items-center justify-between shrink-0">
                <div class="text-[11px] text-gray-500 font-medium">Showing <b>{{ $dataPengajuan->firstItem() }}</b> to <b>{{ $dataPengajuan->lastItem() }}</b> of <b>{{ $dataPengajuan->total() }}</b></div>
                <div class="flex items-center shadow-sm rounded-md border border-gray-300 overflow-hidden bg-white">
                    @if ($dataPengajuan->onFirstPage())
                        <span class="px-2 py-1 bg-gray-50 text-gray-400 text-[11px] border-r border-gray-300 cursor-not-allowed"> &lt; </span>
                    @else
                        <a href="{{ $dataPengajuan->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" class="px-2 py-1 bg-white hover:bg-gray-100 text-gray-600 text-[10px] border-r border-gray-300 transition"> &lt; </a>
                    @endif
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-[11px] font-bold border-r border-gray-300">{{ $dataPengajuan->currentPage() }}</span>
                    @if ($dataPengajuan->hasMorePages())
                        <a href="{{ $dataPengajuan->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" class="px-2 py-1 bg-white hover:bg-gray-100 text-gray-600 text-[10px] transition"> &gt; </a>
                    @else
                        <span class="px-2 py-1 bg-gray-50 text-gray-400 text-[11px] cursor-not-allowed"> &gt; </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TOMBOL BACK TO TOP (Ditempatkan di sini, akan melayang di atas mainContent) -->
<button id="backToTop" style="display: none;" class="fixed bottom-10 right-10 bg-blue-300 text-white p-3 rounded-full shadow-2xl hover:bg-blue-600 transition-all duration-300 z-50 flex items-center justify-center" title="Kembali ke atas">
    <x-heroicon-o-chevron-up id="arrowIcon" class="h-6 w-6 " />
</button>

{{-- Skrip JavaScript yang menargetkan #mainContent --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const backToTopButton = document.getElementById('backToTop');
        const scrollableElement = document.getElementById('mainContent');

        if (!scrollableElement) {
            console.error('Elemen #mainContent tidak ditemukan.');
            return;
        }

        function toggleBackToTop() {
            // Cek posisi scroll dari elemen target
            if (scrollableElement.scrollTop > 100) { // Nilai diubah menjadi 100
                backToTopButton.style.display = 'flex';
            } else {
                backToTopButton.style.display = 'none';
            }
        }

        function scrollToTop() {
            scrollableElement.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        scrollableElement.addEventListener('scroll', toggleBackToTop);
        backToTopButton.addEventListener('click', scrollToTop);

        toggleBackToTop();
    });
</script>
@endpush

@endsection


