@extends('layouts.layout')
@section('title', 'Penerimaan Barang')

@section('content')

    <div class="page-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" id="createProductButton">
                <i class="fa fa-plus"></i> Tambah Penerimaan Barang
            </button>
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

<!-- Modal Tambah -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Penerimaan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('penerimaan_barang.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="method" name="_method" value="POST">
                    <input type="number" name="user_id" value="{{ Auth::user()->id }}" hidden>

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
                            <label for="produk_id" class="form-label">Produk</label>
                            <select class="form-control" id="produk_id" name="produk_id" required>
                                <option value="" disabled selected>Pilih Produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
                            <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="qty" class="form-label">Qty</label>
                            <input type="number" class="form-control" id="qty" name="qty" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="harga_jual" required>
                                <input type="hidden" id="harga_jual-hidden" name="harga_jual">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="harga_satuan" class="form-label">Harga Satuan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="harga_satuan" required>
                                <input type="hidden" id="harga_satuan-hidden" name="harga_satuan">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expired_date" class="form-label">Expired Date</label>
                            <input type="date" class="form-control" id="expired_date" name="expired_date" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-success" id="submitButton">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function(e) {
            let a = document.querySelector(".dt-scrollableTable");
            a &&
                new DataTable(a, {
                    columnDefs: [{
                        targets: -2,
                        render: function(e, t, a, s) {
                            var a = a.status,
                                r = {
                                    1: {
                                        title: "Current",
                                        class: "bg-label-primary",
                                    },
                                    2: {
                                        title: "Professional",
                                        class: "bg-label-success",
                                    },
                                    3: {
                                        title: "Rejected",
                                        class: "bg-label-danger",
                                    },
                                    4: {
                                        title: "Resigned",
                                        class: "bg-label-warning",
                                    },
                                    5: {
                                        title: "Applied",
                                        class: "bg-label-info"
                                    },
                                };
                            return void 0 === r[a] ?
                                e :
                                `
                        <span class="badge ${r[a].class}">
                            ${r[a].title}
                        </span>
                        `;
                        },
                    }, ],
                    scrollX: !0,
                    layout: {
                        topStart: {
                            rowClass: "row mx-3 my-0 justify-content-between",
                            features: [{
                                pageLength: {
                                    menu: [7, 10, 25, 50, 100],
                                    text: "Show_MENU_entries",
                                },
                            }, ],
                        },
                        topEnd: {
                            search: {
                                placeholder: ""
                            }
                        },
                        bottomStart: {
                            rowClass: "row mx-3 justify-content-between",
                            features: ["info"],
                        },
                        bottomEnd: {
                            paging: {
                                firstLast: !1
                            }
                        },
                    },
                    language: {
                        paginate: {
                            next: '<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-sm"></i>',
                            previous: '<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-sm"></i>',
                        },
                    },
                    initComplete: function(e, t) {
                        a.querySelector("tbody tr:first-child").classList.add(
                            "border-top-0"
                        );
                    },
                });

        });


        document.addEventListener('DOMContentLoaded', function() {
            let productModal = new bootstrap.Modal(document.getElementById('productModal'));

            document.getElementById('createProductButton').addEventListener('click', function() {
                let modal = new bootstrap.Modal(document.getElementById('productModal'));
                modal.show();
            });

            function formatRupiah(value) {
                let number = parseInt(value); 
                return new Intl.NumberFormat('id-ID').format(number); 
            }

            function formatInputOnType(inputId, hiddenId) {
                document.getElementById(inputId).addEventListener('input', function(e) {
                    let rawValue = e.target.value.replace(/[^0-9]/g, ''); 

                    if (rawValue === "") {
                        e.target.value = "";
                        document.getElementById(hiddenId).value = "";
                        return;
                    }

                    let formattedValue = formatRupiah(rawValue);
                    e.target.value = formattedValue;

                    document.getElementById(hiddenId).value = rawValue;
                });
            }

            formatInputOnType('harga_jual', 'harga_jual-hidden');
            formatInputOnType('harga_satuan', 'harga_satuan-hidden');
        });
    </script>
@endpush