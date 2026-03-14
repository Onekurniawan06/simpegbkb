@extends($layout)

@section('content')

{{-- Container Utama dengan Background Gray dan Rounded sesuai permintaan --}}
{{-- Script Alpine.js untuk Live Filter --}}
<script src="https://unpkg.com" defer></script>

<div x-data="{
    search: '',
    status: '',
    {{-- Fungsi Filter: Mengecek kecocokan Nama ATAU NUP --}}
    filterRow(nama, nup, statusRow) {
        const keyword = this.search.toLowerCase();
        const matchSearch = nama.toLowerCase().includes(keyword) || nup.toString().includes(keyword);
        const matchStatus = this.status === '' || statusRow === this.status;
        return matchSearch && matchStatus;
    }
}" class="h-full w-full bg-gray-100 rounded-tl-md shadow-lg flex flex-col overflow-hidden">

    <!-- HEADER & STATISTIK (Tetap di Atas) -->
    <div class="p-3 pb-2 pr-0">
        <div class="mb-4 text-gray-800">
            <h5 class="text-[11px] font-bold uppercase tracking-widest opacity-50">Total Pegawai</h5>
            <h2 class="text-3xl font-black leading-none">{{ $pegawaiDivisi->total() }} <span class="text-xs font-normal opacity-40">Pegawai</span></h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 pr-3 text-[11px]">
            @php
                $statusConfigs = [
                    'Pegawai Tetap' => 'border-orange-500',
                    'Pegawai Kontrak' => 'border-yellow-400',
                    'Pegawai Harian Lepas' => 'border-purple-500',
                    'Pegawai Bulanan' => 'border-emerald-500',
                    'Pegawai Alih Daya' => 'border-sky-500'
                ];
            @endphp
            @foreach($statusConfigs as $label => $borderClass)
            <div class="bg-white p-3 rounded-xl border-l-4 {{ $borderClass }} shadow-sm">
                <span class="text-[10px] font-bold text-gray-400 uppercase leading-none">{{ $label }}</span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-xl font-bold text-gray-700">{{ $stats[$label] ?? 0 }}</span>
                    <span class="text-[9px] text-gray-400">Orang</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- CONTAINER UTAMA TABEL -->
    <div class="flex-1 pl-0 pr-0 overflow-hidden mt-2">
        <div class="bg-white border border-gray-200 border-r-0 border-b-0 h-full flex flex-col overflow-hidden">

            <!-- FILTER BAR (SEARCH NAMA/NUP & STATUS) -->
            <div class="p-3 border-b border-gray-50 flex flex-wrap gap-3">
                <div class="relative w-full md:w-64">
                    <input x-model="search" type="text" placeholder="Cari Nama atau NUP..."
                        class="w-full pl-4 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs outline-none focus:ring-1 focus:ring-blue-400 transition-all">
                    <i class="fa fa-search absolute left-3 top-2.5 text-gray-300"></i>
                </div>

                <select x-model="status" class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-500 outline-none hover:bg-gray-100 transition-colors">
                    <option value="">Semua Status Pegawai</option>
                    @foreach($statusConfigs as $label => $class)
                        <option value="{{ $label }}">{{ $label }}</option>
                    @endforeach
                </select>

                {{-- Indikator Filter Aktif --}}
                <template x-if="search.length > 0 || status !== ''">
                    <button @click="search = ''; status = ''" class="text-[10px] text-red-500 font-bold hover:underline">
                        Reset Filter
                    </button>
                </template>
            </div>

            <!-- AREA DATA SCROLLABLE -->
            <div class="flex-1 overflow-y-auto custom-scroll-container px-3">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 z-30 bg-white">
                        <tr class="text-gray-400 text-[10px] uppercase tracking-widest">
                            <th class="py-4 pl-2 bg-white border-b border-gray-50 shadow-[0_1px_0_rgba(0,0,0,0.05)]">NUP</th>
                            <th class="py-4 bg-white border-b border-gray-50 shadow-[0_1px_0_rgba(0,0,0,0.05)]">Nama Pegawai</th>

                            {{-- Tampilkan Kolom Divisi Jika Direktur --}}
                            @if(auth()->user()->level_id == 3)
                                <th class="py-4 bg-white border-b border-gray-50 shadow-[0_1px_0_rgba(0,0,0,0.05)]">Divisi</th>
                            @endif

                            <th class="py-4 bg-white border-b border-gray-50 shadow-[0_1px_0_rgba(0,0,0,0.05)]">Jabatan</th>
                            <th class="py-4 bg-white border-b border-gray-50 shadow-[0_1px_0_rgba(0,0,0,0.05)]">Status</th>
                            <th class="py-4 text-center bg-white border-b border-gray-50 shadow-[0_1px_0_rgba(0,0,0,0.05)]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pegawaiDivisi as $p)
                        {{-- Row otomatis sembunyi/muncul berdasarkan input --}}
                        <tr x-show="filterRow('{{ $p->nama }}', '{{ $p->nomor_urut_pegawai }}', '{{ $p->status_pegawai }}')"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            class="hover:bg-blue-50/30 transition-colors border-b border-gray-50 group">

                            <td class="py-3 pl-2 text-gray-400 text-[12px] font-medium">{{ $p->nomor_urut_pegawai }}</td>
                            <td class="py-3 font-bold text-gray-700 text-[12px]">{{ $p->nama }}</td>
                            <td class="py-3 text-gray-500 text-[12px]">{{ $p->jabatan }}</td>
                            <td class="py-3 text-gray-500 text-[12px]">{{ $p->pangkat }} / {{ $p->grade }}</td>
                            <td class="py-3">
                                <span class="bg-orange-50 text-orange-600 text-[10px] px-2 py-1 rounded font-bold uppercase border border-orange-100">
                                    {{ $p->status_pegawai }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                {{-- Memanggil route dengan parameter NUP pegawai --}}
                                <a href="{{ route('manager.pegawai.detail', $p->nomor_urut_pegawai) }}"
                                class="bg-[#001A4E] text-white px-3 py-1.5 rounded-lg text-[10px] font-bold hover:bg-blue-900 transition-all shadow-sm">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pesan Jika Data Kosong Setelah Difilter --}}
                <div x-cloak
                    x-show="(search !== '' || status !== '') && $el.parentElement.querySelectorAll('tbody tr[x-show]:not([style*=\'display: none\'])').length === 0"
                    class="py-20 text-center text-gray-300 flex flex-col items-center w-full">
                    <i class="fa fa-user-slash text-4xl mb-2 opacity-20"></i>
                    <p class="text-xs font-medium">Data pegawai tidak ditemukan untuk filter ini...</p>
                    <button @click="search = ''; status = ''" class="mt-3 text-[10px] text-blue-500 hover:underline">
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- FOOTER PAGINATION (Tetap di Dasar) -->
            <div class="p-4 pr-10 border-t border-gray-100 bg-white">
                <div class="flex justify-end pagination-custom no-info">
                    {{ $pegawaiDivisi->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
