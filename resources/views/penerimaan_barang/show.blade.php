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
                            <th>Aksi</th>
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
                                    Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                </td>
                                <td class="text-primary">
                                    Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="text-center fw-bold">{{ $item->qty }}</td>
                                <td class="text-danger">
                                    Rp {{ number_format($item->harga_total, 0, ',', '.') }}
                                </td>
                                <td>
                                    {{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') : '-' }}
                                </td>
                                <td>
                                    <div class="d-flex gap-1 align-items-center">
                                        <!-- Tombol Edit -->
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $item->id }}">
                                            <i class="bx bx-edit-alt"></i> Edit
                                        </button>
                                
                                        <!-- Tombol Soft Delete -->
                                        <form action="{{ route('penerimaan_barang.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bx bx-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                
                            </tr>
                        @endforeach
                    </tbody>
                    
                    @foreach ($penerimaan as $item)
                        <!-- Modal Edit -->
                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1"
                            aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form method="POST" action="{{ route('penerimaan_barang.update', $item->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $item->id }}">Edit Penerimaan
                                                Barang</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Input for Date -->
                                            <div class="mb-3">
                                                <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
                                                <input type="date" name="tgl_masuk" id="tgl_masuk" class="form-control"
                                                    value="{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('Y-m-d') }}"
                                                    required>
                                            </div>
                                            <!-- Input for Expired Date -->
                                            <div class="mb-3">
                                                <label for="expired_date" class="form-label">Expired Date</label>
                                                <input type="date" name="expired_date" id="expired_date"
                                                    class="form-control"
                                                    value="{{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('Y-m-d') : '' }}">
                                            </div>
                                            <!-- Input for Harga Jual -->
                                            <div class="mb-3">
                                                <label for="harga_jual" class="form-label">Harga Jual</label>
                                                <input type="number" step="0.01" name="harga_jual" id="harga_jual"
                                                    class="form-control" value="{{ $item->harga_jual }}" required>
                                            </div>
                                            <!-- Input for Harga Satuan -->
                                            <div class="mb-3">
                                                <label for="harga_satuan" class="form-label">Harga Satuan</label>
                                                <input type="number" step="0.01" name="harga_satuan" id="harga_satuan"
                                                    class="form-control" value="{{ $item->harga_satuan }}" required>
                                            </div>
                                            <!-- Input for Qty -->
                                            <div class="mb-3">
                                                <label for="qty" class="form-label">Qty</label>
                                                <input type="number" name="qty" id="qty" class="form-control"
                                                    value="{{ $item->qty }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                    @endforeach
                    
                </table>
            </div>
        </div>
    </div>
    {{-- </div> --}}
@endsection
