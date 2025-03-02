@extends('layouts.layout')
@section('title', 'Manajemen Produk')

@section('content')

    <div class="page-body">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <button class="btn btn-primary" id="createProductButton">
                <i class="fa fa-plus"></i> Tambah Supplier
            </button>

        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <h5 class="card-header pb-0 text-md-start text-center">Tabel Supplier</h5>
            <div class="card-datatable text-nowrap">
                {{-- <table class=" table table-bordered table-responsive"> --}}
                <table class="dt-scrollableTable table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Nama Supplier</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($supplier as $item)
                            <tr>
                                <td>{{ $item->nama_supplier }}</td>
                                <td>{{ $item->telepon }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>
                                    <button class="btn btn-warning edit-button" data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama_supplier }}" data-telepon="{{ $item->telepon }}"
                                        data-email="{{ $item->email }}" data-alamat="{{ $item->alamat }}"
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <button class="btn btn-danger delete-button" data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama_supplier }}" data-telepon="{{ $item->telepon }}"
                                        data-email="{{ $item->email }}" data-alamat="{{ $item->alamat }}">
                                        <i class="bx bx-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id }}"
                                        action="{{ route('supplier.destroy', $item->id) }}" method="POST"
                                        style="display:none;">
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

    @endsection

    {{-- modal --}}
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST">
                        @csrf
                        <input type="hidden" id="method" name="_method" value="POST">

                        <div class="mb-3">
                            <label for="nama_supplier" class="form-label">Nama Supplier</label>
                            <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" required>
                        </div>

                        <div class="mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="number" class="form-control" id="telepon" name="telepon" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat" required>
                        </div>

                        <button type="submit" class="btn btn-success" id="submitButton">Simpan</button>
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
                    document.getElementById('modalTitle').innerText = "Tambah Supplier";
                    document.getElementById('productForm').setAttribute('action',
                        "{{ route('supplier.store') }}");
                    document.getElementById('method').value = "POST";
                    document.getElementById('submitButton').innerText = "Simpan";

                    // Reset Form Input
                    document.getElementById('nama_supplier').value = "";
                    document.getElementById('telepon').value = "";
                    document.getElementById('email').value = "";
                    document.getElementById('alamat').value = "";

                    productModal.show();
                });

                // Event untuk Edit Supplier
                document.querySelectorAll('.edit-button').forEach(button => {
                    button.addEventListener('click', function() {
                        let id = this.getAttribute('data-id');
                        let nama = this.getAttribute('data-nama');
                        let telepon = this.getAttribute('data-telepon');
                        let email = this.getAttribute('data-email');
                        let alamat = this.getAttribute('data-alamat');

                        document.getElementById('modalTitle').innerText = "Edit Supplier";
                        document.getElementById('productForm').setAttribute('action',
                            `{{ url('supplier') }}/${id}`);
                        document.getElementById('method').value = "PUT";
                        document.getElementById('submitButton').innerText = "Update";

                        document.getElementById('nama_supplier').value = nama;
                        document.getElementById('telepon').value = telepon;
                        document.getElementById('email').value = email;
                        document.getElementById('alamat').value = alamat;

                        productModal.show();
                    });
                });

                // Event untuk Hapus Supplier
                document.querySelectorAll('.delete-button').forEach(button => {
                    button.addEventListener('click', function() {
                        let id = this.getAttribute('data-id');
                        let nama = this.getAttribute('data-nama');

                        Swal.fire({
                            title: "Apakah Anda yakin?",
                            text: `Supplier "${nama}" akan dihapus secara permanen!`,
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
