@extends('layouts.layout')

@section('content')
<div class="page-header">
    <h3 class="page-title"> Edit Kategori Produk </h3>
    <nav aria-label="breadcrumb">
        <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="nama_kategori" class="form-label">Nama Kategori Produk</label>
                <input type="text" class="form-control" name="nama_kategori" value="{{ $kategori->nama_kategori }}" required>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</div>
@endsection
