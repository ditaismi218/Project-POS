<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; }
        table td, table th { padding: 8px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h2>Invoice - No Faktur: {{ $penjualan->no_faktur }}</h2>
        <p><strong>Pelanggan:</strong> {{ $penjualan->member->nama ?? 'Pelanggan Lain' }}</p>
        <p><strong>Total Bayar:</strong> Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</p>

        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan->detailPenjualan as $detail)
                    <tr>
                        <td>{{ $detail->produk->nama_barang }}</td>
                        <td>{{ $detail->qty }}</td>
                        <td>Rp {{ number_format($detail->produk->harga_jual, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($detail->qty * $detail->produk->harga_jual, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Total: Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</h3>
    </div>
</body>
</html>
