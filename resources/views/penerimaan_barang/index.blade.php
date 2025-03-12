@extends('layouts.layout')
@section('title', 'Penerimaan Barang')

@section('content')

    <div class="page-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('penerimaan_barang.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Tambah Penerimaan Barang
            </a>                
        </div>        

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <h5 class="card-header text-md-start text-center">Tabel Penerimaan Barang</h5>
            <div class="card-datatable">
                <table class="table table-striped dt-scrollableTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Supplier</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penerimaan as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->supplier->nama_supplier }}</td>
                                <td>{{ $item->produk->nama_barang }}</td>
                                <td>{{ $item->total_qty }}</td>
                                <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('penerimaan_barang.show', $item->produk_id) }}" class="btn btn-warning">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>                                                             
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });
    </script>
@endpush