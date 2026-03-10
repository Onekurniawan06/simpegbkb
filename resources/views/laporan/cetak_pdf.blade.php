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
        <p>Periode: {{ $request->start_date ?? '-' }} s/d {{ $request->end_date ?? '-' }}</p>
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
            @foreach($dataPengajuan as $key => $row)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
                <td>{{ $row->nama }} ({{ $row->nup }})</td>
                <td>{{ $row->nama_divisi }}</td>
                <td>{{ ucfirst($row->jenis) }}</td>
                <td class="{{ $row->status == 'disetujui' ? 'status-disetujui' : 'status-ditolak' }}">
                    {{ ucfirst($row->status) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
