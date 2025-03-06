@extends('layouts.layout')

@section('content')
<div class="container text-center">
    <h2 class="mb-4">Pembayaran Berhasil!</h2>

    <div class="card p-4">
        <h4>Invoice</h4>
        <hr>
        <p><strong>Nomor Faktur:</strong> {{ $penjualan->no_faktur }}</p>
        <p><strong>Tanggal:</strong> {{ $penjualan->tgl_faktur }}</p>
        <p><strong>Total Bayar:</strong> Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($penjualan->status) }}</p>

        <h5 class="mt-3">Detail Pembayaran</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jumlah Bayar</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan->pembayaran as $pembayaran)
                <tr>
                    <td>{{ $pembayaran->created_at->format('d M Y H:i') }}</td>
                    <td>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($pembayaran->metode_pembayaran) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button onclick="window.print()" class="btn btn-success mt-3">Cetak Invoice</button>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
</div>
@endsection
