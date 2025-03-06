@extends('layouts.layout')

@section('content')
<div class="container">
    <h2 class="mb-4">History Pembayaran</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Penjualan</th>
                <th>Subtotal</th>
                <th>Jumlah Bayar</th>
                <th>Kembalian</th>
                <th>Metode Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembayaran as $item)
            <tr>
                <td>{{ $item->penjualan->no_faktur }}</td>
                <td>Rp {{ number_format($item->penjualan->total_bayar, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->kembalian, 0, ',', '.') }}</td>
                <td>{{ ucfirst($item->metode_pembayaran) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
