<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Laporan Penjualan Produk</h2>

    <table>
        <thead>
            <tr>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Total Terjual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $item)
                <tr>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->nama_kategori }}</td>
                    <td>{{ $item->total_terjual }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        <strong>Total Kuantitas Terjual: </strong> {{ $totalQty }}
    </div>
</body>
</html>
