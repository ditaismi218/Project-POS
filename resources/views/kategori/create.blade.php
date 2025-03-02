@extends('layouts.layout')

@section('content')
<div class="page-header">
    <h3 class="page-title"> Tambah Kategori Produk </h3>
    <nav aria-label="breadcrumb">
        <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nama_kategori" class="form-label">Nama Kategori Produk</label>
                <input type="text" class="form-control" name="nama_kategori" required>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
@endsection
