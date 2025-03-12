@extends('layouts.layout')
@section('title', 'Tambah Penerimaan Barang')

@section('content')
    <div class="page-body">
        <div class="card">
            <div class="card-header">
                <h5>Tambah Penerimaan Barang</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('penerimaan_barang.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select class="form-control" id="supplier_id" name="supplier_id" required>
                                <option value="" disabled selected>Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
                            <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" required>
                        </div>
                    </div>

                    <hr>

                    <h5>Detail Produk</h5>
                    <div id="product-container">
                        <div class="row product-row">
                            <div class="col-md-3 mb-3">
                                <label for="produk_id" class="form-label">Produk</label>
                                <select class="form-control" name="produk_id[]" required>
                                    <option value="" disabled selected>Pilih Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->nama_barang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="qty" class="form-label">Qty</label>
                                <input type="number" class="form-control" name="qty[]" required>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="harga_jual" class="form-label">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control harga-jual" required>
                                    <input type="hidden" name="harga_jual[]" class="harga-jual-hidden">
                                </div>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="harga_satuan" class="form-label">Harga Satuan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control harga-satuan" required>
                                    <input type="hidden" name="harga_satuan[]" class="harga-satuan-hidden">
                                </div>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="expired_date" class="form-label">Expired Date</label>
                                <input type="date" class="form-control" name="expired_date[]" required>
                            </div>

                            <div class="col-md-1 mb-3 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-product">X</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addProduct" class="btn btn-secondary">Tambah Produk</button>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="{{ route('penerimaan_barang.index') }}" class="btn btn-secondary ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                function formatRupiah(value) {
                    let number = parseInt(value) || 0;
                    return new Intl.NumberFormat('id-ID', {
                        useGrouping: true, // Gunakan pemisah ribuan
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(number); // Tidak pakai style: 'currency' agar tanpa Rp
                }

                function formatInputOnType(input) {
                    input.addEventListener('input', function(e) {
                        let rawValue = e.target.value.replace(/[^0-9]/g, ''); // Hanya angka
                        e.target.value = formatRupiah(rawValue);
                        e.target.nextElementSibling.value = rawValue; // Simpan nilai asli
                    });
                }

                document.querySelectorAll('.harga-jual').forEach(formatInputOnType);
                document.querySelectorAll('.harga-satuan').forEach(formatInputOnType);

                document.getElementById('addProduct').addEventListener('click', function() {
                    let productRow = document.querySelector('.product-row').cloneNode(true);

                    productRow.querySelectorAll('input, select').forEach(input => {
                        if (input.type !== 'hidden') input.value = "";
                    });

                    productRow.querySelector('.remove-product').addEventListener('click', function() {
                        productRow.remove();
                    });

                    productRow.querySelectorAll('.harga-jual').forEach(input => formatInputOnType(input));
                    productRow.querySelectorAll('.harga-satuan').forEach(input => formatInputOnType(input));

                    document.getElementById('product-container').appendChild(productRow);
                });

                document.querySelectorAll('.remove-product').forEach(button => {
                    button.addEventListener('click', function() {
                        button.closest('.product-row').remove();
                    });
                });
            });
        </script>
    @endpush
@endsection
