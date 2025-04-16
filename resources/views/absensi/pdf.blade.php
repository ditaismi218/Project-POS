<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi</title>
</head>
<body>
    <h1>Laporan Absensi</h1>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Tanggal Masuk</th>
                <th>Waktu Masuk</th>
                <th>Status</th>
                <th>Waktu Selesai Kerja</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($absensi as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ $item->tanggal_masuk }}</td>
                    <td>{{ $item->waktu_masuk }}</td>
                    <td>{{ ucfirst($item->status_masuk) }}</td>
                    <td>
                        @if ($item->waktu_selesai_kerja)
                            {{ $item->waktu_selesai_kerja }}
                        @else
                            Belum Selesai
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
