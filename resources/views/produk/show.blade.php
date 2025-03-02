@extends('layouts.layout')

@section('content')
<div class="content">
    <div class="page-header">
        <h4 class="page-title">Detail Produk</h4>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Informasi Produk</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Kode Barang</th>
                    <td>{{ $product->kode_barang }}</td>
                </tr>
                <tr>
                    <th>Nama Barang</th>
                    <td>{{ $product->nama_barang }}</td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td>{{ $product->kategori->nama_kategori ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Harga Beli</th>
                    <td>Rp {{ number_format($product->harga_beli, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Harga Jual</th>
                    <td>Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Satuan</th>
                    <td>{{ $product->satuan }}</td>
                </tr>
            </table>
            <a href="{{ route('produk.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
@endsection
