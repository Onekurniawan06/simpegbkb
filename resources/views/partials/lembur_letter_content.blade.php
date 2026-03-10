{{-- resources/views/partials/letter_content.blade.php --}}

@if(isset($is_pdf) && $is_pdf)
    <style>
        @page { margin: 0cm 0cm 0cm 0cm !important; }
        body {
            font-family: sans-serif;
            font-size: 10pt;
            margin: 0cm !important;
            padding: 0cm !important;
            line-height: 1.4;
        }
        .content-wrap {
            padding-bottom: 45pt !important;
            padding-top: 20pt;
            padding-left: 40pt !important;
            padding-right: 40pt !important;
        }
        .main-detail-table { width: 100%; border-collapse: collapse; }
        .main-detail-table td { padding-top: 1pt; padding-bottom: 1pt; vertical-align: top; }
        .label-column { width: 140pt; padding-left: 12pt; }
        .data-column { font-weight: bold; }
        p, ul { margin-top: 10pt !important; margin-bottom: 10pt !important; }
        ul.list-disc li { margin-bottom: 4pt !important; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 5pt !important; margin-top: 5pt; }
        .header-table td { vertical-align: middle; padding: 0; }
        .logo-img { height: 44px; width: auto; margin-right: 10pt; }
        .perumda-name { font-size: 10pt; font-weight: bold; text-align: right; }
        .date-section { text-align: right; font-size: 10pt; margin-bottom: 12pt !important; }
        .salutation { margin-bottom: 12pt !important; font-size: 10pt; }
        .signature-section-table { width: 100%; border-collapse: collapse; margin-top: 20pt !important; }
        .signature-section-table td { vertical-align: bottom; padding: 0; height: 90pt; text-align: center; font-size: 10pt; }
        .signature-label { display: block; margin-bottom: 4pt; }
        .signature-name { font-weight: bold; margin-top: 4pt; display: block; }
        .signature-title { display: block; }
        .footer-pdf-fixed { position: absolute; bottom: 0; left: 0; right: 0; height: 30pt; }
        .footer-table-fixed { width: 100%; height: 100%; border-collapse: collapse; padding: 0; }
        .footer-text { color: rgb(198, 177, 177); font-size: 9pt; margin: 0; padding: 0 10pt; }
        .text-sm { font-size: 10pt; }
        .font-semibold { font-weight: bold; }
    </style>
@endif

<div class="content-wrap">

<!-- Header Section -->
@if(isset($is_pdf) && $is_pdf)
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                @php
                    $path = public_path('images/logobkb.png');
                    $base64 = file_exists($path) ? 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path)) : '';
                @endphp
                @if($base64)
                    <img src="{{ $base64 }}" alt="Logo Perusahaan" class="logo-img">
                @endif
            </td>
            <td class="perumda-name">Perumda BPR Bank Kota Bogor</td>
        </tr>
    </table>
@else
    <div class="flex justify-between items-center mb-3">
        <div class="flex items-center">
            @php
                $path = public_path('images/logobkb.png');
                $base64 = file_exists($path) ? 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path)) : '';
            @endphp
            @if($base64)
                <img src="{{ $base64 }}" alt="Logo Perusahaan" class="h-11 w-auto">
            @endif
        </div>
        <p class="text-sm font-bold">Perumda BPR Bank Kota Bogor</p>
    </div>
@endif

{{-- Garis Biru Penuh --}}
<div style="background-color: #0000FF; height: 20px; width: 100%; margin-bottom: 10pt;"></div>

<!-- Date Section -->
<div class="{{ (isset($is_pdf) && $is_pdf) ? 'date-section' : 'text-right text-sm mb-3' }}" style="{{ (isset($is_pdf) && $is_pdf) ? 'text-align: right;' : '' }}">
    <p>Bogor, {{\Carbon\Carbon::parse($lembur->created_at)->format('d F Y') }}</p>
</div>

<!-- Salutation -->
<p class="{{ (isset($is_pdf) && $is_pdf) ? '' : 'mb-4 text-sm' }}">Dengan hormat,</p>

@if(isset($is_pdf) && $is_pdf)
    {{-- PDF View: Menggunakan satu tabel besar --}}
    <table class="main-detail-table mb-4">
        {{-- Employee Details --}}
        <tr><td class="label-column">NUP Pegawai</td><td class="data-column">{{ $lembur->nomor_urut_pegawai ?? 'N/A'}}</td></tr>
        <tr><td class="label-column">Nama Pegawai</td><td class="data-column">{{ $lembur->pegawai->nama ?? 'N/A' }}</td></tr>
        {{-- Menggunakan operator safe access (?->) --}}
        <tr><td class="label-column">Divisi</td><td class="data-column">{{ $lembur->pegawai->pekerjaan?->divisi?->nama_divisi ?? 'N/A' }}</td></tr>
        <tr><td class="label-column">Jabatan</td><td class="data-column">{{ $lembur->pegawai->pekerjaan?->jabatan ?? 'N/A' }}</td></tr>
    </table>
@else
    {{-- Tampilan Web: Menggunakan Grid dan margin konsisten --}}
    <div class="ml-4">
        <div class="grid grid-cols-[160px_1fr] gap-x-4 gap-y-2 mb-4 text-sm">
            <div class="font-normal">NUP Pegawai</div><div class="font-semibold">{{ $lembur->nomor_urut_pegawai ?? 'N/A'}}</div>
            <div class="font-normal">Nama Pegawai</div><div class="font-semibold">{{ $lembur->pegawai->nama ?? 'N/A' }}</div>
            {{-- Menggunakan operator safe access (?->) --}}
            <div class="font-normal">Divisi</div><div class="font-semibold">{{ $lembur->pegawai->pekerjaan?->divisi?->nama_divisi ?? 'N/A' }}</div>
            <div class="font-normal">Jabatan</div><div class="font-semibold">{{ $lembur->pegawai->pekerjaan?->jabatan ?? 'N/A' }}</div>
        </div>
    </div>
@endif

<!-- Paragraf pengantar tabel -->
<p class="text-sm {{ (isset($is_pdf) && $is_pdf) ? '' : 'mb-3' }}">untuk melaksanakan kerja lembur pada,</p>

<p class="text-sm font-semibold {{ (isset($is_pdf) && $is_pdf) ? '' : 'mb-3' }}">Tanggal Mulai Lembur : {{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d F Y') ?? '[Hari/Tanggal]' }}</p>

{{-- START: TABLE URAIAN TUGAS DAN WAKTU LEMBUR --}}
<table class="w-full border-collapse text-sm {{ (isset($is_pdf) && $is_pdf) ? 'main-detail-table' : 'table-auto mb-8' }}"
       style="{{ (isset($is_pdf) && $is_pdf) ? 'width: 100%; border-collapse: collapse; font-size: 0.875rem;' : '' }}">
    <thead>
        <tr class="{{ (isset($is_pdf) && $is_pdf) ? '' : 'bg-gray-100' }}"
            style="{{ (isset($is_pdf) && $is_pdf) ? 'background-color: #f3f4f6;' : '' }}">

            {{-- Menggunakan lebar 35% untuk PDF (mirip w-4/12), dan w-4/12 untuk Web --}}
            <th rowspan="2" class="border px-4 py-2 text-center font-semibold align-middle {{ (isset($is_pdf) && $is_pdf) ? '' : 'w-4/12' }}"
                style="{{ (isset($is_pdf) && $is_pdf) ? 'border: 1px solid #000; padding: 10px; text-align: center; vertical-align: middle; width: 45%;' : '' }}">Uraian Tugas</th>

            <th colspan="3" class="border px-4 py-2 text-center font-semibold"
                style="{{ (isset($is_pdf) && $is_pdf) ? 'border: 1px solid #000; padding: 10px; text-align: center;' : '' }}">Perkiraan Waktu Lembur</th>
        </tr>
        <tr class="{{ (isset($is_pdf) && $is_pdf) ? '' : 'bg-gray-100' }}"
            style="{{ (isset($is_pdf) && $is_pdf) ? 'background-color: #f3f4f6;' : '' }}">

            {{-- Lebar Waktu di PDF disesuaikan agar mirip rasio asli Anda --}}
            <th class="border px-4 py-2 text-center font-semibold {{ (isset($is_pdf) && $is_pdf) ? '' : 'w-1/6' }}"
                style="{{ (isset($is_pdf) && $is_pdf) ? 'border: 1px solid #000; padding: 10px; text-align: center; width: 20%;' : '' }}">Jam Mulai</th>
            <th class="border px-4 py-2 text-center font-semibold {{ (isset($is_pdf) && $is_pdf) ? '' : 'w-1/6' }}"
                style="{{ (isset($is_pdf) && $is_pdf) ? 'border: 1px solid #000; padding: 10px; text-align: center; width: 20%;' : '' }}">Jam Selesai</th>
            <th class="border px-4 py-2 text-center font-semibold {{ (isset($is_pdf) && $is_pdf) ? '' : 'w-1/6' }}"
                style="{{ (isset($is_pdf) && $is_pdf) ? 'border: 1px solid #000; padding: 10px; text-align: center; width: 20%;' : '' }}">Jumlah Jam</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border px-4 py-2" style="{{ (isset($is_pdf) && $is_pdf) ? 'border: 1px solid #000; padding: 10px;' : '' }}">{{ $lembur->uraian_tugas }}</td>
            <td class="border px-4 py-2 text-center" style="{{ (isset($is_pdf) && $is_pdf) ? 'border: 1px solid #000; padding: 10px; text-align: center;' : '' }}">{{ $lembur->jam_mulai }}</td>
            <td class="border px-4 py-2 text-center" style="{{ (isset($is_pdf) && $is_pdf) ? 'border: 1px solid #000; padding: 10px; text-align: center;' : '' }}">{{ $lembur->jam_selesai }}</td>
            <td class="border px-4 py-2 text-center" style="{{ (isset($is_pdf) && $is_pdf) ? 'border: 1px solid #000; padding: 10px; text-align: center;' : '' }}">{{ $lembur->total_jam_lembur }}</td>
        </tr>
    </tbody>
</table>
{{-- END: TABLE URAIAN TUGAS DAN WAKTU LEMBUR --}}


<!-- Justification Paragraph (Disesuaikan untuk Lembur) -->
<p class="text-sm {{ (isset($is_pdf) && $is_pdf) ? '' : 'italic text-gray-600' }}" style="{{ (isset($is_pdf) && $is_pdf) ? 'font-size: 0.875rem;' : '' }}">
    Berdasarkan validasi atas kegiatan kerja lembur sebagaimana Surat Perintah Kerja Lembur (SPKL) diatas, maka kegiatan kerja lembur pegawai bisa dilaksanakan sesuai rincian waktu yang telah disetujui.
</p>

<!-- Realisasi Waktu Lembur Line (dari gambar) -->
<p class="text-sm mt-3 mb-6">
    Realisasi Waktu Kerja Lembur : <span class="font-semibold">{{ $lembur->jam_mulai }}</span> s/d <span class="font-semibold">{{ $lembur->jam_selesai }}</span> Total Jam Kerja Lembur : <span class="font-semibold">{{ $lembur->total_jam_lembur }} </span>
</p>

<!-- Approvals/Signatures Section -->
@if(isset($lembur->logPersetujuanLembur) && count($lembur->logPersetujuanLembur) > 0)
    {{-- Cek apakah HANYA ada 'Pengajuan Awal' saja di dalam log --}}
    @php
        $hanyaPengajuanAwal = $lembur->logPersetujuanLembur->every(function($log) {
            return $log->tahap_persetujuan === 'Pengajuan Awal' || $log->tahap_persetujuan === 'Pegawai';
        });
    @endphp

    @if($hanyaPengajuanAwal)
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-6">
            <p class="text-sm text-blue-700">
                <strong>Informasi:</strong> Pengajuan lembur telah dibuat dan sedang menunggu proses verifikasi awal.
            </p>
        </div>
    @else
        <p class="text-sm mt-6 mb-2">Dengan beberapa persetujuan yaitu,</p>

        @if(isset($is_pdf) && $is_pdf)
            {{-- PDF View --}}
            <table class="main-detail-table">
                @php $approvalCounter = 1; @endphp
                @foreach ($lembur->logPersetujuanLembur as $log)
                    @if ($log->tahap_persetujuan !== 'Pengajuan Awal' && $log->tahap_persetujuan !== 'Pegawai')
                        <tr>
                            <td class="label-column">{{ $approvalCounter++ }}. Tahap Persetujuan</td>
                            <td class="data-column">{{ $log->tahap_persetujuan ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="label-column" style="padding-left: 15pt;"><span style="padding-left: 8pt;">Status Persetujuan</span></td>
                            <td class="data-column">
                                @if($log->status_pengajuan == 'ditolak')
                                    <span style="color: #FF0000;">Ditolak</span>
                                @elseif($log->status_pengajuan == 'disetujui')
                                    <span style="color: #008000;">Disetujui</span>
                                @else
                                    <span>Menunggu</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="label-column" style="padding-left: 15pt;"><span style="padding-left: 8pt;">Catatan</span></td>
                            <td>{{ $log->komentar ?? 'N/A' }}</td>
                        </tr>
                        <tr><td colspan="2" style="padding-bottom: 10pt;"></td></tr>
                    @endif
                @endforeach
            </table>
        @else
            {{-- Tampilan Web --}}
            <div class="ml-4">
                <div class="grid grid-cols-[160px_1fr] gap-x-4 gap-y-2 text-sm">
                    @php $approvalCounter = 1; @endphp
                    @foreach ($lembur->logPersetujuanLembur as $log)
                        @if ($log->tahap_persetujuan !== 'Pengajuan Awal' && $log->tahap_persetujuan !== 'Pegawai')
                            <div class="font-normal">{{ $approvalCounter++ }}. Tahap Persetujuan</div>
                            <div class="font-semibold">{{ $log->tahap_persetujuan ?? 'N/A'}}</div>

                            <div class="font-normal pl-4">Status Persetujuan</div>
                            <div class="font-semibold">
                                @if($log->status_pengajuan == 'ditolak')
                                    <span class="text-red-600">Ditolak</span>
                                @elseif($log->status_pengajuan == 'disetujui')
                                    <span class="text-green-600">Disetujui</span>
                                @else
                                    <span class="text-gray-600">Menunggu</span>
                                @endif
                            </div>

                            <div class="font-normal pl-4">Catatan</div>
                            <div>{{ $log->komentar ?? 'N/A' }}</div>
                            <div style="grid-column: 1 / span 2; padding-top: 10px;"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endif
@else
    <p class="text-sm mt-6">Belum ada riwayat persetujuan yang tersedia.</p>
@endif

<!-- Paragraf Penutup -->
<p class="text-sm mt-6 mb-8">Demikian surat perintah kerja lembur ini dibuat untuk dilaksanakan sebagaimana mestinya.</p>

{{-- Bagian Tanda Tangan (Web & PDF View) --}}
{{-- Gunakan div bersih untuk memastikan penempatan yang konsisten --}}
<div class="text-left">
    <p class="text-sm mb-4">Hormat saya,</p>

    {{-- Placeholder QR Code --}}
    @if(isset($is_pdf) && $is_pdf)
        {{-- Di PDF, lebih baik menggunakan margin-right langsung --}}
        <div style="width: 100px; height: 100px; margin-right: 0px;">
            {{-- Logika render QR Code Anda di sini --}}
            <div class="w-24 h-24 bg-gray-300 my-4"></div>
        </div>
    @else
        {{-- Di Web, gunakan kelas Tailwind --}}
        <div class="flex justify-start">
            {{-- Logika render QR Code Anda di sini --}}
            <div class="w-24 h-24 bg-gray-300 my-4"></div>
        </div>
    @endif

    <p class="text-sm font-semibold mt-4">{{ $lembur->pegawai->nama ?? '[Nama Lengkap Anda]' }}</p>
    <p class="text-sm font-semibold">{{ $lembur->pegawai->pekerjaan?->jabatan ?? 'Pegawai' }}</p>
</div>

{{-- FOOTER KHUSUS WEB (DIPINDAHKAN KE DALAM content-wrap) --}}
@if(!isset($is_pdf) || !$is_pdf)
    <div class="mt-12">
        <table class="w-full h-10 border-collapse">
            <tr>
                <td style="background-color: #0000FF; width: 64%; vertical-align: middle; padding: 0 15px;">
                    <p class="text-white text-xs m-0">PERUMDA BPR BANK KOTA BOGOR</p>
                </td>
                <td style="background-color: #FFFFFF; width: 1%;"> &nbsp; </td>
                <td style="background-color: #FF0000; width: 15%;"> &nbsp; </td>
                <td style="background-color: #FFFFFF; width: 1%;"> &nbsp; </td>
                <td style="background-color: #FF0000; width: 10%;"> &nbsp; </td>
            </tr>
        </table>
    </div>
@endif

</div> {{-- Penutup content-wrap --}}

{{-- FOOTER BAWAH HALAMAN (Menempel di tepi kertas UNTUK PDF SAJA) --}}
@if(isset($is_pdf) && $is_pdf)
    <table style="width: 100%; height: 30pt; border-collapse: collapse; position: absolute; bottom: 0; left: 0; right: 0;">
        <tr>
            <td style="background-color: #0000FF; width: 64%; vertical-align: middle; padding: 0 15pt; height: 30pt;">
                <p style="color: white; font-size: 10pt; margin: 0;">PERUMDA BPR BANK KOTA BOGOR</p>
            </td>
            <td style="background-color: #FFFFFF; width: 1%; height: 30pt;">&nbsp;</td>
            <td style="background-color: #FF0000; width: 15%; height: 30pt;">&nbsp;</td>
            <td style="background-color: #FFFFFF; width: 1%; height: 30pt;">&nbsp;</td>
            <td style="background-color: #FF0000; width: 10%; height: 30pt;">&nbsp;</td>
        </tr>
    </table>
@endif
