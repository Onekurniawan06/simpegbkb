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
    <p>Bogor, {{ (isset($pangkatgajitunjangan) && $pangkatgajitunjangan->created_at) ? \Carbon\Carbon::parse($pangkatgajitunjangan->created_at)->locale('id')->translatedFormat('d F Y') : \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
</div>

<!-- Salutation -->
<p class="{{ (isset($is_pdf) && $is_pdf) ? '' : 'mb-4 text-sm' }}">Dengan hormat,</p>

{{-- BAGIAN KONTEN SURAT --}}
@php
    $formatDate = function($date) {
        if (!$date) return '';
        // Set locale ke Indonesia agar nama bulan dalam bahasa Indonesia
        \Carbon\Carbon::setLocale('id');
        return \Carbon\Carbon::parse($date)->translatedFormat('d F Y');
    };
@endphp

<div class="text-sm mb-4">
    <p class="mb-2">Saya yang bertanda tangan di bawah ini:</p>

    <table style="width: 100%; border-collapse: collapse; margin-left: 15px;">
        <tr>
            <td style="width: 25%; padding: 2px 0;">NUP Pegawai</td>
            <td style="width: 1%; padding: 2px 0;">:</td>
            <td style="width: 74%; padding: 2px 0;"><strong id="review_nup">{{ $pangkatgajitunjangan->pegawai->nomor_urut_pegawai ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Nama Pegawai</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;"><strong id="review_nama_pegawai">{{ $pangkatgajitunjangan->pegawai->nama ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Divisi</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;"><strong id="review_unit_kerja">{{ $pangkatgajitunjangan->pegawai->pekerjaan->divisi->nama_divisi ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Jabatan</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;"><strong id="review_jabatan">{{ $pangkatgajitunjangan->pegawai->pekerjaan->jabatan ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Pangkat/Grade</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;">
                <strong id="review_pangkat">{{ $pangkatgajitunjangan->pegawai->pekerjaan->pangkat ?? '' }}</strong> /
                <strong id="review_grade">{{ $pangkatgajitunjangan->pegawai->pekerjaan->grade ?? '' }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Status Pegawai</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;"><strong id="review_status_pegawai">{{ $pangkatgajitunjangan->pegawai->pekerjaan->status_pegawai ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">TMT Pegawai</td>
            <td style="padding: 2px 0;">:</td>
            {{-- FIX: Proteksi agar tidak meledak di Baris 133 --}}
            <td style="padding: 2px 0;"><strong id="review_tmt_pegawai">{{ isset($pangkatgajitunjangan) ? $formatDate($pangkatgajitunjangan->tmt_pegawai) : '' }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Lama Bergabung</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;"><strong id="review_masa_kerja">{{ $pangkatgajitunjangan->masa_kerja ?? '' }}</strong></td>
        </tr>
    </table>

    <p class="mt-4">Mengajukan permohonan Kenaikan sebagai berikut,</p>

    <table style="width: 100%; border-collapse: collapse; margin-left: 15px;">
        <tr>
            <td style="width: 25%; padding: 2px 0;">Jenis Pengajuan Kenaikan</td>
            <td style="width: 1%; padding: 2px 0;">:</td>
            <td style="width: 74%; padding: 2px 0;"><strong id="review_jenis_pengajuan">{{ $pangkatgajitunjangan->jenis_pengajuan ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Pangkat Tujuan</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;"><strong id="review_pangkat_tujuan">{{ $pangkatgajitunjangan->pangkat_tujuan ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 2px 0;">Grade Tujuan</td>
            <td style="padding: 2px 0;">:</td>
            <td style="padding: 2px 0;"><strong id="review_grade_tujuan">{{ $pangkatgajitunjangan->grade_tujuan ?? '' }}</strong></td>
        </tr>
    </table>

    <p class="mt-4 mb-2">Sebagai bahan pertimbangan atas permohonan tersebut saya lampirkan dokumen persyaratan administratif sebagai berikut:</p>
</div>

{{-- Tabel Lampiran Dokumen --}}
<div class="mb-4">
    <table style="width: 100%; border-collapse: collapse;" class="text-sm">
        <thead>
            <tr>
                <th style="border: 1px solid black; padding: 8px; text-align: center; width: 5%;">No</th>
                <th style="border: 1px solid black; padding: 8px; text-align: left; width: 15%;">Dokumen Persyaratan</th>
                <th style="border: 1px solid black; padding: 8px; text-align: left; width: 80%;">Nama Dokumen</th>
            </tr>
        </thead>
        <tbody>
            {{-- FIX: Pakai @if(isset) agar tidak error forelse --}}
            @if(isset($pangkatgajitunjangan) && isset($pangkatgajitunjangan->files) && $pangkatgajitunjangan->files->count() > 0)
                @foreach($pangkatgajitunjangan->files as $index => $file)
                    <tr>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid black; padding: 8px; text-align: left;">{{ $file->tipe_dokumen }}</td>
                        <td style="border: 1px solid black; padding: 8px;">{{ $file->nama_file_asli }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;" colspan="3">-- Belum ada dokumen yang dilampirkan --</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

{{-- Paragraf Penutup --}}
<div class="{{ (isset($is_pdf) && $is_pdf) ? 'text-sm mb-8' : 'text-sm mb-8' }}">
    <p>Demikian surat permohonan ini saya buat dengan harapan dapat diproses sebagaimana mestinya. Atas perhatian dan kerja sama Bapak/Ibu, saya ucapkan terima kasih.</p>
</div>
{{-- AKHIR BAGIAN KONTEN SURAT --}}

<!-- Approvals/Signatures Section -->
{{-- FIX: Gunakan optional() dan isset() agar tidak Error pas Preview --}}
@if(isset($pangkatgajitunjangan) && optional($pangkatgajitunjangan->logPersetujuanPangkatgajitunjangan)->count() > 0)
    @php
        $hanyaPengajuanAwal = $pangkatgajitunjangan->logPersetujuanPangkatgajitunjangan->every(function($log) {
            return $log->tahap_persetujuan === 'Pengajuan Awal' || $log->tahap_persetujuan === 'Pegawai';
        });
    @endphp

    @if($hanyaPengajuanAwal)
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-6">
            <p class="text-sm text-blue-700">
                <strong>Informasi:</strong> Pengajuan Pangkat, Gaji dan Tunjangan telah diterima dan sedang dalam proses verifikasi berkas awal.
            </p>
        </div>
    @else
        <p class="text-sm mt-6 mb-2">Dengan beberapa persetujuan yaitu,</p>

        @if(isset($is_pdf) && $is_pdf)
            {{-- PDF View --}}
            <table class="main-detail-table">
                @php $approvalCounter = 1; @endphp
                @foreach ($pangkatgajitunjangan->logPersetujuanPangkatgajitunjangan as $log)
                    @if ($log->tahap_persetujuan !== 'Pengajuan Awal' && $log->tahap_persetujuan !== 'Pegawai')
                        <tr>
                            <td class="label-column">{{ $approvalCounter++ }}. Tahap Persetujuan</td>
                            <td class="data-column">{{ $log->tahap_persetujuan ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="label-column" style="padding-left: 15pt;"><span style="padding-left: 8pt;">Status Persetujuan</span></td>
                            <td class="data-column">
                                @if($log->status_persetujuan == 'ditolak')
                                    <span style="color: #FF0000;">Ditolak</span>
                                @elseif($log->status_persetujuan == 'disetujui')
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
                    @foreach ($pangkatgajitunjangan->logPersetujuanPangkatgajitunjangan as $log)
                        @if ($log->tahap_persetujuan !== 'Pengajuan Awal' && $log->tahap_persetujuan !== 'Pegawai')
                            <div class="font-normal">{{ $approvalCounter++ }}. Tahap Persetujuan</div>
                            <div class="font-semibold">{{ $log->tahap_persetujuan ?? 'N/A'}}</div>

                            <div class="font-normal pl-4">Status Persetujuan</div>
                            <div class="font-semibold">
                                @if($log->status_persetujuan == 'ditolak')
                                    <span class="text-red-600">Ditolak</span>
                                @elseif($log->status_persetujuan == 'disetujui')
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
    {{-- Tampilan pas Preview (Karena data log belum ada di DB) --}}
    <p class="text-sm mt-6 italic text-gray-500">Draft Pengajuan: Riwayat persetujuan akan muncul setelah pengajuan diproses.</p>
@endif

{{-- Bagian Tanda Tangan (Web & PDF View) --}}
{{-- Gunakan div bersih untuk memastikan penempatan yang konsisten --}}
<div class="text-left mt-4">
    <p class="text-sm mb-4">Hormat saya,</p>

    {{-- Placeholder QR Code --}}
    @if(isset($is_pdf) && $is_pdf)
        <div style="width: 100px; height: 100px; margin-right: 0px;">
            <div style="width: 96px; height: 96px; background-color: #e2e8f0; margin-top: 1rem; margin-bottom: 1rem;"></div>
        </div>
    @else
        <div class="flex justify-start">
             <div class="w-24 h-24 bg-gray-300 my-4"></div>
        </div>
    @endif

    {{-- ID review_nama_pegawai_footer & review_jabatan_footer agar JS bisa akses --}}
    <p id="review_nama_pegawai_footer" class="text-sm font-semibold mt-4">
        {{ $pangkatgajitunjangan->pegawai->nama ?? '[Nama Lengkap Anda]' }}
    </p>
    <p id="review_jabatan_footer" class="text-sm font-semibold">
        {{ $pangkatgajitunjangan->pegawai->pekerjaan?->jabatan ?? 'Pegawai' }}
    </p>
</div>

{{-- FOOTER KHUSUS WEB --}}
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

{{-- FOOTER BAWAH HALAMAN (KHUSUS PDF) --}}
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

