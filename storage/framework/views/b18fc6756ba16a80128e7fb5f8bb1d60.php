<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengajuan</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: sans-serif; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; }
        .status-disetujui { color: green; font-weight: bold; }
        .status-ditolak { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PENGAJUAN PEGAWAI</h2>
        <p>Periode: <?php echo e($request->start_date ?? '-'); ?> s/d <?php echo e($request->end_date ?? '-'); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Pegawai (Nomor Urut Pegawai)</th>
                <th>Divisi</th>
                <th>Jenis</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $dataPengajuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($key + 1); ?></td>
                <td><?php echo e(date('d/m/Y', strtotime($row->tanggal))); ?></td>
                <td><?php echo e($row->nama); ?> (<?php echo e($row->nup); ?>)</td>
                <td><?php echo e($row->nama_divisi); ?></td>
                <td><?php echo e(ucfirst($row->jenis)); ?></td>
                <td class="<?php echo e($row->status == 'disetujui' ? 'status-disetujui' : 'status-ditolak'); ?>">
                    <?php echo e(ucfirst($row->status)); ?>

                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\simpegbkb\resources\views/laporan/cetak_pdf.blade.php ENDPATH**/ ?>