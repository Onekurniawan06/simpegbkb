

<style>
    .form-table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 10pt; background-color: white; border: 1px solid #000; }
    .form-table th, .form-table td { border: 1px solid #000 !important; padding: 4px 6px; vertical-align: top; font-size: 8.5pt; color: black; }
    .bg-gray-form { background-color: #cbd5e1 !important; -webkit-print-color-adjust: exact; font-weight: bold; }
    .bg-blue-grey { background-color: #b9c5d5 !important; -webkit-print-color-adjust: exact; font-weight: bold; }
    .text-center { text-align: center; }
    .font-bold { font-weight: bold; }
    .checkmark { font-family: "DejaVu Sans", sans-serif; font-weight: bold; font-size: 10pt; color: #1e40af; }
    .spacer { height: 10px; }
    ul.ketentuan { margin: 0; padding-left: 15px; list-style-type: decimal; }
    ul.ketentuan li { margin-bottom: 2px; }
</style>

<?php if(isset($is_pdf) && $is_pdf): ?>
<style>
    @page { margin: 0.5cm; }
    body { font-family: "DejaVu Sans", sans-serif; }
    .content-wrap { padding: 15pt 25pt; }
</style>
<?php endif; ?>

<div class="content-wrap">
    <!-- HEADER LOGO -->
    <?php if(isset($is_pdf) && $is_pdf): ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 5pt;">
            <tr>
                <td style="width: 50%; border: none; padding: 0;">
                    <?php
                        $path = public_path('images/logobkb.png');
                        $base64 = file_exists($path) ? 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path)) : '';
                    ?>
                    <?php if($base64): ?> < img src="<?php echo e($base64); ?>" style="height: 35px;"> <?php endif; ?>
                </td>
                <td style="text-align: right; border: none; padding: 0; font-weight: bold; font-size: 9pt;">Perumda BPR Bank Kota Bogor</td>
            </tr>
        </table>
    <?php else: ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <img src="<?php echo e(asset('images/logobkb.png')); ?>" style="height: 35px;">
            <p style="font-weight: bold; font-size: 9pt; margin: 0;">Perumda BPR Bank Kota Bogor</p>
        </div>
    <?php endif; ?>

    

    <!-- I. DATA PEGAWAI -->
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr>
                <th colspan="2" class="bg-gray-form" style="text-align: left; width: 50%;">I. DATA PEGAWAI</th>
                <th colspan="2" class="bg-gray-form" style="text-align: left; width: 50%;">Nomor Surat : </th>
                
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 15%;">Nama</td>
                <td style="width: 35%; font-weight: bold;"><?php echo e($cuti->pegawai->nama ?? (auth()->user()->pegawai->nama ?? '-')); ?></td>
                <td style="width: 15%;">Jabatan</td>
                <td style="width: 35%; font-weight: bold;"><?php echo e($cuti->pegawai->pekerjaan->jabatan ?? (auth()->user()->pegawai->pekerjaan->jabatan ?? '-')); ?></td>
            </tr>
            <tr>
                <td>NUP</td>
                <td style="font-weight: bold;"><?php echo e($cuti->nomor_urut_pegawai ?? (auth()->user()->nomor_urut_pegawai ?? '-')); ?></td>
                <td>Unit Kerja</td>
                <td style="font-weight: bold;"><?php echo e($cuti->pegawai->pekerjaan->divisi->nama_divisi ?? (auth()->user()->pegawai->pekerjaan->divisi->nama_divisi ?? '-')); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="spacer"></div>

    <!-- SECTION II - V -->
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr style="height: 0; line-height: 0;">
                <td style="width: 32%; border: none; padding: 0;"></td>
                <td style="width: 5%; border: none; padding: 0;"></td>
                <td style="width: 10%; border: none; padding: 0;"></td>
                <td style="width: 9%; border: none; padding: 0;"></td>
                <td style="width: 8%; border: none; padding: 0;"></td>
                <td style="width: 14%; border: none; padding: 0;"></td>
                <td style="width: 22%; border: none; padding: 0;"></td>
            </tr>
            <tr>
                <th colspan="5" class="bg-blue-grey" style="text-align: left;">II. JENIS CUTI YANG DIAMBIL*</th>
                <th colspan="2" class="bg-blue-grey" style="text-align: left;">III. PERIODE CUTI</th>
            </tr>
            <tr class="text-center font-bold" style="font-size: 8pt;">
                <td>Jenis</td>
                <td><span class="checkmark">&#10003;</span></td>
                <td>Kuota</td>
                <td>Lama Cuti</td>
                <td>Sisa Cuti</td>
                <td colspan="2" style="text-align: left; padding-left: 5px;">Detail Periode</td>
            </tr>
        </thead>
        <tbody>
            <?php
                $list = [
                    ['nama' => 'Cuti Tahunan', 'kuota' => '12 Hari'], ['nama' => 'Cuti Besar', 'kuota' => '2 Bulan'],
                    ['nama' => 'Cuti Menikah', 'kuota' => '5 Hari'], ['nama' => 'Cuti Melahirkan', 'kuota' => '3 Bulan'],
                    ['nama' => 'Cuti Sakit', 'kuota' => '3 x'], ['nama' => 'Cuti Hari Raya Keagamaan', 'kuota' => '-'],
                    ['nama' => 'Cuti Menunaikan Ibadah Keagamaan', 'kuota' => '14 Hari'],
                    ['nama' => 'Cuti Alasan Penting dan Mendesak', 'kuota' => '2 Hari'], ['nama' => 'Izin Tidak Masuk Kerja', 'kuota' => '-'],
                ];
            ?>
            <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="white-space: nowrap;"><?php echo e($i+1); ?>. <?php echo e($item['nama']); ?></td>
                <td class="text-center"><span class="checkmark" id="v_<?php echo e(Str::slug($item['nama'], '_')); ?>"><?php echo (isset($cuti) && $cuti->jenis_cuti == $item['nama']) ? '&#10003;' : ''; ?></span></td>
                <td class="text-center"><?php echo e($item['kuota']); ?></td>
                <td class="text-center" id="review_cuti_diambil_display"><?php echo e(($i == 0 && isset($cuti)) ? $cuti->cuti_diambil : ''); ?></td>
                <td class="text-center" id="review_sisa_cuti_display"><?php echo e(($i == 0 && isset($cuti)) ? $cuti->sisa_cuti : ''); ?></td>
                <?php if($i == 0): ?>
                    <td>1. Tgl Pengajuan</td><td><?php echo e((isset($cuti) && $cuti->created_at) ? $cuti->created_at->format('d/m/Y') : date('d/m/Y')); ?></td>
                <?php elseif($i == 1): ?>
                    <td>2. Lama Cuti</td><td><span id="review_jumlah_cuti_display"><?php echo e($cuti->jumlah_cuti ?? '0'); ?></span> Hari</td>
                <?php elseif($i == 2): ?>
                    <td>3. TMT Cuti</td><td id="review_tmt_cuti_display" style="font-size: 8pt;"><?php echo e(isset($cuti) ? $cuti->tanggal_mulai : '...'); ?></td>
                <?php elseif($i == 3): ?>
                    <td colspan="2" class="bg-blue-grey font-bold">IV. ALASAN CUTI</td>
                <?php elseif($i == 4): ?>
                    <td colspan="2" rowspan="2" id="review_alasan_cuti_display" style="height: 35px;"><?php echo e($cuti->keterangan ?? '-'); ?></td>
                <?php elseif($i == 6): ?>
                    <td colspan="2" class="bg-blue-grey font-bold">V. YANG MENGAJUKAN</td>
                <?php elseif($i == 7): ?>
                    <td colspan="2" rowspan="2" class="text-center">
                        <p style="margin: 0;">Hormat Saya,</p><div style="height: 25px;"></div>
                        <p style="margin: 0; font-weight: bold; text-decoration: underline;"><?php echo e($cuti->pegawai->nama ?? (auth()->user()->pegawai->nama ?? '-')); ?></p>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    

    <!-- SECTION VI: PERTIMBANGAN ATASAN LANGSUNG -->
    <div style="height: 15px;"></div>
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr>
                <th colspan="8" class="bg-blue-grey" style="text-align: left;">VI. PERTIMBANGAN ATASAN LANGSUNG**</th>
            </tr>
            <tr class="text-center" style="font-size: 8pt;">
                <td style="width: 15%;">DISETUJUI</td>
                <td style="width: 10%;">....</td>
                <td style="width: 15%;">PERUBAHAN</td>
                <td style="width: 10%;">....</td>
                <td style="width: 15%;">DITANGGUHKAN</td>
                <td style="width: 10%;">....</td>
                <td style="width: 15%;">TIDAK DISETUJUI</td>
                <td style="width: 10%;">....</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                
                <td colspan="4" style="height: 80px; vertical-align: top; padding: 5px;">
                    <p style="margin: 0; font-size: 8pt; text-decoration: underline;">Pertimbangan/Catatan/Rekomendasi:</p>
                </td>
                
                <td colspan="4" class="text-center" style="vertical-align: bottom; padding-bottom: 10px; padding-top: 8%;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 9pt;">NAMA ATASAN LANGSUNG</p>
                    <p style="margin: 0; font-size: 8pt;">Jabatan</p>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- SECTION VII: KEPUTUSAN PEJABAT YANG BERWENANG -->
    <div class="spacer" style="height: 15px;"></div>
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr>
                <th colspan="8" class="bg-blue-grey" style="text-align: left;">VII. KEPUTUSAN PEJABAT YANG BERWENANG MEMBERIKAN CUTI**</th>
            </tr>
            <tr class="text-center" style="font-size: 8pt;">
                <td style="width: 15%;">DISETUJUI</td>
                <td style="width: 10%;">....</td>
                <td style="width: 15%;">PERUBAHAN</td>
                <td style="width: 10%;">....</td>
                <td style="width: 15%;">DITANGGUHKAN</td>
                <td style="width: 10%;">....</td>
                <td style="width: 15%;">TIDAK DISETUJUI</td>
                <td style="width: 10%;">....</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                
                <td colspan="4" style="height: 80px; vertical-align: top; padding: 5px;">
                    <p style="margin: 0; font-size: 8pt; text-decoration: underline;">Pertimbangan/Catatan/Rekomendasi:</p>
                </td>
                
                <td colspan="4" class="text-center" style="vertical-align: bottom; padding-bottom: 10px; padding-top: 8%;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 9pt;">BHIMA IRSI FALIANDRI atau ANJAS ASMARA</p>
                    <p style="margin: 0; font-size: 8pt;">Direktur Operasional atau Direktur Kepatuhan</p>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- SECTION VIII: KETENTUAN CUTI DISETUJUI -->
    <div class="spacer" style="height: 15px;"></div>
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr><th class="bg-blue-grey" style="text-align: left;">VIII. KETENTUAN CUTI DISETUJUI</th></tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px;">
                    <ol style="margin: 0; padding-left: 20px; font-size: 8.5pt; line-height: 1.5;">
                        <li>Segala pekerjaan yang menjadi tugasnya telah diselesaikan sebelum menjalankan cuti.</li>
                        <li>Yang bersangkutan wajib hadir apabila sewaktu-waktu dipanggil masuk kerja apabila dibutuhkan.</li>
                        <li>Izin tidak masuk kerja apabila hak cuti tahunannya telah habis dikenakan pengurangan penghasilan secara proposional yang dihitung secara harian.</li>
                        <li>Lembar permohonan dan persetujuan cuti sebelumnya dilampirkan kembali saat pengajuan cuti berikutnya.</li>
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- SECTION IX: KETENTUAN TAMBAHAN & VERIFIKATOR -->
    <div class="spacer" style="height: 15px;"></div>
    <table class="form-table" style="margin-top: 0;">
        <thead>
            <tr>
                <th style="width: 50%; text-align: left;" class="bg-blue-grey">IX. KETENTUAN TAMBAHAN</th>
                <th colspan="2" class="bg-blue-grey text-center">VERIFIKATOR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                
                <td style="height: 100px; font-size: 7.5pt; vertical-align: bottom; padding: 5px;">
                    <p style="margin: 0;">Catatan :</p>
                    <p style="margin: 0;">* Pilih salah satu dengan memberikan tanda centang (v).</p>
                    <p style="margin: 0;">** Pilih salah satu dengan memberikan tanda centang (v) dan alasannya (diisi oleh atasan).</p>
                </td>
                
                <td class="text-center" style="vertical-align: bottom; padding-bottom: 8px; width: 25%;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 8.5pt;">RIKA DEWI KUMALASARI</p>
                    <p style="margin: 0; font-size: 8pt;">Kepala SKK MR</p>
                </td>
                
                <td class="text-center" style="vertical-align: bottom; padding-bottom: 8px; width: 25%;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 8.5pt;">AKHIRIANTO SOEDEWO</p>
                    <p style="margin: 0; font-size: 8pt;">Human Resources Officer</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/partials/letter_content.blade.php ENDPATH**/ ?>