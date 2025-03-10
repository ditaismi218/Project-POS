@extends('layouts.layout')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="text-center fw-bold">INVOICE</h4>
    <hr>

    <p><strong>No Faktur:</strong> {{ $penjualan->no_faktur }}</p>
    <p><strong>Tanggal:</strong> {{ $penjualan->created_at->format('d-m-Y') }}</p>
    <p><strong>Pelanggan:</strong> {{ $penjualan->member->nama ?? 'Pelanggan Lain' }}</p>

    <h6 class="fw-bold">Detail Pembelian:</h6>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th class="text-center">Jumlah</th>
                <th class="text-end">Harga Satuan</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualan->detailPenjualan as $detail)
                <tr>
                    <td>{{ $detail->produk->nama_barang }}</td>
                    <td class="text-center">{{ $detail->qty }}</td>
                    <td class="text-end">Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total Bayar:</strong> Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</p>
    <p><strong>Jumlah Bayar:</strong> Rp {{ number_format(request()->session()->get('jumlah_bayar'), 0, ',', '.') }}</p>
    <p><strong>Kembalian:</strong> Rp {{ number_format(request()->session()->get('jumlah_bayar') - $penjualan->total_bayar, 0, ',', '.') }}</p>
    <p><strong>Metode Pembayaran:</strong> {{ request()->session()->get('metode_pembayaran') }}</p>

    <button onclick="window.print()" class="btn btn-primary mt-3">
        <i class="fas fa-print"></i> Cetak Invoice
    </button>
</div>
@endsection
    