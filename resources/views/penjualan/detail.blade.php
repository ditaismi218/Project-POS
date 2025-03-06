@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>Detail Penjualan</h2>
    <hr>

    <div class="card">
        <div class="card-body">
            <h4>No. Faktur: {{ $penjualan->no_faktur }}</h4>
            <p><strong>Tanggal Faktur:</strong> {{ $penjualan->tgl_faktur }}</p>
            <p><strong>Pelanggan:</strong> {{ $penjualan->member->nama ?? 'Umum' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($penjualan->status) }}</p>
            <p><strong>Total Bayar:</strong> Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</p>
        </div>
    </div>

    <h4 class="mt-4">Detail Produk</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualan->detailPenjualan as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->produk->nama_barang }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('penjualan.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
