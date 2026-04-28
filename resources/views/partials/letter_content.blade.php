{{-- resources/views/partials/letter_content.blade.php --}}

<style>
    /* CSS TOTAL PROTECTION - BIAR WEB & PDF SINKRON */
    .form-table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 8pt; background-color: white; }
    .form-table th, .form-table td { border: 1px solid #000 !important; padding: 4px 6px; vertical-align: middle; font-size: 8pt; color: black; line-height: 1.2; }
    .bg-gray-form { background-color: #cbd5e1 !important; -webkit-print-color-adjust: exact; }
    .bg-blue-grey { background-color: #b9c5d5 !important; -webkit-print-color-adjust: exact; }
    .text-center { text-align: center; }
    .font-bold { font-weight: bold; }
    .checkmark { font-family: "DejaVu Sans", sans-serif; font-weight: bold; font-size: 10pt; color: #1e40af; }
    .spacer { height: 12px; }
    .list-ketentuan {
        margin: 0;
        padding-left: 15px !important; /* Tambah padding agar angka tidak tertutup garis */
        list-style-type: decimal !important;
    }
    .list-ketentuan li {
        display: list-item !important; /* Paksa agar muncul sebagai list */
        margin-bottom: 4px;
        font-size: 8pt;
    }
</style>

@if(isset($is_pdf) && $is_pdf)
<style>
    @page { margin: 15pt !important; }
    body { font-family: "DejaVu Sans", sans-serif; margin: 0; padding: 0; }
    .content-wrap { padding: 0; margin: 0; }
</style>
@endif

<div class="content-wrap">
    <!-- HEADER SECTION -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
        @php
            $path = public_path('images/logobkb.png');
            $base64 = file_exists($path) ? 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path)) : null;
        @endphp
        @if($base64) <img src="{{ $base64 }}" style="height: 40px;"> @endif
        {{-- <p style="font-weight: bold; margin: 0; font-size: 10pt;">Perumda BPR Bank Kota Bogor</p> --}}
    </div>

    {{-- <div style="text-align: right; font-size: 9pt; margin-bottom: 5px;">
        <p>Bogor, {{ (isset($cuti) && $cuti->created_at) ? \Carbon\Carbon::parse($cuti->created_at)->format('d/m/Y') : date('d/m/Y') }}</p>
    </div> --}}

    <div class="spacer"></div>

    <!-- I. DATA PEGAWAI -->
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr>
                <th colspan="2" class="bg-gray-form" style="text-align: left; width: 50%;">I. DATA PEGAWAI</th>
                <th colspan="2" class="bg-gray-form" style="text-align: left; width: 50%;">Nomor : {{ $cuti->nomor_surat ?? '.../SDM-CUTI/IV/2026' }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 15%;">Nama</td><td style="width: 35%;" class="font-bold">{{ $cuti->pegawai->nama ?? (auth()->user()->pegawai->nama ?? '-') }}</td>
                <td style="width: 15%;">Jabatan</td><td style="width: 35%;" class="font-bold">{{ $cuti->pegawai->pekerjaan->jabatan ?? (auth()->user()->pegawai->pekerjaan->jabatan ?? '-') }}</td>
            </tr>
            <tr>
                <td>NUP</td><td class="font-bold">{{ $cuti->nomor_urut_pegawai ?? (auth()->user()->nomor_urut_pegawai ?? '-') }}</td>
                <td>Unit Kerja</td><td class="font-bold">{{ $cuti->pegawai->pekerjaan->divisi->nama_divisi ?? (auth()->user()->pegawai->pekerjaan->divisi->nama_divisi ?? '-') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="spacer"></div>

    <!-- II. JENIS CUTI & III. PERIODE (Satu Tabel Solid) -->
    <table class="form-table" style="margin-top: 0;">
        <!-- KUNCINYA: Setting lebar kolom di baris paling atas agar Web & PDF lurus -->
        <thead>
            <tr style="height: 0; line-height: 0; border: none;">
                <td style="width: 35%; border: none !important; padding: 0;"></td> {{-- 1. Jenis --}}
                <td style="width: 5%; border: none !important; padding: 0;"></td>  {{-- 2. ✔ --}}
                <td style="width: 10%; border: none !important; padding: 0;"></td> {{-- 3. Kuota --}}
                <td style="width: 9%; border: none !important; padding: 0;"></td>  {{-- 4. Lama Cuti --}}
                <td style="width: 8%; border: none !important; padding: 0;"></td>  {{-- 5. Sisa Cuti --}}
                <td style="width: 12%; border: none !important; padding: 0;"></td> {{-- 6. Label Kanan --}}
                <td style="width: 23%; border: none !important; padding: 0;"></td> {{-- 7. Data Kanan --}}
            </tr>
            <tr>
                <th colspan="5" class="bg-blue-grey" style="text-align: left;">II. JENIS CUTI YANG DIAMBIL*</th>
                <th colspan="2" class="bg-blue-grey" style="text-align: left;">III. PERIODE CUTI</th>
            </tr>
            <tr class="text-center font-bold">
                <td>Jenis</td><td>(<span class="checkmark">&#10003;</span>)</td><td>Kuota</td><td>Lama Cuti</td><td>Sisa Cuti</td><td colspan="2" style="text-align: left; padding-left: 5px;">Detail Periode</td>
            </tr>
        </thead>
        <tbody>
            @php
                $list = [['nama' => 'Cuti Tahunan', 'kuota' => '12 Hari'], ['nama' => 'Cuti Besar', 'kuota' => '2 Bulan'], ['nama' => 'Cuti Menikah', 'kuota' => '5 Hari'], ['nama' => 'Cuti Melahirkan', 'kuota' => '3 Bulan'], ['nama' => 'Cuti Sakit', 'kuota' => '3 x'], ['nama' => 'Cuti Hari Raya Keagamaan', 'kuota' => '-'], ['nama' => 'Cuti Menunaikan Ibadah Keagamaan', 'kuota' => '14 Hari'], ['nama' => 'Cuti Alasan Penting dan Mendesak', 'kuota' => '2 Hari'], ['nama' => 'Izin Tidak Masuk Kerja', 'kuota' => '-']];
            @endphp
            @foreach($list as $i => $item)
            <tr>
                {{-- KIRI: DATA JENIS CUTI --}}
                <td style="white-space: nowrap;">{{ $i+1 }}. {{ $item['nama'] }}</td>
                <td class="text-center"><span class="checkmark" id="v_{{ Str::slug($item['nama'], '_') }}">{!! (isset($cuti) && $cuti->jenis_cuti == $item['nama']) ? '&#10003;' : '' !!}</span></td>
                <td class="text-center">{{ $item['kuota'] }}</td>
                {{-- Kolom Lama Cuti --}}
                <td class="text-center" id="review_cuti_diambil_display">
                    {{ (isset($cuti) && $cuti->jenis_cuti == $item['nama'] && $cuti->jumlah_cuti) ? $cuti->jumlah_cuti . ' Hari' : '' }}
                </td>

                {{-- Kolom Sisa Cuti --}}
                <td class="text-center" id="review_sisa_cuti_display">
                    {{ ($i == 0 && isset($cuti) && $cuti->sisa_cuti !== null) ? $cuti->sisa_cuti . ' Hari' : '' }}
                </td>

                {{-- KANAN: MAPPING BARIS AGAR GARIS LURUS --}}
                @if($i == 0)
                    <td>1. Tgl Pengajuan</td><td>{{ (isset($cuti) && $cuti->created_at) ? \Carbon\Carbon::parse($cuti->created_at)->format('d/m/Y') : date('d/m/Y') }}</td>
                @elseif($i == 1)
                    <td>2. Lama Cuti</td><td><span id="review_jumlah_cuti_display">{{ $cuti->jumlah_cuti ?? '0' }}</span> Hari</td>
                @elseif($i == 2)
                    <td>3. TMT Cuti</td><td id="review_tmt_cuti_display" style="font-size: 7.5pt;">{{ (isset($cuti) && $cuti->tanggal_mulai) ? \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y').' s/d '.\Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') : '... s/d ...' }}</td>
                @elseif($i == 3)
                    <td colspan="2" class="bg-blue-grey font-bold">IV. ALASAN CUTI</td>
                @elseif($i == 4)
                    <td colspan="2" rowspan="2" id="review_alasan_cuti_display" style="height: 35px; vertical-align: top;">{{ $cuti->keterangan ?? '-' }}</td>
                @elseif($i == 6)
                    <td colspan="2" class="bg-blue-grey font-bold">V. YANG MENGAJUKAN</td>
                @elseif($i == 7)
                    <td colspan="2" rowspan="2" class="text-center" style="vertical-align: top; padding-top: 5px;">
                        <p style="margin: 0; font-size: 8pt;">Hormat Saya,</p><div style="height: 25px;"></div>
                        <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $cuti->pegawai->nama ?? (auth()->user()->pegawai->nama ?? '-') }}</p>
                    </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="spacer"></div>

    <!-- VI & VII: PERTIMBANGAN & KEPUTUSAN -->
    @foreach([['title' => 'VI. PERTIMBANGAN ATASAN LANGSUNG**', 'nama' => $namaAtasan, 'jab' => $jabatanAtasan], ['title' => 'VII. KEPUTUSAN PEJABAT BERWENANG**', 'nama' => $namaDireksi, 'jab' => $jabatanDireksi]] as $p)
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr><th colspan="8" class="bg-blue-grey" style="text-align: left;">{{ $p['title'] }}</th></tr>
            <tr class="text-center" style="font-size: 7pt;">
                <td style="width: 15%;">DISETUJUI</td><td style="width: 10%;">....</td><td style="width: 15%;">PERUBAHAN</td><td style="width: 10%;">....</td>
                <td style="width: 15%;">DITANGGUHKAN</td><td style="width: 10%;">....</td><td style="width: 15%;">TIDAK DISETUJUI</td><td style="width: 10%;">....</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" style="height: 100px; vertical-align: top; padding: 10px; width: 50%;">
                    <p style="text-decoration: underline; margin-bottom: 5px;">Pertimbangan/Catatan/Rekomendasi:</p>
                </td>
                <td colspan="4" class="text-center" style="vertical-align: bottom; padding-bottom: 15px; width: 50%;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 9pt;">{{ $p['nama'] }}</p>
                    <p style="margin: 0; font-size: 8pt;">{{ $p['jab'] }}</p>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="spacer"></div>
    @endforeach

    <!-- VIII: KETENTUAN -->
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr><th class="bg-blue-grey" style="text-align: left;">VIII. KETENTUAN CUTI DISETUJUI</th></tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 10px;">
                    <ol class="list-ketentuan">
                        <li>Segala pekerjaan yang menjadi tugasnya telah diselesaikan sebelum menjalankan cuti.</li>
                        <li>Yang bersangkutan wajib hadir apabila sewaktu-waktu dipanggil masuk kerja apabila dibutuhkan.</li>
                        <li>Izin tidak masuk kerja apabila hak cuti tahunannya telah habis dikenakan pengurangan penghasilan secara proposional.</li>
                        <li>Lembar permohonan dan persetujuan cuti sebelumnya dilampirkan kembali saat pengajuan berikutnya.</li>
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="spacer"></div>

    <!-- IX: VERIFIKATOR -->
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr><th style="width: 50%; text-align: left;" class="bg-blue-grey">IX. KETENTUAN TAMBAHAN</th><th colspan="2" class="bg-blue-grey text-center">VERIFIKATOR</th></tr>
        </thead>
        <tbody>
            <tr>
                <td style="height: 80px; font-size: 7.5pt; vertical-align: bottom; padding: 10px;">
                    <p style="margin: 0;">* Beri tanda centang (<span class="checkmark">&#10003;</span>).</p>
                    <p style="margin: 0;">** Beri tanda centang (<span class="checkmark">&#10003;</span>) dan alasan.</p>
                </td>
                <td class="text-center" style="vertical-align: bottom; width: 25%; padding-bottom: 12px;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $namaVerif1 }}</p>
                    <p style="margin: 0; font-size: 7.5pt;">{{ $jabatanVerif1 }}</p>
                </td>
                <td class="text-center" style="vertical-align: bottom; width: 25%; padding-bottom: 12px;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $namaVerif2 }}</p>
                    <p style="margin: 0; font-size: 7.5pt;">{{ $jabatanVerif2 }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
