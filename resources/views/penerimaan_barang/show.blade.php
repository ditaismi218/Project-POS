@extends('layouts.layout')

@section('content')
{{-- <div class="container mt-4"> --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bx bx-detail"></i> Detail Penerimaan Barang</h5>
            <a href="{{ route('penerimaan_barang.index') }}" class="btn btn-light btn-sm">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover dt-scrollableTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Supplier</th>
                            <th>Produk</th>
                            <th>Kode Penerimaan</th>
                            <th>Tanggal Masuk</th>
                            <th>Harga Jual</th>
                            <th>Harga Satuan</th>
                            <th>Qty</th>
                            <th>Total Harga</th>
                            <th>Expired Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penerimaan as $index => $item)
                            <tr class="align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-info text-white">
                                        {{ $item->supplier->nama_supplier }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $item->produk->nama_barang }}</strong>
                                </td>
                                <td>{{ $item->kode_penerimaan }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d-m-Y') }}</td>
                                <td class="text-success">
                                    Rp {{ number_format($item->harga_jual, 2, ',', '.') }}
                                </td>
                                <td class="text-primary">
                                    Rp {{ number_format($item->harga_satuan, 2, ',', '.') }}
                                </td>
                                <td class="text-center fw-bold">{{ $item->qty }}</td>
                                <td class="text-danger">
                                    Rp {{ number_format($item->harga_total, 2, ',', '.') }}
                                </td>
                                <td>
                                    {{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{{-- </div> --}}
@endsection
