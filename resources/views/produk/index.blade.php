@extends('layouts.layout')
@section('title', 'Manajemen Produk')

@section('content')

    <div class="page-body">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <button class="btn btn-primary" id="createProductButton">
                <i class="fa fa-plus"></i> Tambah Produk
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <h5 class="card-header text-md-start text-center">Tabel Produk</h5>
            <div class="card-datatable">
                <table class="dt-scrollableTable table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Gambar</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products  as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->kode_barang }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td>{{ $item->kategori->nama_kategori }}</td>
                                <td>
                                    @if ($item->gambar)
                                        <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama_barang }}" width="50">
                                    @else
                                        <span class="text-muted">Tidak ada gambar</span>
                                    @endif
                                </td>
                                <td>{{ $item->satuan }}</td>
                                <td>
                                    <button class="btn btn-warning edit-button" 
                                        data-id="{{ $item->id }}"
                                        data-kode="{{ $item->kode_barang }}" 
                                        data-nama="{{ $item->nama_barang }}"
                                        data-kategori="{{ $item->kategori_id }}" 
                                        data-gambar="{{ $item->gambar }}"
                                        data-satuan="{{ $item->satuan }}"
                                        data-bs-toggle="modal" data-bs-target="#productModal">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <button class="btn btn-danger delete-button" data-id="{{ $item->id }}" data-nama="{{ $item->nama_barang }}">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id }}" action="{{ route('produk.destroy', $item->id) }}" method="POST" style="display:none;">
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
                    <h5 class="modal-title" id="modalTitle">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="method" name="_method" value="POST">

                        <div class="mb-3">
                            <label for="kode_barang" class="form-label">Kode Barang</label>
                            <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                        </div>

                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                        </div>

                        <div class="mb-3">
                            <label for="kategori_id" class="form-label">Kategori</label>
                            <select class="form-control" id="kategori_id" name="kategori_id" required>
                                @foreach ($categories as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            <img id="gambarPreview" src="" alt="Preview" width="100" class="mt-2 d-none">
                        </div>

                        <div class="mb-3">
                            <label for="satuan">Satuan</label>
                            <select class="form-control" id="satuan" name="satuan" required>
                                <option value="pcs">Pcs</option>
                                <option value="pack">Pack</option>
                                <option value="box">Box</option>
                                <option value="lusin">Lusin</option>
                                <option value="gram">Gram</option>
                                <option value="kg">Kg</option>
                                <option value="ml">Ml</option>
                                <option value="liter">Liter</option>
                                <option value="meter">Meter</option>
                                <option value="botol">Botol</option>
                                <option value="kaleng">Kaleng</option>
                                <option value="sachet">Sachet</option>
                                <option value="strip">Strip</option>
                            </select>
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
                    // {
            // targets: -1,
            // title: "Actions",
            // searchable: !1,
            // className: "d-flex align-items-center",
            // orderable: !1,
            // render: function (e, t, a, s) {
            //     return '<div class="d-inline-block"><a href="javascript:;" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded icon-base"></i></a><div class="dropdown-menu dropdown-menu-end m-0"><a href="javascript:;" class="dropdown-item">Details</a><a href="javascript:;" class="dropdown-item">Archive</a><div class="dropdown-divider"></div><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a></div></div><a href="javascript:;" class="item-edit text-body"><i class="bx bxs-edit icon-base"></i></a>';
            // },
        // },
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

        // Event untuk Tambah Produk
        document.getElementById('createProductButton').addEventListener('click', function() {
            document.getElementById('modalTitle').innerText = "Tambah Produk";
            document.getElementById('productForm').setAttribute('action', "{{ route('produk.store') }}");
            document.getElementById('method').value = "POST";
            document.getElementById('submitButton').innerText = "Simpan";

            document.getElementById('kode_barang').value = "";
            document.getElementById('nama_barang').value = "";
            document.getElementById('kategori_id').value = "";
            document.getElementById('satuan').value = "";
            document.getElementById('gambar').value = "";
            document.getElementById('gambarPreview').classList.add('d-none');

            productModal.show();
        });

        // Event untuk Edit Produk
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function() {
                let id = this.getAttribute('data-id');
                let kode = this.getAttribute('data-kode');
                let nama = this.getAttribute('data-nama');
                let kategori = this.getAttribute('data-kategori');
                let gambar = this.getAttribute('data-gambar');
                let satuan = this.getAttribute('data-satuan');

                document.getElementById('modalTitle').innerText = "Edit Produk";
                document.getElementById('productForm').setAttribute('action', `{{ url('produk') }}/${id}`);
                document.getElementById('method').value = "PUT";
                document.getElementById('submitButton').innerText = "Update";

                document.getElementById('kode_barang').value = kode;
                document.getElementById('nama_barang').value = nama;
                document.getElementById('kategori_id').value = kategori;
                document.getElementById('satuan').value = satuan;
                document.getElementById('gambarPreview').src = `{{ asset('storage/') }}/${gambar}`;
                document.getElementById('gambarPreview').classList.remove('d-none');

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
                    text: `Produk "${nama}" akan dihapus secara permanen!`,
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
