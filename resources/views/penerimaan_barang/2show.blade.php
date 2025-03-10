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
            <!-- <div class="card-datatable overflow-auto"> -->
                <table class="table table-striped dt-scrollableTable">
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
                            <th>Harga Total</th>
                            <th>Expired Date</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penerimaan as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->supplier->nama_supplier }}</td>
                                <td>{{ $item->produk->nama_barang }}</td>
                                <td>{{ $item->kode_penerimaan }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d-m-Y') }}</td>
                                <td>Rp {{ number_format($item->harga_jual, 2, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->harga_satuan, 2, ',', '.') }}</td>                                
                                <td>{{ $item->qty }}</td>
                                <td>Rp {{ number_format($item->harga_total, 2, ',', '.') }}</td>
                                <td>{{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') : '-' }}</td>
                                <td>
                                    <button class="btn btn-warning"
                                        onclick="test(
                                            '{{ $item->id }}', 
                                            '{{ $item->supplier->id }}', 
                                            '{{ $item->supplier->nama_supplier }}', 
                                            '{{ $item->produk->id }}', 
                                            '{{ $item->produk->nama_barang }}', 
                                            '{{ $item->tgl_masuk }}', 
                                            '{{ $item->harga_jual }}', 
                                            '{{ $item->harga_satuan }}', 
                                            '{{ $item->qty }}', 
                                            '{{ $item->expired_date ?? '' }}'
                                        )"
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                        <i class="bx bx-edit"></i>
                                    </button>


                                    <button class="btn btn-danger delete-button" 
                                        data-id="{{ $item->id }}"
                                        data-nama="{{ $item->kode_penerimaan }}">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id }}" 
                                        action="{{ route('voucher.destroy', $item->id) }}" 
                                        method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
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
                    <input type="number" name="user_id" value="{{Auth::user()->id}}" hidden>

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

                        {{-- <div class="col-md-6 mb-3">
                            <label for="kode_penerimaan" class="form-label">Kode Penerimaan</label>
                            <input type="text" class="form-control" id="kode_penerimaan" name="kode_penerimaan" required>
                        </div> --}}

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

                        {{-- <div class="col-md-6 mb-3">
                            <label for="harga_total" class="form-label">Harga Total</label>
                            <input type="number" class="form-control" id="harga_total" name="harga_total" required>
                        </div> --}}

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

     
<!-- Modal Edit Produk -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Penerimaan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('penerimaan_barang.update') }}">
                    @csrf
                    <input type="hidden" id="id-edit" name="id">
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select class="form-control" id="supplier_id-edit" name="supplier_id" required>
                                <option id="supplier_id-content" disabled selected>Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="produk_id" class="form-label">Produk</label>
                            <select class="form-control" id="produk_id-edit" name="produk_id" required>
                                <option id="produk_id-content" disabled selected>Pilih Produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
                            <input type="date" class="form-control" id="tgl_masuk-edit" name="tgl_masuk" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="qty" class="form-label">Qty</label>
                            <input type="number" class="form-control" id="qty-edit" name="qty" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="harga_jual-edit" name="harga_jual" required>
                                {{-- <input type="text" id="harga_jual-hidden" name="harga_jual"> --}}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="harga_satuan" class="form-label">Harga Satuan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="harga_satuan-edit" name="harga_satuan" required>
                                {{-- <input type="hidden" id="harga_satuan-hidden" name="harga_satuan"> --}}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expired_date" class="form-label">Expired Date</label>
                            <input type="date" class="form-control" id="expired_date-edit" name="expired_date" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Update</button>
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

            function test(id, supplier_id, supplier, produk_id, produk, tgl_masuk, harga_jual, harga_satuan, qty, expired_date) {
                document.getElementById('id-edit').value = id;
                document.getElementById('supplier_id-edit').value = supplier_id;
                document.getElementById('supplier_id-content').innerText = supplier;
                document.getElementById('produk_id-edit').value = produk_id;
                document.getElementById('produk_id-content').innerText = produk;
                document.getElementById('tgl_masuk-edit').value = tgl_masuk;
                document.getElementById('harga_jual-edit').value = harga_jual;
                document.getElementById('harga_satuan-edit').value = harga_satuan;

                // **Set harga jual dan harga satuan dengan format Rupiah saat membuka modal**
                // formatInputOnLoad('harga_jual-edit', 'harga_jual-hidden', harga_jual);
                // formatInputOnLoad('harga_satuan-edit', 'harga_satuan-hidden', harga_satuan);

                document.getElementById('qty-edit').value = qty;
                document.getElementById('expired_date-edit').value = expired_date;
            }

            // Fungsi untuk memformat nilai saat modal dibuka
            function formatInputOnLoad(inputId, hiddenId, value) {
                if (value) {
                    let formatted = formatRupiah(value);
                    document.getElementById(inputId).value = formatted;
                    document.getElementById(hiddenId).value = value;
                }
            }

            // Fungsi untuk memformat angka ke Rupiah dengan ,00 di belakangnya
            function formatRupiah(value) {
                let number = parseFloat(value);
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(number).replace("Rp", "").trim(); // Hapus "Rp" agar tidak double
            }

            // Fungsi untuk memformat input saat user mengetik
            function formatInputOnType(inputId, hiddenId) {
                document.getElementById(inputId).addEventListener('input', function (e) {
                    let rawValue = e.target.value.replace(/[^0-9]/g, ''); // Hanya angka

                    if (rawValue === "") {
                        e.target.value = "";
                        document.getElementById(hiddenId).value = "";
                        return;
                    }

                    let number = parseFloat(rawValue) / 100; // Biar 100 jadi 1,00
                    let formattedValue = formatRupiah(number);
                    e.target.value = formattedValue;

                    // Simpan nilai asli tanpa format (dengan dua desimal)
                    document.getElementById(hiddenId).value = number.toFixed(2);
                });
            }

            // Tambahkan event listener untuk kedua input harga
            formatInputOnType('harga_jual-edit', 'harga_jual-hidden');
            formatInputOnType('harga_satuan-edit', 'harga_satuan-hidden');
        </script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- JavaScript -->

        <script>
            document.addEventListener("DOMContentLoaded", function (e) {
                let a = document.querySelector(".dt-scrollableTable");
                a &&
                    new DataTable(a, {
                        columnDefs: [
                            {
                                targets: -2,
                                render: function (e, t, a, s) {
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
                                            5: { title: "Applied", class: "bg-label-info" },
                                        };
                                    return void 0 === r[a]
                                        ? e
                                        : `
                        <span class="badge ${r[a].class}">
                            ${r[a].title}
                        </span>
                        `;
                                },
                            },
                        ],
                        // scrollY: "300px",
                        scrollX: !0,
                        layout: {
                            topStart: {
                                rowClass: "row mx-3 my-0 justify-content-between",
                                features: [
                                    {
                                        pageLength: {
                                            menu: [7, 10, 25, 50, 100],
                                            text: "Show_MENU_entries",
                                        },
                                    },
                                ],
                            },
                            topEnd: { search: { placeholder: "" } },
                            bottomStart: {
                                rowClass: "row mx-3 justify-content-between",
                                features: ["info"],
                            },
                            bottomEnd: { paging: { firstLast: !1 } },
                        },
                        language: {
                            paginate: {
                                next: '<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-sm"></i>',
                                previous:
                                    '<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-sm"></i>',
                            },
                        },
                        initComplete: function (e, t) {
                            a.querySelector("tbody tr:first-child").classList.add(
                                "border-top-0"
                            );
                        },
                    });
                
            });


            document.addEventListener('DOMContentLoaded', function() {
                let productModal = new bootstrap.Modal(document.getElementById('productModal'));

                // Event untuk Tambah Supplier
                document.getElementById('createProductButton').addEventListener('click', function() {
                    let modal = new bootstrap.Modal(document.getElementById('productModal'));
                    modal.show();
                });

                function formatRupiah(value) {
                    let number = parseFloat(value);
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(number).replace("Rp", "").trim(); // Hapus "Rp" agar tidak double
                }

                // Fungsi untuk memformat input saat user mengetik
                function formatInputOnType(inputId, hiddenId) {
                    document.getElementById(inputId).addEventListener('input', function (e) {
                        let rawValue = e.target.value.replace(/[^0-9]/g, ''); // Hanya angka

                        if (rawValue === "") {
                            e.target.value = "";
                            document.getElementById(hiddenId).value = "";
                            return;
                        }

                        let number = parseFloat(rawValue) / 100; // Biar 100 jadi 1,00
                        let formattedValue = formatRupiah(number);
                        e.target.value = formattedValue;

                        // Simpan nilai asli tanpa format (dengan dua desimal)
                        document.getElementById(hiddenId).value = number.toFixed(2);
                    });
                }

                // Tambahkan event listener untuk modal tambah
                formatInputOnType('harga_jual', 'harga_jual-hidden');
                formatInputOnType('harga_satuan', 'harga_satuan-hidden');
                
                // Event untuk Hapus User
                document.querySelectorAll('.delete-button').forEach(button => {
                    button.addEventListener('click', function() {
                        let id = this.getAttribute('data-id');
                        let nama = this.getAttribute('data-nama');

                        Swal.fire({
                            title: "Apakah Anda yakin?",
                            text: `User "${nama}" akan dihapus secara permanen!`,
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#d33",
                            cancelButtonColor: "#3085d6",
                            confirmButtonText: "Ya, hapus!",
                            cancelButtonText: "Batal"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`delete-form-${id}`).submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush

    