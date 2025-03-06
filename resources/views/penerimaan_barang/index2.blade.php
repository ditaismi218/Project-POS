@extends('layouts.layout')
@section('title', 'Penerimaan Barang')

@section('content')

    <div class="page-body">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <button class="btn btn-primary" id="createProductButton">
                <i class="fa fa-plus"></i> Tambah Penerimaan Barang
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <h5 class="card-header text-md-start text-center">Tabel Penerimaan Barang</h5>
            <div class="card-datatable">
                <table class="dt-scrollableTable table table-bordered">
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
                                <td>{{ $item->supplier->nama }}</td>
                                <td>{{ $item->produk->nama_barang }}</td>
                                <td>{{ $item->kode_penerimaan }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d-m-Y') }}</td>
                                <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>Rp {{ number_format($item->harga_total, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') }}</td>
                                <td>
                                    <button class="btn btn-warning edit-button" 
                                        data-id="{{ $item->id }}"
                                        data-supplier="{{ $item->supplier_id }}" 
                                        data-produk="{{ $item->produk_id }}"
                                        data-kode="{{ $item->kode_penerimaan }}" 
                                        data-tgl="{{ $item->tgl_masuk }}"
                                        data-jual="{{ $item->harga_jual}}"
                                        data-satuan="{{ $item->harga_satuan}}"
                                        data-qty="{{ $item->qty}}"
                                        data-total="{{ $item->harga_total}}"
                                        data-expired="{{ $item->expired_date}}"
                                        data-bs-toggle="modal" data-bs-target="#productModal">
                                        <i class="bx bx-edit"></i>
                                    </button>
                
                                    <button class="btn btn-danger delete-button" data-id="{{ $item->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('penerimaan_barang.destroy', $item->id) }}" method="POST" style="display:none;">
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

    <!-- Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 500px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Penerimaan Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST" enctype="multipart/form-data" data-action="{{ route('penerimaan_barang.store') }}">
                        @csrf
                        <input type="hidden" id="method" name="_method" value="POST">

                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select class="form-control" id="supplier_id" name="supplier_id" required>
                                <option value="" disabled selected>Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="produk_id" class="form-label">Produk</label>
                            <select class="form-control" id="produk_id" name="produk_id" required>
                                <option value="" disabled selected>Pilih Produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="kode_penerimaan" class="form-label">Kode Penerimaan</label>
                            <input type="text" class="form-control" id="kode_penerimaan" name="kode_penerimaan" required>
                        </div>

                        <div class="mb-3">
                            <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
                            <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" required>
                        </div>

                        <div class="mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual</label>
                            <input type="number" class="form-control" id="harga_jual" name="harga_jual" required>
                        </div>

                        <div class="mb-3">
                            <label for="harga_satuan" class="form-label">Harga Satuan</label>
                            <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" required>
                        </div>

                        <div class="mb-3">
                            <label for="qty" class="form-label">Qty</label>
                            <input type="number" class="form-control" id="qty" name="qty" required>
                        </div>

                        <div class="mb-3">
                            <label for="harga_total" class="form-label">Harga Total</label>
                            <input type="number" class="form-control" id="harga_total" name="harga_total" required>
                        </div>

                        <div class="mb-3">
                            <label for="expired_date" class="form-label">Expired Date</label>
                            <input type="date" class="form-control" id="expired_date" name="expired_date" required>
                        </div>

                        <button type="submit" class="btn btn-success" id="submitButton">Simpan</button>
                    </form>
                </div>
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

        // Event untuk Tambah Produk
        document.getElementById('createProductButton').addEventListener('click', function() {
            document.getElementById('modalTitle').innerText = "Tambah Penerimaan Barang";
            document.getElementById('productForm').setAttribute('action', "{{ route('penerimaan_barang.store') }}");
            document.getElementById('method').value = "POST";
            document.getElementById('submitButton').innerText = "Simpan";

            document.getElementById('supplier_id').value = "";
            document.getElementById('produk_id').value = "";
            document.getElementById('kode_penerimaan').value = "";
            document.getElementById('tgl_masuk').value = "";
            document.getElementById('harga_jual').value = "";
            document.getElementById('harga_satuan').value = "";
            document.getElementById('qty').value = "";
            document.getElementById('harga_total').value = "";
            document.getElementById('expired_date').value = "";

            productModal.show();
        });

        // Event untuk Edit Produk
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function() {
                let id = this.getAttribute('data-id');
                let supplier = this.getAttribute('data-supplier');
                let produk = this.getAttribute('data-produk');
                let kode = this.getAttribute('data-kode');
                let tgl = this.getAttribute('data-tgl');
                let jual = this.getAttribute('data-jual');
                let satuan = this.getAttribute('data-satuan');
                let qty = this.getAttribute('data-qty');
                let total = this.getAttribute('data-total');
                let expired = this.getAttribute('data-expired');

                document.getElementById('modalTitle').innerText = "Edit Penerimaan Barang";
                document.getElementById('productForm').setAttribute('action', `{{ url('penerimaan_barang') }}/${id}`);
                document.getElementById('method').value = "PUT";
                document.getElementById('submitButton').innerText = "Update";

                document.getElementById('supplier_id').value = supplier;
                document.getElementById('produk_id').value = produk;
                document.getElementById('kode_penerimaan').value = kode;
                document.getElementById('tgl_masuk').value = tgl;
                document.getElementById('harga_jual').value = jual;
                document.getElementById('harga_satuan').value = satuan;
                document.getElementById('qty').value = qty;
                document.getElementById('harga_total').value = total;
                document.getElementById('expired_date').value = expired;
                classList.remove('d-none');

                productModal.show();
            });
        });

        

        // Event untuk Hapus Produk
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                let id = this.getAttribute('data-id');
                let nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: `Penerimaan "${produk_id}" akan dihapus secara permanen!`,
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
