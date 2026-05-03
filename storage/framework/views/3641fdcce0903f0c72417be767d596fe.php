

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

<?php if(isset($is_pdf) && $is_pdf): ?>
<style>
    @page { margin: 15pt !important; }
    body { font-family: "DejaVu Sans", sans-serif; margin: 0; padding: 0; }
    .content-wrap { padding: 0; margin: 0; }
</style>
<?php endif; ?>

<div class="content-wrap">
    <!-- HEADER SECTION -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
        <?php
            $path = public_path('images/logobkb.png');
            $base64 = file_exists($path) ? 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path)) : null;
        ?>
        <?php if($base64): ?> <img src="<?php echo e($base64); ?>" style="height: 40px;"> <?php endif; ?>
        
    </div>

    

    <div class="spacer"></div>

    <!-- I. DATA PEGAWAI -->
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr>
                <th colspan="2" class="bg-gray-form" style="text-align: left; width: 50%;">I. DATA PEGAWAI</th>
                <th colspan="2" class="bg-gray-form" style="text-align: left; width: 50%;">Nomor : <?php echo e($cuti->nomor_surat ?? '.../SDM-CUTI/IV/2026'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 15%;">Nama</td><td style="width: 35%;" class="font-bold"><?php echo e($cuti->pegawai->nama ?? (auth()->user()->pegawai->nama ?? '-')); ?></td>
                <td style="width: 15%;">Jabatan</td><td style="width: 35%;" class="font-bold"><?php echo e($cuti->pegawai->pekerjaan->jabatan ?? (auth()->user()->pegawai->pekerjaan->jabatan ?? '-')); ?></td>
            </tr>
            <tr>
                <td>NUP</td><td class="font-bold"><?php echo e($cuti->nomor_urut_pegawai ?? (auth()->user()->nomor_urut_pegawai ?? '-')); ?></td>
                <td>Unit Kerja</td><td class="font-bold"><?php echo e($cuti->pegawai->pekerjaan->divisi->nama_divisi ?? (auth()->user()->pegawai->pekerjaan->divisi->nama_divisi ?? '-')); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="spacer"></div>

    <!-- II. JENIS CUTI & III. PERIODE (Satu Tabel Solid) -->
    <table class="form-table" style="margin-top: 0;">
        <!-- KUNCINYA: Setting lebar kolom di baris paling atas agar Web & PDF lurus -->
        <thead>
            <tr style="height: 0; line-height: 0; border: none;">
                <td style="width: 35%; border: none !important; padding: 0;"></td> 
                <td style="width: 5%; border: none !important; padding: 0;"></td>  
                <td style="width: 10%; border: none !important; padding: 0;"></td> 
                <td style="width: 9%; border: none !important; padding: 0;"></td>  
                <td style="width: 8%; border: none !important; padding: 0;"></td>  
                <td style="width: 12%; border: none !important; padding: 0;"></td> 
                <td style="width: 23%; border: none !important; padding: 0;"></td> 
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
    <?php
        $list = [
            ['nama' => 'Cuti Tahunan', 'kuota' => '12 Hari'], 
            ['nama' => 'Cuti Besar', 'kuota' => '2 Bulan'], 
            ['nama' => 'Cuti Menikah', 'kuota' => '5 Hari'], 
            ['nama' => 'Cuti Melahirkan', 'kuota' => '3 Bulan'], 
            ['nama' => 'Cuti Sakit', 'kuota' => '3 x'], 
            ['nama' => 'Cuti Hari Raya Keagamaan', 'kuota' => '-'], 
            ['nama' => 'Cuti Menunaikan Ibadah Keagamaan', 'kuota' => '14 Hari'], 
            ['nama' => 'Cuti Alasan Penting dan Mendesak', 'kuota' => '2 Hari'], 
            ['nama' => 'Izin Tidak Masuk Kerja', 'kuota' => '-']
        ];

        // Ambil data jika sudah ada di DB (untuk mode view/lacak)
        $jenisCutiDb = $cuti->jenis_cuti ?? '';
    ?>

    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $isRowSelected = (isset($cuti) && $cuti->jenis_cuti == $item['nama']);
    ?>
    <tr>
        
        <td style="white-space: nowrap;"><?php echo e($i+1); ?>. <?php echo e($item['nama']); ?></td>
        
        
        <td class="text-center">
            <span class="checkmark" id="v_<?php echo e(Str::slug($item['nama'], '_')); ?>">
                <?php echo $isRowSelected ? '✓' : ''; ?>

            </span>
        </td>

        <td class="text-center"><?php echo e($item['kuota']); ?></td>

        
<td class="text-center review-lama-cuti" id="lama_<?php echo e(Str::slug($item['nama'], '_')); ?>">
    <?php echo e((isset($cuti) && $cuti->jenis_cuti == $item['nama']) ? $cuti->jumlah_cuti . ' Hari' : ''); ?>

</td>


<td class="text-center review-sisa-cuti" id="sisa_<?php echo e(Str::slug($item['nama'], '_')); ?>">
    <?php echo e((isset($cuti) && $cuti->jenis_cuti == $item['nama']) ? ($cuti->saldo_akhir ?? $cuti->sisa_cuti) . ' Hari' : ''); ?>

</td>


        
        <?php if($i == 0): ?>
            <td>1. Tgl Pengajuan</td>
            <td id="review_tgl_pengajuan"><?php echo e((isset($cuti) && $cuti->created_at) ? \Carbon\Carbon::parse($cuti->created_at)->format('d/m/Y') : date('d/m/Y')); ?></td>
        <?php elseif($i == 1): ?>
            <td>2. Lama Cuti</td>
            <td><span id="review_jumlah_cuti_display"><?php echo e($cuti->jumlah_cuti ?? '0'); ?></span> Hari</td>
        <?php elseif($i == 2): ?>
            <td>3. TMT Cuti</td>
            <td id="review_tmt_cuti_display" style="font-size: 7.5pt;">
                <?php echo e((isset($cuti) && $cuti->tanggal_mulai) ? \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y').' s/d '.\Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') : '... s/d ...'); ?>

            </td>
        <?php elseif($i == 3): ?>
            <td colspan="2" class="bg-blue-grey font-bold">IV. ALASAN CUTI</td>
        <?php elseif($i == 4): ?>
            <td colspan="2" rowspan="2" id="review_alasan_cuti_display" style="height: 35px; vertical-align: top;"><?php echo e($cuti->keterangan ?? '-'); ?></td>
        <?php elseif($i == 6): ?>
            <td colspan="2" class="bg-blue-grey font-bold">V. YANG MENGAJUKAN</td>
        <?php elseif($i == 7): ?>
            <td colspan="2" rowspan="2" class="text-center" style="vertical-align: top; padding-top: 5px;">
                <p style="margin: 0; font-size: 8pt;">Hormat Saya,</p>
                <div style="height: 25px;"></div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                    <?php echo e($cuti->pegawai->nama ?? (auth()->user()->pegawai->nama ?? '-')); ?>

                </p>
            </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>

    </table>

    <div class="spacer"></div>

    <!-- VI & VII: PERTIMBANGAN & KEPUTUSAN -->
    <?php $__currentLoopData = [['title' => 'VI. PERTIMBANGAN ATASAN LANGSUNG**', 'nama' => $namaAtasan, 'jab' => $jabatanAtasan], ['title' => 'VII. KEPUTUSAN PEJABAT BERWENANG**', 'nama' => $namaDireksi, 'jab' => $jabatanDireksi]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr><th colspan="8" class="bg-blue-grey" style="text-align: left;"><?php echo e($p['title']); ?></th></tr>
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
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 9pt;"><?php echo e($p['nama']); ?></p>
                    <p style="margin: 0; font-size: 8pt;"><?php echo e($p['jab']); ?></p>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="spacer"></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;"><?php echo e($namaVerif1); ?></p>
                    <p style="margin: 0; font-size: 7.5pt;"><?php echo e($jabatanVerif1); ?></p>
                </td>
                <td class="text-center" style="vertical-align: bottom; width: 25%; padding-bottom: 12px;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;"><?php echo e($namaVerif2); ?></p>
                    <p style="margin: 0; font-size: 7.5pt;"><?php echo e($jabatanVerif2); ?></p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/partials/letter_content.blade.php ENDPATH**/ ?>