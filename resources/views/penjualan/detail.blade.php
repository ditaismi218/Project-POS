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
            <p><strong>Subtotal:</strong> Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</p>
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
            @php
                // Mengelompokkan produk berdasarkan nama
                $groupedProduk = $penjualan->detailPenjualan->groupBy('produk.nama_barang');
            @endphp

            @foreach ($groupedProduk as $nama_produk => $details)
                @php
                    $total_qty = $details->sum('qty');
                    $total_subtotal = $details->sum('sub_total');
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $nama_produk }}</td>
                    <td>{{ $total_qty }}</td>
                    <td>Rp {{ number_format($total_subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('penjualan.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
