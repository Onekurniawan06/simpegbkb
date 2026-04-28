

<?php if(isset($is_pdf) && $is_pdf): ?>
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
<?php endif; ?>

<div class="content-wrap">

    <!-- Header Section -->
    <?php if(isset($is_pdf) && $is_pdf): ?>
        <table class="header-table">
            <tr>
                <td style="width: 70%;">
                    <?php
                        $path = public_path('images/logobkb.png');
                        $base64 = file_exists($path) ? 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path)) : '';
                    ?>
                    <?php if($base64): ?>
                        <img src="<?php echo e($base64); ?>" alt="Logo Perusahaan" class="logo-img">
                    <?php endif; ?>
                </td>
                <td class="perumda-name">Perumda BPR Bank Kota Bogor</td>
            </tr>
        </table>
    <?php else: ?>
        <div class="flex justify-between items-center mb-3">
            <div class="flex items-center">
                <?php
                    $path = public_path('images/logobkb.png');
                    $base64 = file_exists($path) ? 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path)) : '';
                ?>
                <?php if($base64): ?>
                    <img src="<?php echo e($base64); ?>" alt="Logo Perusahaan" class="h-11 w-auto">
                <?php endif; ?>
            </div>
            <p class="text-sm font-bold">Perumda BPR Bank Kota Bogor</p>
        </div>
    <?php endif; ?>

    
    <div style="background-color: #0000FF; height: 20px; width: 100%; margin-bottom: 10pt;"></div>

    <!-- Date Section -->
    <div class="<?php echo e((isset($is_pdf) && $is_pdf) ? 'date-section' : 'text-right text-sm mb-3'); ?>" style="<?php echo e((isset($is_pdf) && $is_pdf) ? 'text-align: right;' : ''); ?>">
        <p>Bogor, <?php echo e(isset($lembur) ? \Carbon\Carbon::parse($lembur->created_at)->format('d F Y') : now()->format('d F Y')); ?></p>
    </div>

    <!-- Salutation -->
    <p class="<?php echo e((isset($is_pdf) && $is_pdf) ? '' : 'mb-4 text-sm'); ?>">Dengan hormat,</p>

    <?php if(isset($is_pdf) && $is_pdf): ?>
        
        <table class="main-detail-table mb-4">
            <tr><td class="label-column">NUP Pegawai</td><td class="data-column"><?php echo e($lembur->nomor_urut_pegawai ?? 'N/A'); ?></td></tr>
            <tr><td class="label-column">Nama Pegawai</td><td class="data-column"><?php echo e($lembur->pegawai->nama ?? 'N/A'); ?></td></tr>
            <tr><td class="label-column">Divisi</td><td class="data-column"><?php echo e($lembur->pegawai->pekerjaan?->divisi?->nama_divisi ?? 'N/A'); ?></td></tr>
            <tr><td class="label-column">Jabatan</td><td class="data-column"><?php echo e($lembur->pegawai->pekerjaan?->jabatan ?? 'N/A'); ?></td></tr>
        </table>
    <?php else: ?>
        
        <div class="ml-4">
            <div class="grid grid-cols-[160px_1fr] gap-x-4 gap-y-2 mb-4 text-sm">
                <div class="font-normal">NUP Pegawai</div>
                <div class="font-semibold" id="review_nup">
                    <?php echo e($lembur->nomor_urut_pegawai ?? (auth()->user()->nomor_urut_pegawai ?? '-')); ?>

                </div>

                <div class="font-normal">Nama Pegawai</div>
                <div class="font-semibold" id="review_nama_pegawai">
                    <?php echo e($lembur->pegawai->nama ?? (auth()->user()->nama ?? '-')); ?>

                </div>

                <div class="font-normal">Divisi</div>
                <div class="font-semibold" id="review_divisi">
                    <?php
                        $user = auth()->user();
                        $namaDivisi = $lembur->pegawai->pekerjaan->divisi->nama_divisi
                            ?? ($user->pegawai->pekerjaan->divisi->nama_divisi ?? '-');
                    ?>
                    <?php echo e($namaDivisi); ?>

                </div>

                <div class="font-normal">Jabatan</div>
                <div class="font-semibold" id="review_jabatan">
                    <?php
                        $namaJabatan = $lembur->pegawai->pekerjaan->jabatan
                            ?? ($user->pegawai->pekerjaan->jabatan ?? '-');
                    ?>
                    <?php echo e($namaJabatan); ?>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <p class="text-sm <?php echo e((isset($is_pdf) && $is_pdf) ? '' : 'mb-3'); ?>">untuk melaksanakan kerja lembur pada,</p>
    <p class="text-sm font-semibold <?php echo e((isset($is_pdf) && $is_pdf) ? '' : 'mb-3'); ?>">Tanggal Lembur : <span id="review_tanggal_lembur"><?php echo e(isset($lembur) ? \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d F Y') : '-'); ?></span></p>

    
    <table style="width: 100%; border-collapse: collapse; font-size: 10pt; text-align: center; margin-bottom: 15px;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th rowspan="2" style="border: 1px solid #d1d5db; padding: 6px; vertical-align: middle;">Uraian Tugas</th>
                <th colspan="3" style="border: 1px solid #d1d5db; padding: 6px; vertical-align: middle;">Perkiraan Waktu Lembur</th>
            </tr>
            <tr style="background-color: #f3f4f6;">
                <th style="border: 1px solid #d1d5db; padding: 6px; vertical-align: middle;">Jam Mulai</th>
                <th style="border: 1px solid #d1d5db; padding: 6px; vertical-align: middle;">Jam Selesai</th>
                <th style="border: 1px solid #d1d5db; padding: 6px; vertical-align: middle;">Total Jam</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #d1d5db; padding: 6px; text-align: left; vertical-align: top;" id="review_uraian_tugas"><?php echo e($lembur->uraian_tugas ?? '-'); ?></td>
                <td style="border: 1px solid #d1d5db; padding: 6px; vertical-align: middle;" id="review_jam_mulai_lembur"><?php echo e($lembur->jam_mulai ?? '-'); ?></td>
                <td style="border: 1px solid #d1d5db; padding: 6px; vertical-align: middle;" id="review_jam_selesai_lembur"><?php echo e($lembur->jam_selesai ?? '-'); ?></td>
                <td style="border: 1px solid #d1d5db; padding: 6px; vertical-align: middle;" id="review_total_lembur"><?php echo e($lembur->total_jam_lembur ?? '-'); ?></td>
            </tr>
        </tbody>
    </table>
    

    <!-- Justification Paragraph -->
    <p class="text-sm <?php echo e((isset($is_pdf) && $is_pdf) ? '' : 'italic text-gray-600'); ?>" style="<?php echo e((isset($is_pdf) && $is_pdf) ? 'font-size: 0.875rem;' : ''); ?>">
        Berdasarkan validasi atas kegiatan kerja lembur sebagaimana Surat Perintah Kerja Lembur (SPKL) diatas, maka kegiatan kerja lembur pegawai bisa dilaksanakan sesuai rincian waktu yang telah disetujui.
    </p>

    <!-- Realisasi Waktu Lembur Line -->
    <p class="text-sm mt-3 mb-6">
        Realisasi Waktu Kerja Lembur :
        <span class="font-semibold" id="review_realisasi_mulai"><?php echo e($lembur->jam_mulai ?? '-'); ?></span> s/d
        <span class="font-semibold" id="review_realisasi_selesai"><?php echo e($lembur->jam_selesai ?? '-'); ?></span>
        Total Jam Kerja Lembur :
        <span class="font-semibold" id="review_realisasi_total"><?php echo e($lembur->total_jam_lembur ?? '-'); ?></span>
    </p>

    <!-- Approvals/Signatures Section -->
    <?php if(isset($lembur) && isset($lembur->logPersetujuanLembur) && count($lembur->logPersetujuanLembur) > 0): ?>
        <?php
            $hanyaPengajuanAwal = $lembur->logPersetujuanLembur->every(function($log) {
                return $log->tahap_persetujuan === 'Pengajuan Awal' || $log->tahap_persetujuan === 'Pegawai';
            });
        ?>

        <?php if($hanyaPengajuanAwal): ?>
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-6">
                <p class="text-sm text-blue-700">
                    <strong>Informasi:</strong> Pengajuan lembur telah dibuat dan sedang menunggu proses verifikasi awal.
                </p>
            </div>
        <?php else: ?>
            <p class="text-sm mt-6 mb-2">Dengan beberapa persetujuan yaitu,</p>
            <?php if(isset($is_pdf) && $is_pdf): ?>
                
                <table style="width: 520px; border-collapse: collapse; margin-top: 5px;">
                    <?php $approvalCounter = 1; ?>
                    <?php $__currentLoopData = $lembur->logPersetujuanLembur->sortBy('id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($log->tahap_persetujuan !== 'Pengajuan Awal' && $log->tahap_persetujuan !== 'Selesai'): ?>
                            <?php
                                $namaPenyetuju = $log->penyetuju->nama ?? 'Atasan';
                                $jabatanPenyetuju = $log->penyetuju->pekerjaan->jabatan ?? $log->tahap_persetujuan;

                                if (str_contains($jabatanPenyetuju, 'Human Resources Officer')) {
                                    $jabatanPenyetuju = 'Human Resources Officer';
                                }
                            ?>
                            <tr>
                                <td style="font-size: 10pt; color: #1f2937; padding: 3px 0; vertical-align: top; width: 420px;">
                                    <?php echo e($approvalCounter++); ?>. <?php echo e($namaPenyetuju); ?> (<?php echo e(trim($jabatanPenyetuju)); ?>)
                                </td>
                                <td style="font-size: 10pt; font-weight: bold; color: #059669; text-align: right; text-transform: uppercase; padding: 3px 0; width: 100px; vertical-align: top;">
                                    <?php echo e($log->status_persetujuan ?? $log->status_pengajuan); ?>

                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </table>
            <?php else: ?>
                
                <div class="ml-4">
                    <table style="width: 100%; max-width: 500px; border-collapse: collapse; margin-top: 8px;">
                        <?php $approvalCounter = 1; ?>
                        <?php $__currentLoopData = $lembur->logPersetujuanLembur->sortBy('id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($log->tahap_persetujuan !== 'Pengajuan Awal' && $log->tahap_persetujuan !== 'Selesai'): ?>
                                <?php
                                    $namaPenyetuju = $log->penyetuju->nama ?? 'Atasan';
                                    $jabatanPenyetuju = $log->penyetuju->pekerjaan->jabatan ?? $log->tahap_persetujuan;

                                    if (str_contains($jabatanPenyetuju, 'Human Resources Officer')) {
                                        $jabatanPenyetuju = 'Human Resources Officer';
                                    }
                                ?>
                                <tr>
                                    <td style="font-size: 13px; color: #1f2937; padding: 6px 0; vertical-align: top;">
                                        <?php echo e($approvalCounter++); ?>. <?php echo e($namaPenyetuju); ?> (<?php echo e($jabatanPenyetuju); ?>)
                                    </td>
                                    <td style="font-size: 13px; font-weight: bold; color: #059669; text-align: right; text-transform: uppercase; padding: 6px 0; width: 100px; vertical-align: top;">
                                        <?php echo e($log->status_persetujuan ?? $log->status_pengajuan); ?>

                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-sm mt-6 text-gray-500 italic">Data persetujuan akan muncul setelah pengajuan diproses.</p>
    <?php endif; ?>

    <!-- Paragraf Penutup -->
    <p class="text-sm mt-6 mb-8">Demikian surat perintah kerja lembur ini dibuat untuk dilaksanakan sebagaimana mestinya.</p>

    
    <div class="text-left">
        <p class="text-sm mb-4">Hormat saya,</p>

        <!-- Placeholder QR Code -->
        <div class="flex justify-start">
            <div class="w-24 h-24 bg-gray-300 my-4"></div>
        </div>

        <?php
            $user = auth()->user();
            $namaTtd = $lembur->pegawai->nama ?? ($user->pegawai->nama ?? $user->name);
            $jabatanTtd = $lembur->pegawai->pekerjaan->jabatan ?? ($user->pegawai->pekerjaan->jabatan ?? 'Pegawai');
        ?>

        <p class="text-sm font-semibold mt-4" id="review_footer_nama"><?php echo e($namaTtd); ?></p>
        <p class="text-sm font-semibold" id="review_footer_jabatan"><?php echo e($jabatanTtd); ?></p>
    </div>

    
    <?php if(!isset($is_pdf) || !$is_pdf): ?>
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
    <?php endif; ?>

</div>


<?php if(isset($is_pdf) && $is_pdf): ?>
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
<?php endif; ?>

<?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/partials/lembur_letter_content.blade.php ENDPATH**/ ?>