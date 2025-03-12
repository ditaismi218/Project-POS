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
                            <div class="col-md-3 mb-2">
                                <label for="produk_id" class="form-label">Produk</label>
                                <div class="input-group">
                                    <input type="text" class="form-control produk_nama" readonly>
                                    <input type="hidden" name="produk_id[]" class="produk_id">
                                    <button type="button" class="btn btn-primary pilih-produk-btn" data-bs-toggle="modal"
                                        data-bs-target="#produkModal">Pilih</button>
                                </div>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="qty" class="form-label">Qty</label>
                                <input type="number" class="form-control qty" name="qty[]" min="1" required>
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
                                <label for="harga_satuan" class="form-label">Harga Beli Satuan</label>
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

    <!-- Modal Pilih Produk -->
    <div class="modal fade" id="produkModal" tabindex="-1" aria-labelledby="produkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="produkModalLabel">Pilih Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" id="searchProduk" placeholder="Cari produk...">
                    <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="produkTable">
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->nama_barang }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary pilih-produk"
                                                data-id="{{ $product->id }}"
                                                data-nama="{{ $product->nama_barang }}">Pilih</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                function validateQty(input) {
                    input.addEventListener('input', function() {
                        if (parseInt(this.value) < 1 || isNaN(this.value)) {
                            this.value = 1;
                        }
                    });
                }

                function formatRupiah(input) {
                    input.addEventListener('input', function(e) {
                        let rawValue = e.target.value.replace(/[^0-9]/g, '');
                        e.target.value = new Intl.NumberFormat('id-ID').format(rawValue);
                        e.target.nextElementSibling.value = rawValue;
                    });
                }
                document.querySelectorAll('.harga-jual').forEach(formatRupiah);
                document.querySelectorAll('.harga-satuan').forEach(formatRupiah);
                document.querySelectorAll('.qty').forEach(validateQty);

                document.getElementById('addProduct').addEventListener('click', function() {
                    let firstRow = document.querySelector('.product-row');
                    let requiredFields = firstRow.querySelectorAll('input[required], select[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('is-invalid'); // Tambah border merah jika kosong
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });

                    if (!isValid) {
                        alert('Harap isi semua data produk pertama sebelum menambahkan produk baru.');
                        return;
                    }

                    let productRow = firstRow.cloneNode(true);
                    productRow.querySelectorAll('input, select').forEach(input => input.value = "");

                    let qtyInput = productRow.querySelector('.qty');
                    validateQty(qtyInput);

                    let hargaJual = productRow.querySelector('.harga-jual');
                    let hargaSatuan = productRow.querySelector('.harga-satuan');
                    formatRupiah(hargaJual);
                    formatRupiah(hargaSatuan);

                    productRow.querySelector('.remove-product').addEventListener('click', function() {
                        productRow.remove();
                    });

                    document.getElementById('product-container').appendChild(productRow);
                });

                document.querySelectorAll('input, select').forEach(field => {
                    field.addEventListener('input', function() {
                        if (this.value.trim()) {
                            this.classList.remove('is-invalid');
                        }
                    });
                });

                document.getElementById('produkTable').addEventListener('click', function(e) {
                    if (e.target.classList.contains('pilih-produk')) {
                        let activeRow = document.querySelector('.product-row:last-child');
                        activeRow.querySelector('.produk_nama').value = e.target.getAttribute('data-nama');
                        activeRow.querySelector('.produk_id').value = e.target.getAttribute('data-id');
                        bootstrap.Modal.getInstance(document.getElementById('produkModal')).hide();
                    }
                });
            });
        </script>
    @endpush
@endsection
