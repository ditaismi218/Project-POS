@extends('layouts.layout')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Penjualan</h2>

    <a href="{{ route('penjualan.create') }}" class="btn btn-primary mb-3">Tambah Penjualan</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Faktur</th>
                <th>Pelanggan</th>
                <th>Total Bayar</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penjualan as $key => $p)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $p->no_faktur }}</td>
                    <td>{{ $p->member->nama ?? 'Umum' }}</td>
                    <td>Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($p->status) }}</td>
                    <td>{{ $p->created_at->format('d-m-Y H:i') }}</td>
                    <td>
                        <a href="{{ route('penjualan.show', $penjualan->first()->id ?? 0) }}" class="btn btn-info">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada transaksi penjualan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
